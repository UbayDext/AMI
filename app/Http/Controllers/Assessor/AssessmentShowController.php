<?php

namespace App\Http\Controllers\Assessor;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Ptk;

class AssessmentShowController extends Controller
{
    public function show(Assessment $assessment)
    {
        abort_unless($assessment->assessor_id === auth()->id(), 403);

        $assessment->load([
            'accreditationYear',
            'findings.standard',
            'findings.auditArea',

            // penting: load question + options + standard
            'answers' => function ($q) {
                $q->with([
                    'question' => fn ($qq) => $qq->with(['standard', 'options']),
                ])->orderBy('question_id');
            },
        ]);

        // ambil PTK per question_id
        $ptks = Ptk::where('assessment_id', $assessment->id)
            ->with(['auditArea', 'standard']) // kalau relasi ada
            ->get()
            ->keyBy('question_id');

        return view('assessor.assessments.show', compact('assessment', 'ptks'));
    }
}
