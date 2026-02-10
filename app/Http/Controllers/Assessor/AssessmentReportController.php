<?php

namespace App\Http\Controllers\Assessor;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\QuestionCategory;
use Illuminate\Http\Request;

class AssessmentReportController extends Controller
{
    public function show(Assessment $assessment)
    {
        // Ensure the dashboard layout can accommodate full width or we use a custom layout for printing.
        // We need grouped questions similar to the fill view.

        $groupedQuestions = $assessment->questions()
    ->with(['standard', 'category', 'options'])
    ->get()
    ->sortBy(fn($q) => $q->sort_order)
    ->groupBy(fn($q) => $q->category_id ?? 0);


        $answers = $assessment->answers->keyBy('question_id');
        $ptks = $assessment->ptks->keyBy('question_id');

        // Also get independent findings
        $independentFindings = $assessment->findings;

        return view('assessor.assessments.report', compact(
            'assessment',
            'groupedQuestions',
            'answers',
            'ptks',
            'independentFindings'
        ));
    }
}
