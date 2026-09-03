<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Models\PreparationStage;
use App\Models\PreparationTask;
use App\Models\PreparationTaskFile;
use App\Models\Prodi;
use App\Models\ProdiTaskProgress;
use App\Models\Standard;
use App\Models\OnboardingProgress;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class PreparationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $standardCodes = $user->roles->pluck('name');

        $prodi = $request->filled('prodi')
            ? Prodi::where('is_active', true)->findOrFail($request->integer('prodi'))
            : null;

        $standards = Standard::query()
            ->whereIn('code', $standardCodes)
            ->withCount(['preparationStages as stage_count' => fn ($q) => $q->where('is_active', true)])
            ->with([
                'preparationStages' => fn ($q) => $q->where('is_active', true),
                'preparationStages.tasks.prodis',
                'preparationStages.tasks.progress' => fn ($q) => $prodi
                    ? $q->where('prodi_id', $prodi->id)
                    : $q->whereRaw('1 = 0'),
            ])
            ->orderByRaw('LENGTH(code), code')
            ->get()
            ->each(function (Standard $standard) use ($prodi) {
                $tasks = $standard->preparationStages
                    ->flatMap->tasks;

                if ($prodi) {
                    $tasks = $tasks->filter(function (PreparationTask $task) use ($prodi) {
                        $assignedProdiIds = $task->prodis->modelKeys();

                        return in_array($prodi->id, $assignedProdiIds, true)
                            || (int) $task->prodi_id === $prodi->id
                            || ($assignedProdiIds === [] && $task->prodi_id === null);
                    });
                }

                $standard->setAttribute('total_tasks', $tasks->count());
                $standard->setAttribute('done_tasks', $prodi
                    ? $tasks->filter(fn (PreparationTask $task) => (bool) $task->progress->first()?->is_done)->count()
                    : 0);
                $standard->setAttribute('readiness_percent', $prodi && $tasks->isNotEmpty()
                    ? (int) round($standard->done_tasks / $standard->total_tasks * 100)
                    : 0);
            });

        $prodis = Prodi::where('is_active', true)->orderBy('name')->get();

        $preparationsOnboarding = OnboardingProgress::firstOrCreate(
            ['user_id' => $user->id, 'onboarding_key' => 'internal_preparations', 'version' => 1],
            ['current_step' => 0, 'status' => 'started', 'started_at' => now(), 'last_seen_at' => now()]
        );

        return view('internal.preparations.landing', compact('standards', 'prodi', 'prodis', 'preparationsOnboarding'));
    }

    public function show(Request $request, Standard $standard)
    {
        $this->authorizeStandard($request, $standard);

        $prodi = null;
        if ($request->filled('prodi')) {
            $prodi = Prodi::findOrFail((int) $request->input('prodi'));
        }

        $stages = $standard->preparationStages()
            ->where(function ($q) use ($prodi) {
                $q->whereNull('prodi_id');
                if ($prodi) {
                    $q->orWhere('prodi_id', $prodi->id);
                }
            })
            ->with([
                'creator',
                'prodi',
                'tasks' => fn ($q) => $q->where(function ($scope) use ($prodi) {
                    if (! $prodi) {
                        return;
                    }
                    $scope->whereHas('prodis', fn ($assigned) => $assigned->whereKey($prodi->id))
                        ->orWhere('prodi_id', $prodi->id)
                        ->orWhere(fn ($global) => $global->whereNull('prodi_id')->whereDoesntHave('prodis'));
                })->orderBy('sort_order'),
                'tasks.prodi',
                'tasks.prodis',
                'tasks.creator',
                'tasks.progress' => fn ($q) => $prodi ? $q->where('prodi_id', $prodi->id) : $q,
                'tasks.files' => fn ($q) => ($prodi ? $q->where('prodi_id', $prodi->id) : $q)->with('uploader'),
            ])
            ->orderBy('sort_order')
            ->get();

        $activeStage = $stages->first();
        if ($request->filled('stage')) {
            $activeStage = $stages->firstWhere('id', $request->integer('stage')) ?? $activeStage;
        }

        $activeStage?->tasks->each(function (PreparationTask $task) use ($prodi) {
            $progress = $prodi ? $task->progress->first() : null;
            $task->setAttribute('is_done', (bool) $progress?->is_done);
            $task->setAttribute('done_at', $progress?->done_at);
        });

        $prodis = Prodi::where('is_active', true)->orderBy('name')->get();
        $preparationsDetailOnboarding = OnboardingProgress::firstOrCreate(
            ['user_id' => $request->user()->id, 'onboarding_key' => 'internal_preparations_detail', 'version' => 1],
            ['current_step' => 0, 'status' => 'started', 'started_at' => now(), 'last_seen_at' => now()]
        );

        return view('admin.standard-preparations.index', [
            'standard' => $standard,
            'stages' => $stages,
            'activeStage' => $activeStage,
            'prodi' => $prodi,
            'prodis' => $prodis,
            'isEvidenceManager' => false,
            'preparationsDetailOnboarding' => $preparationsDetailOnboarding,
        ]);
    }

    public function upload(Request $request, PreparationTask $task)
    {
        $this->authorizeTask($request, $task);

        $request->validate([
            'file' => ['required', 'file', 'max:10240', 'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,zip,rar'],
            'prodi_id' => ['required', 'exists:prodis,id'],
        ]);

        $file = $request->file('file');
        $prodiId = $request->integer('prodi_id');
        $this->assertTaskAppliesToProdi($task, $prodiId);

        $path = $file->store("preparations/task-{$task->id}/prodi-{$prodiId}", 'public');

        $task->files()->create([
            'prodi_id' => $prodiId,
            'uploaded_by' => $request->user()->id,
            'file_path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
        ]);

        $this->markDoneIfNeeded($task, $prodiId, $request->user()->id);

        return back()->with('success', 'Dokumen berhasil diupload.');
    }

    public function storeStage(Request $request, Standard $standard): RedirectResponse
    {
        $this->authorizeStandard($request, $standard);
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'prodi_id' => ['nullable', 'exists:prodis,id'],
        ]);
        $stage = $standard->preparationStages()->create([
            ...$data,
            'sort_order' => ((int) $standard->preparationStages()->max('sort_order')) + 1,
            'is_active' => true,
            'created_by' => $request->user()->id,
        ]);
        return redirect()->route('internal.preparations.show', [$standard, 'stage' => $stage->id, 'prodi' => $data['prodi_id'] ?? null])->with('success', 'Tahap evidence berhasil ditambahkan.');
    }

    public function updateStage(Request $request, Standard $standard, PreparationStage $stage): RedirectResponse
    {
        $this->assertStageBelongsToStandard($stage, $standard);
        $this->authorizeStandard($request, $standard);
        $this->authorizeOwned($request, $stage->created_by);
        $stage->update($request->validate(['title' => ['required', 'string', 'max:255'], 'description' => ['nullable', 'string']]));
        return back()->with('success', 'Tahap evidence berhasil diperbarui.');
    }

    public function destroyStage(Request $request, Standard $standard, PreparationStage $stage): RedirectResponse
    {
        $this->assertStageBelongsToStandard($stage, $standard);
        $this->authorizeStandard($request, $standard);
        $this->authorizeOwned($request, $stage->created_by);
        abort_if($stage->tasks()->where('created_by', '!=', $request->user()->id)->exists(), 422, 'Tahap tidak dapat dihapus karena berisi task milik pengguna lain.');
        $stage->delete();
        return redirect()->route('internal.preparations.show', $standard)->with('success', 'Tahap evidence berhasil dihapus.');
    }

    public function storeTask(Request $request, Standard $standard, PreparationStage $stage): RedirectResponse
    {
        $this->assertStageBelongsToStandard($stage, $standard);
        $this->authorizeStandard($request, $standard);
        $data = $this->validateTask($request);
        $prodiIds = $request->boolean('all_prodis')
            ? collect()
            : collect($data['prodi_ids'] ?? [])->map(fn ($id) => (int) $id)->unique()->values();
        $task = $stage->tasks()->create([
            'title' => $data['title'], 'description' => $data['description'] ?? null,
            'link' => $data['link'] ?? null, 'category' => $data['category'],
            'is_required' => $request->boolean('is_required'), 'sort_order' => ((int) $stage->tasks()->max('sort_order')) + 1,
            'created_by' => $request->user()->id,
        ]);
        $task->prodis()->sync($prodiIds);
        return back()->with('success', 'Task evidence berhasil ditambahkan.');
    }

    public function updateTask(Request $request, Standard $standard, PreparationStage $stage, PreparationTask $task): RedirectResponse
    {
        $this->assertTaskHierarchy($standard, $stage, $task);
        $this->authorizeStandard($request, $standard);
        $this->authorizeOwned($request, $task->created_by);
        $data = $this->validateTask($request);
        $task->update(['title'=>$data['title'], 'description'=>$data['description'] ?? null, 'link'=>$data['link'] ?? null, 'category'=>$data['category'], 'is_required'=>$request->boolean('is_required')]);
        $task->prodis()->sync($request->boolean('all_prodis') ? [] : ($data['prodi_ids'] ?? []));
        return back()->with('success', 'Task evidence berhasil diperbarui.');
    }

    public function destroyTask(Request $request, Standard $standard, PreparationStage $stage, PreparationTask $task): RedirectResponse
    {
        $this->assertTaskHierarchy($standard, $stage, $task);
        $this->authorizeStandard($request, $standard);
        $this->authorizeOwned($request, $task->created_by);
        $task->delete();
        return back()->with('success', 'Task evidence berhasil dihapus.');
    }

    public function storeLink(Request $request, PreparationTask $task)
    {
        $this->authorizeTask($request, $task);

        $request->validate([
            'link_url' => ['required', 'url', 'max:2048'],
            'link_name' => ['nullable', 'string', 'max:255'],
            'prodi_id' => ['required', 'exists:prodis,id'],
        ]);

        $prodiId = $request->integer('prodi_id');
        $this->assertTaskAppliesToProdi($task, $prodiId);

        $task->files()->create([
            'prodi_id' => $prodiId,
            'uploaded_by' => $request->user()->id,
            'file_path' => $request->link_url,
            'original_name' => $request->link_name ?: $request->link_url,
            'mime_type' => null,
            'size' => null,
            'link_url' => $request->link_url,
        ]);

        $this->markDoneIfNeeded($task, $prodiId, $request->user()->id);

        return back()->with('success', 'Link berhasil disimpan.');
    }

    public function toggle(Request $request, PreparationTask $task)
    {
        $this->authorizeTask($request, $task);

        $request->validate([
            'prodi_id' => ['required', 'exists:prodis,id'],
            'done' => ['required', 'boolean'],
        ]);

        $done = $request->boolean('done');
        $prodiId = $request->integer('prodi_id');
        $this->assertTaskAppliesToProdi($task, $prodiId);

        ProdiTaskProgress::updateOrCreate(
            ['task_id' => $task->id, 'prodi_id' => $prodiId],
            [
                'is_done' => $done,
                'done_at' => $done ? now() : null,
                'done_by' => $done ? $request->user()->id : null,
            ]
        );

        return back()->with('success', $done ? 'Ditandai selesai.' : 'Dibuka kembali.');
    }

    public function destroyFile(Request $request, PreparationTaskFile $file)
    {
        $user = $request->user();
        $this->authorizeTask($request, $file->task);
        if ($file->uploaded_by !== $user->id && ! $user->hasRole('admin')) {
            abort(403, 'Anda hanya dapat menghapus file yang Anda upload sendiri.');
        }

        if ($file->link_url === null) {
            Storage::disk('public')->delete($file->file_path);
        }
        $file->delete();

        return back()->with('success', 'File dihapus.');
    }

    private function markDoneIfNeeded(PreparationTask $task, int $prodiId, int $userId): void
    {
        $progress = ProdiTaskProgress::firstOrNew([
            'task_id' => $task->id,
            'prodi_id' => $prodiId,
        ]);

        if (! $progress->is_done) {
            $progress->is_done = true;
            $progress->done_at = now();
            $progress->done_by = $userId;
            $progress->save();
        }
    }

    private function assertTaskAppliesToProdi(PreparationTask $task, int $prodiId): void
    {
        $hasAssignments = $task->prodis()->exists();
        $isAssigned = $task->prodis()->whereKey($prodiId)->exists()
            || (int) $task->prodi_id === $prodiId;

        $isGlobal = ! $hasAssignments && $task->prodi_id === null;
        abort_if(! $isGlobal && ! $isAssigned, 403, 'Dokumen tidak ditujukan untuk prodi ini.');
    }

    private function authorizeStandard(Request $request, Standard $standard): void
    {
        $user = $request->user();
        abort_unless(
            $user->hasRole('auditor') || $user->hasRole($standard->code),
            403,
            'Standar ini tidak ditugaskan kepada akun Anda.'
        );
    }

    private function authorizeTask(Request $request, PreparationTask $task): void
    {
        $task->loadMissing('stage.standard');
        abort_unless($task->stage?->standard, 404);
        $this->authorizeStandard($request, $task->stage->standard);
    }

    private function authorizeOwned(Request $request, ?int $ownerId): void
    {
        abort_unless($request->user()->hasRole('admin') || ($ownerId && $ownerId === $request->user()->id), 403, 'Anda hanya dapat mengubah evidence milik sendiri.');
    }

    private function assertStageBelongsToStandard(PreparationStage $stage, Standard $standard): void
    {
        abort_unless((int) $stage->standard_id === (int) $standard->id, 404);
    }

    private function assertTaskHierarchy(Standard $standard, PreparationStage $stage, PreparationTask $task): void
    {
        $this->assertStageBelongsToStandard($stage, $standard);
        abort_unless((int) $task->stage_id === (int) $stage->id, 404);
    }

    private function validateTask(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'], 'description' => ['nullable', 'string'],
            'link' => ['nullable', 'url', 'max:2048'], 'category' => ['required', 'in:'.implode(',', PreparationTask::CATEGORIES)],
            'is_required' => ['nullable', 'boolean'], 'prodi_ids' => ['nullable', 'array'],
            'prodi_ids.*' => ['integer', 'distinct', 'exists:prodis,id'],
            'all_prodis' => ['nullable', 'boolean'],
        ]);
    }
}
