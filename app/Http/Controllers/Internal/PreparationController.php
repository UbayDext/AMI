<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Models\AccreditationYear;
use App\Models\PreparationStage;
use App\Models\PreparationTask;
use App\Models\PreparationTaskFile;
use App\Models\Prodi;
use App\Models\ProdiTaskProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PreparationController extends Controller
{
    public function index(Request $request)
    {
        $year = null;
        if ($request->filled('year')) {
            $year = AccreditationYear::findOrFail($request->integer('year'));
        }

        $prodi = null;
        if ($request->filled('prodi')) {
            $prodi = Prodi::findOrFail((int) $request->input('prodi'));
        }

        $stages = PreparationStage::with([
            'tasks' => fn ($q) => $q->when($prodi, fn ($tasks) => $tasks->where(fn ($scope) => $scope
                ->whereHas('prodis', fn ($assigned) => $assigned->whereKey($prodi->id))
                ->orWhere('prodi_id', $prodi->id)
                ->orWhere(fn ($global) => $global->whereNull('prodi_id')->whereDoesntHave('prodis')))),
            'tasks.prodis',
            'tasks.progress' => fn ($q) => $prodi ? $q->where('prodi_id', $prodi->id) : $q,
            'tasks.files' => fn ($q) => $prodi ? $q->where('prodi_id', $prodi->id) : $q,
        ])
            ->where('is_active', true)
            ->when($year, fn ($q) => $q->where(function ($qq) use ($year) {
                $qq->whereNull('accreditation_year_id')
                    ->orWhere('accreditation_year_id', $year->id);
            }))
            ->orderBy('sort_order')
            ->get();

        $activeStage = $stages->first();
        if ($request->filled('stage')) {
            $activeStage = $stages->firstWhere('id', $request->integer('stage')) ?? $activeStage;
        }

        $total = 0;
        $done = 0;
        if ($activeStage && $prodi) {
            $applicableProgress = $activeStage->tasks
                ->flatMap(fn ($t) => $t->progress)
                ->where('is_applicable', true);
            $total = $applicableProgress->count();
            $done = $applicableProgress->where('is_done', true)->count();
        }

        $prodis = Prodi::where('is_active', true)->orderBy('name')->get();

        return view('internal.preparations.index', compact('year', 'stages', 'activeStage', 'total', 'done', 'prodi', 'prodis'));
    }

    public function upload(Request $request, PreparationTask $task)
    {
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

    public function storeLink(Request $request, PreparationTask $task)
    {
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
}
