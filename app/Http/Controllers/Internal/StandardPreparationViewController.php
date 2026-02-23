<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Models\Standard;
use Illuminate\Http\Request;

class StandardPreparationViewController extends Controller
{
    public function index()
    {
        $standards = Standard::query();

        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        if ($user && $user->hasRole('standar') && !$user->hasRole('admin')) {
            $userStandardRoles = $user->roles()->whereIn('name', Standard::pluck('code'))->pluck('name')->toArray();
            $standards->whereIn('code', $userStandardRoles);
        }

        $standards = $standards->orderByRaw('LENGTH(code), code')
            ->with(['preparationStages.tasks'])
            ->get()
            ->map(function ($s) {
                $allTasks = $s->preparationStages->flatMap(fn($stage) => $stage->tasks);
                $s->total_tasks = $allTasks->count();
                $s->done_tasks  = $allTasks->where('is_done', true)->count();
                $s->percent     = $s->total_tasks > 0
                    ? (int) round($s->done_tasks / $s->total_tasks * 100)
                    : 0;
                return $s;
            });

        return view('internal.standards.index', compact('standards'));
    }

    /** Assessor: checklist for one standard */
    public function show(Standard $standard, Request $request)
    {
        $stages = $standard->preparationStages()
            ->with(['tasks.files'])
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $activeStage = $stages->first();
        if ($request->filled('stage')) {
            $activeStage = $stages->firstWhere('id', $request->integer('stage')) ?? $activeStage;
        }

        $tasks   = $activeStage?->tasks?->sortBy('sort_order') ?? collect();
        $total   = $activeStage?->tasks->count() ?? 0;
        $done    = $activeStage?->tasks->where('is_done', true)->count() ?? 0;
        $percent = $total > 0 ? (int) round($done / $total * 100) : 0;

        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        if ($user && $user->hasRole('standar') && !$user->hasRole('admin')) {
            $userStandardRoles = $user->roles()->whereIn('name', Standard::pluck('code'))->pluck('name')->toArray();
            if (!in_array($standard->code, $userStandardRoles)) {
                abort(403, 'Anda tidak memiliki akses ke standar ini.');
            }
        }

        return view('internal.standards.show', compact(
            'standard',
            'stages',
            'activeStage',
            'tasks',
            'total',
            'done',
            'percent'
        ));
    }
}
