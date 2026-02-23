<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PreparationStage;
use App\Models\PreparationTask;
use App\Models\Standard;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StandardPreparationController extends Controller
{
    /** Admin: list / manage stages + tasks for one standard */
    public function index(Standard $standard, Request $request)
    {
        $stages = $standard->preparationStages()
            ->with(['tasks.files'])
            ->orderBy('sort_order')
            ->get();

        $activeStage = $stages->first();
        if ($request->filled('stage')) {
            $activeStage = $stages->firstWhere('id', $request->integer('stage')) ?? $activeStage;
        }

        return view('admin.standard-preparations.index', compact('standard', 'stages', 'activeStage'));
    }

    /** Admin: create a new stage under a standard */
    public function storeStage(Request $request, Standard $standard): RedirectResponse
    {
        $data = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'sort_order'  => ['nullable', 'integer', 'min:0'],
        ]);

        $standard->preparationStages()->create([
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
            'sort_order'  => $data['sort_order'] ?? ($standard->preparationStages()->max('sort_order') + 1),
            'is_active'   => true,
        ]);

        return redirect()
            ->route('admin.standard-preparations.index', $standard)
            ->with('success', 'Tahap berhasil ditambahkan.');
    }

    /** Admin: delete a stage */
    public function destroyStage(Standard $standard, PreparationStage $stage): RedirectResponse
    {
        $stage->delete();

        return redirect()
            ->route('admin.standard-preparations.index', $standard)
            ->with('success', 'Tahap dihapus.');
    }

    /** Admin: create a task under a stage */
    public function storeTask(Request $request, Standard $standard, PreparationStage $stage): RedirectResponse
    {
        $data = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_required' => ['nullable', 'boolean'],
        ]);

        $stage->tasks()->create([
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
            'is_required' => $request->boolean('is_required', true),
            'sort_order'  => $stage->tasks()->max('sort_order') + 1,
        ]);

        return redirect()
            ->route('admin.standard-preparations.index', [$standard, 'stage' => $stage->id])
            ->with('success', 'Task berhasil ditambahkan.');
    }

    /** Admin: delete a task */
    public function destroyTask(Standard $standard, PreparationStage $stage, PreparationTask $task): RedirectResponse
    {
        $task->delete();

        return redirect()
            ->route('admin.standard-preparations.index', [$standard, 'stage' => $stage->id])
            ->with('success', 'Task dihapus.');
    }
}
