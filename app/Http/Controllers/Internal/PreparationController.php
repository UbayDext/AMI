<?php

namespace App\Http\Controllers\Internal;
use App\Http\Controllers\Controller;
use App\Models\AccreditationYear;
use App\Models\PreparationStage;
use App\Models\PreparationTask;
use App\Models\PreparationTaskFile;
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

        $stages = PreparationStage::with(['tasks.files'])
            ->where('is_active', true)
            ->when($year, fn($q) => $q->where(function ($qq) use ($year) {
                $qq->whereNull('accreditation_year_id')
                   ->orWhere('accreditation_year_id', $year->id);
            }))
            ->orderBy('sort_order')
            ->get();

        $activeStage = $stages->first();
        if ($request->filled('stage')) {
            $activeStage = $stages->firstWhere('id', $request->integer('stage')) ?? $activeStage;
        }

        $total = $activeStage?->tasks->count() ?? 0;
        $done  = $activeStage?->tasks->where('is_done', true)->count() ?? 0;

        return view('internal.preparations.index', compact('year','stages','activeStage','total','done'));
    }

    public function upload(Request $request, PreparationTask $task)
    {
        $request->validate([
            'file' => ['required', 'file', 'max:10240'], // 10MB
        ]);

        $file = $request->file('file');

        $path = $file->store("preparations/task-{$task->id}", 'public');

        $task->files()->create([
            'uploaded_by'    => $request->user()->id,
            'file_path'      => $path,
            'original_name'  => $file->getClientOriginalName(),
            'mime_type'      => $file->getClientMimeType(),
            'size'           => $file->getSize(),
        ]);

        // auto tandai selesai setelah upload (boleh kamu matikan kalau mau manual)
        if (! $task->is_done) {
            $task->forceFill([
                'is_done' => true,
                'done_at' => now(),
                'done_by' => $request->user()->id,
            ])->save();
        }

        return back()->with('success', 'Dokumen berhasil diupload.');
    }

    public function toggle(Request $request, PreparationTask $task)
    {
        $done = $request->boolean('done');

        $task->forceFill([
            'is_done' => $done,
            'done_at' => $done ? now() : null,
            'done_by' => $done ? $request->user()->id : null,
        ])->save();

        return back()->with('success', $done ? 'Ditandai selesai.' : 'Dibuka kembali.');
    }

    public function destroyFile(PreparationTaskFile $file)
    {
        Storage::disk('public')->delete($file->file_path);
        $file->delete();

        return back()->with('success', 'File dihapus.');
    }
}
