<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PreparationStage;
use App\Models\PreparationTask;
use App\Models\Prodi;
use App\Models\Standard;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StandardPreparationController extends Controller
{
    public function landing(Request $request)
    {
        $prodi = null;
        if ($request->filled('prodi')) {
            $prodi = Prodi::findOrFail((int) $request->input('prodi'));
        }

        $standards = Standard::orderByRaw('LENGTH(code), code')
            ->with([
                'preparationStages.tasks.progress' => fn ($q) => $prodi
                    ? $q->where('prodi_id', $prodi->id)
                    : $q,
            ])
            ->get()
            ->map(function ($s) use ($prodi) {
                $allTasks = $s->preparationStages->flatMap(fn ($st) => $st->tasks);
                if ($prodi) {
                    $allProg = $allTasks->flatMap(fn ($t) => $t->progress)->where('is_applicable', true);
                    $s->total_tasks = $allProg->count();
                    $s->done_tasks = $allProg->where('is_done', true)->count();
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

        return view('admin.standard-preparations.landing', compact('standards', 'prodi', 'prodis'));
    }

    public function index(Standard $standard, Request $request)
    {
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
                'prodi',
                'tasks' => fn ($q) => $q->where(function ($q2) use ($prodi) {
                    if (! $prodi) {
                        return;
                    }
                    $q2->whereHas('prodis', fn ($assigned) => $assigned->whereKey($prodi->id))
                        ->orWhere('prodi_id', $prodi->id)
                        ->orWhere(fn ($global) => $global->whereNull('prodi_id')->whereDoesntHave('prodis'));
                })->orderBy('sort_order'),
                'tasks.prodi',
                'tasks.prodis',
                'tasks.progress' => fn ($q) => $prodi ? $q->where('prodi_id', $prodi->id) : $q,
                'tasks.files' => fn ($q) => $prodi
                    ? $q->where('prodi_id', $prodi->id)
                    : $q,
            ])
            ->orderBy('sort_order')
            ->get();

        $activeStage = $stages->first();
        if ($request->filled('stage')) {
            $activeStage = $stages->firstWhere('id', (int) $request->input('stage')) ?? $activeStage;
        }

        $prodis = Prodi::where('is_active', true)->orderBy('name')->get();

        return view('admin.standard-preparations.index', compact('standard', 'stages', 'activeStage', 'prodi', 'prodis'));
    }

    public function storeStage(Request $request, Standard $standard): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'prodi_id' => ['nullable', 'exists:prodis,id'],
        ]);

        $standard->preparationStages()->create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'prodi_id' => $data['prodi_id'] ?? null,
            'sort_order' => $data['sort_order'] ?? ($standard->preparationStages()->max('sort_order') + 1),
            'is_active' => true,
        ]);

        return redirect()
            ->route('admin.standard-preparations.index', [$standard, 'prodi' => $request->input('prodi')])
            ->with('success', 'Tahap berhasil ditambahkan.');
    }

    public function destroyStage(Request $request, Standard $standard, PreparationStage $stage): RedirectResponse
    {
        $stage->delete();

        return redirect()
            ->route('admin.standard-preparations.index', [$standard, 'prodi' => $request->input('prodi')])
            ->with('success', 'Tahap dihapus.');
    }

    public function storeTask(Request $request, Standard $standard, PreparationStage $stage): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'link' => ['nullable', 'url', 'max:2048'],
            'category' => ['required', 'in:'.implode(',', PreparationTask::CATEGORIES)],
            'is_required' => ['nullable', 'boolean'],
            'prodi_ids' => ['nullable', 'array'],
            'prodi_ids.*' => ['integer', 'distinct', 'exists:prodis,id'],
        ]);

        $prodiIds = collect($data['prodi_ids'] ?? [])->map(fn ($id) => (int) $id)->unique()->values();
        if ($stage->prodi_id && ($prodiIds->count() !== 1 || $prodiIds->first() !== (int) $stage->prodi_id)) {
            return back()->withInput()->with('error', 'Task pada tahap khusus prodi hanya dapat ditujukan ke prodi tahap tersebut.');
        }

        $task = $stage->tasks()->create([
            'title' => $data['title'],
            'link' => $data['link'] ?? null,
            'category' => $data['category'],
            'is_required' => $request->boolean('is_required', true),
            'prodi_id' => null,
            'sort_order' => $stage->tasks()->max('sort_order') + 1,
        ]);
        $task->prodis()->sync($prodiIds);

        return redirect()
            ->route('admin.standard-preparations.index', [$standard, 'stage' => $stage->id, 'prodi' => $request->input('prodi')])
            ->with('success', 'Task berhasil ditambahkan.');
    }

    public function destroyTask(Request $request, Standard $standard, PreparationStage $stage, PreparationTask $task): RedirectResponse
    {
        $task->delete();

        return redirect()
            ->route('admin.standard-preparations.index', [$standard, 'stage' => $stage->id, 'prodi' => $request->input('prodi')])
            ->with('success', 'Task dihapus.');
    }
}
