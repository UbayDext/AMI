<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Models\PreparationStage;
use App\Models\PreparationTask;
use App\Models\Prodi;
use App\Models\Standard;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class StandardPreparationViewController extends Controller
{
    public function index(Request $request)
    {
        $prodi = null;
        if ($request->filled('prodi')) {
            $prodi = Prodi::findOrFail((int) $request->input('prodi'));
        }

        $standards = Standard::query();

        /** @var User|null $user */
        $user = auth()->user();
        if ($user && $user->hasRole('auditee') && ! $user->hasRole('admin')) {
            $userStandardRoles = $user->roles()->whereIn('name', Standard::pluck('code'))->pluck('name')->toArray();
            $standards->whereIn('code', $userStandardRoles);
        }

        $standards = $standards->orderByRaw('LENGTH(code), code')
            ->with([
                'preparationStages.tasks.progress' => fn ($q) => $prodi
                    ? $q->where('prodi_id', $prodi->id)
                    : $q,
            ])
            ->get()
            ->map(function ($s) use ($prodi) {
                $allTasks = $s->preparationStages->flatMap(fn ($stage) => $stage->tasks);

                if ($prodi) {
                    $allProgress = $allTasks->flatMap(fn ($t) => $t->progress)->where('is_applicable', true);
                    $s->total_tasks = $allProgress->count();
                    $s->done_tasks = $allProgress->where('is_done', true)->count();
                } else {
                    $s->total_tasks = $allTasks->count();
                    $s->done_tasks = 0;
                }

                $s->percent = $s->total_tasks > 0
                    ? (int) round($s->done_tasks / $s->total_tasks * 100)
                    : 0;

                return $s;
            });

        $prodis = Prodi::where('is_active', true)->orderBy('name')->get();

        return view('internal.standards.index', compact('standards', 'prodi', 'prodis'));
    }

    public function show(Standard $standard, Request $request)
    {
        $prodi = null;
        if ($request->filled('prodi')) {
            $prodi = Prodi::findOrFail((int) $request->input('prodi'));
        }

        $stages = $standard->preparationStages()
            ->where('is_active', true)
            ->where(function ($q) use ($prodi) {
                $q->whereNull('prodi_id');
                if ($prodi) {
                    $q->orWhere('prodi_id', $prodi->id);
                }
            })
            ->with([
                'creator',
                'prodi',
                'tasks' => fn ($q) => $q->where(function ($q2) use ($prodi) {
                    if (! $prodi) {
                        return;
                    }
                    $q2->whereHas('prodis', fn ($assigned) => $assigned->whereKey($prodi->id))
                        ->orWhere('prodi_id', $prodi->id)
                        ->orWhere(fn ($global) => $global->whereNull('prodi_id')->whereDoesntHave('prodis'));
                })->orderBy('sort_order'),
                'tasks.creator',
                'tasks.prodi',
                'tasks.prodis',
                'tasks.progress' => fn ($q) => $prodi ? $q->where('prodi_id', $prodi->id) : $q,
                'tasks.files' => fn ($q) => $prodi
                    ? $q->where('prodi_id', $prodi->id)->with('uploader')
                    : $q->with('uploader'),
            ])
            ->orderBy('sort_order')
            ->get();

        $activeStage = $stages->first();
        if ($request->filled('stage')) {
            $activeStage = $stages->firstWhere('id', $request->integer('stage')) ?? $activeStage;
        }

        $tasks = $activeStage?->tasks?->sortBy('sort_order') ?? collect();

        [$stageTotal, $stageDone] = $this->calcProgress($activeStage?->tasks ?? collect(), $prodi);
        $stagePercent = $stageTotal > 0 ? (int) round($stageDone / $stageTotal * 100) : 0;

        $allTasks = $stages->flatMap(fn ($s) => $s->tasks);
        [$total, $done] = $this->calcProgress($allTasks, $prodi);
        $percent = $total > 0 ? (int) round($done / $total * 100) : 0;

        /** @var User|null $user */
        $user = auth()->user();
        if ($user && $user->hasRole('auditee') && ! $user->hasRole('admin')) {
            $userStandardRoles = $user->roles()->whereIn('name', Standard::pluck('code'))->pluck('name')->toArray();
            if (! in_array($standard->code, $userStandardRoles)) {
                abort(403, 'Anda tidak memiliki akses ke standar ini.');
            }
        }

        $prodis = Prodi::where('is_active', true)->orderBy('name')->get();

        return view('internal.standards.show', compact(
            'standard',
            'stages',
            'activeStage',
            'tasks',
            'total',
            'done',
            'percent',
            'prodi',
            'prodis',
            'stageTotal',
            'stageDone',
            'stagePercent',
        ));
    }

    public function storeStage(Request $request, Standard $standard): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'prodi_id' => ['nullable', 'exists:prodis,id'],
        ]);

        $standard->preparationStages()->create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'prodi_id' => $data['prodi_id'] ?? null,
            'sort_order' => $standard->preparationStages()->max('sort_order') + 1,
            'is_active' => true,
            'created_by' => $request->user()->id,
        ]);

        $prodiParam = $data['prodi_id'] ?? null;

        return redirect()
            ->route('internal.standard-preparations.show', [$standard, 'prodi' => $prodiParam])
            ->with('success', 'Tahap persiapan berhasil ditambahkan.');
    }

    public function updateStage(Request $request, Standard $standard, PreparationStage $stage): RedirectResponse
    {
        if ($stage->created_by !== $request->user()->id && ! $request->user()->hasRole('admin')) {
            abort(403);
        }

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $stage->update($data);

        return redirect()
            ->route('internal.standard-preparations.show', [$standard, 'stage' => $stage->id])
            ->with('success', 'Tahap persiapan berhasil diperbarui.');
    }

    public function destroyStage(Request $request, Standard $standard, PreparationStage $stage): RedirectResponse
    {
        if ($stage->created_by !== $request->user()->id && ! $request->user()->hasRole('admin')) {
            abort(403);
        }

        $stage->delete();

        return redirect()
            ->route('internal.standard-preparations.show', $standard)
            ->with('success', 'Tahap persiapan dihapus.');
    }

    public function storeTask(Request $request, Standard $standard, PreparationStage $stage): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'link' => ['required', 'string'],
            'category' => ['required', 'in:'.implode(',', PreparationTask::CATEGORIES)],
            'prodi_ids' => ['nullable', 'array'],
            'prodi_ids.*' => ['integer', 'distinct', 'exists:prodis,id'],
        ]);

        $prodiIds = collect($data['prodi_ids'] ?? [])->map(fn ($id) => (int) $id)->unique()->values();
        if ($stage->prodi_id && ($prodiIds->count() !== 1 || $prodiIds->first() !== (int) $stage->prodi_id)) {
            return back()->withInput()->with('error', 'Dokumen pada tahap khusus prodi hanya dapat ditujukan ke prodi tahap tersebut.');
        }

        $task = $stage->tasks()->create([
            'title' => $data['title'],
            'description' => $data['description'],
            'link' => $data['link'],
            'category' => $data['category'],
            'prodi_id' => null,
            'is_required' => true,
            'sort_order' => $stage->tasks()->max('sort_order') + 1,
            'created_by' => $request->user()->id,
        ]);
        $task->prodis()->sync($prodiIds);

        $prodiParam = $prodiIds->first();

        return redirect()
            ->route('internal.standard-preparations.show', [$standard, 'stage' => $stage->id, 'prodi' => $prodiParam])
            ->with('success', 'Dokumen berhasil ditambahkan.');
    }

    public function updateTask(Request $request, Standard $standard, PreparationStage $stage, PreparationTask $task): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'link' => ['required', 'string'],
            'category' => ['required', 'in:'.implode(',', PreparationTask::CATEGORIES)],
            'prodi_ids' => ['nullable', 'array'],
            'prodi_ids.*' => ['integer', 'distinct', 'exists:prodis,id'],
        ]);

        if ($task->created_by !== $request->user()->id && ! $request->user()->hasRole('admin')) {
            abort(403);
        }

        $prodiIds = collect($data['prodi_ids'] ?? [])->map(fn ($id) => (int) $id)->unique()->values();
        if ($stage->prodi_id && ($prodiIds->count() !== 1 || $prodiIds->first() !== (int) $stage->prodi_id)) {
            return back()->withInput()->with('error', 'Dokumen pada tahap khusus prodi hanya dapat ditujukan ke prodi tahap tersebut.');
        }

        $task->update([
            'title' => $data['title'],
            'description' => $data['description'],
            'link' => $data['link'],
            'category' => $data['category'],
            'prodi_id' => null,
        ]);
        $task->prodis()->sync($prodiIds);

        return redirect()
            ->route('internal.standard-preparations.show', [$standard, 'stage' => $stage->id, 'prodi' => $prodiIds->first()])
            ->with('success', 'Dokumen berhasil diperbarui.');
    }

    public function destroyTask(Request $request, Standard $standard, PreparationStage $stage, PreparationTask $task): RedirectResponse
    {
        if ($task->created_by !== $request->user()->id && ! $request->user()->hasRole('admin')) {
            abort(403);
        }

        $prodiId = $request->integer('prodi') ?: $task->prodis()->value('prodis.id');
        $task->delete();

        return redirect()
            ->route('internal.standard-preparations.show', [$standard, 'stage' => $stage->id, 'prodi' => $prodiId])
            ->with('success', 'Dokumen dihapus.');
    }

    private function calcProgress(Collection $tasks, ?Prodi $prodi): array
    {
        if ($prodi) {
            $progress = $tasks->flatMap(fn ($t) => $t->progress)->where('is_applicable', true);

            return [$progress->count(), $progress->where('is_done', true)->count()];
        }

        return [$tasks->count(), 0];
    }
}
