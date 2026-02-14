<?php

namespace App\Http\Controllers\Assessor;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Ptk;
use App\Models\Question;

class AssessmentShowController extends Controller
{
    public function show(Assessment $assessment)
    {
        abort_unless($assessment->assessor_id === auth()->id(), 403);

        $assessment->load([
            'accreditationYear',
            'findings.standard',
        ]);

        // Load questions grouped by category (same as fill controller)
        $questions = Question::with(['standard', 'options', 'category'])
            ->where('is_active', true)
            ->when($assessment->unit_name, function ($query) use ($assessment) {
                $query->whereHas('category', function ($q) use ($assessment) {
                    $q->where('name', $assessment->unit_name);
                });
            })
            ->orderByRaw('COALESCE(category_id, 0) asc')
            ->orderBy('sort_order')
            ->get();

        $groupedQuestions = $questions->groupBy(fn($q) => $q->category_id ?? 0);

        // Load answers keyed by question_id
        $answers = $assessment->answers()->with([
            'question' => fn($qq) => $qq->with(['standard', 'options']),
        ])->orderBy('question_id')->get()->keyBy('question_id');

        // Load PTKs keyed by question_id (with PTS)
        $ptks = Ptk::where('assessment_id', $assessment->id)
            ->with(['standard', 'pts'])
            ->get()
            ->keyBy('question_id');

        // Load audit areas for resolving area names
        $areas = \App\Models\AuditArea::orderBy('name')->get()->keyBy('id');

        return view('assessor.assessments.show', compact(
            'assessment',
            'groupedQuestions',
            'answers',
            'ptks',
            'areas'
        ));
    }
}
