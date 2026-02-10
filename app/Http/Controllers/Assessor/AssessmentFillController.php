<?php

namespace App\Http\Controllers\Assessor;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\Assessment;
use App\Models\Ptk;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AssessmentFillController extends Controller
{
    public function edit(Assessment $assessment)
    {
        abort_unless($assessment->assessor_id === auth()->id(), 403);

        $questions = Question::with(['standard', 'options', 'category'])
            ->where('is_active', true)
            ->orderByRaw('COALESCE(category_id, 0) asc')
            ->orderBy('sort_order')
            ->get();

        $groupedQuestions = $questions->groupBy(fn($q) => $q->category_id ?? 0);

        $answers = $assessment->answers()->get()->keyBy('question_id');

        $standards = \App\Models\Standard::get()
            ->transform(function ($s) {
                $s->code = preg_replace_callback('/(\d+)/', function ($m) {
                    return sprintf('%02d', $m[1]);
                }, $s->code);
                return $s;
            })
            ->sortBy(function ($standard) {
                if (preg_match('/(\d+)/', $standard->code, $matches)) {
                    return (int)$matches[1];
                }
                return 99999;
            })->values();

        $areas = \App\Models\AuditArea::orderBy('name')->get();
        $assessment->load(['findings.standard']);

        $ptks = Ptk::where('assessment_id', $assessment->id)->get()->keyBy('question_id');

        return view('assessor.assessments.fill-v2', compact(
            'assessment',
            'groupedQuestions',
            'answers',
            'standards',
            'areas',
            'ptks'
        ));
    }

    public function update(Request $request, Assessment $assessment)
    {
        abort_unless($assessment->assessor_id === auth()->id(), 403);

        $keys = collect($request->all())->keys();
        $questionIds = $keys->filter(fn($k) => str_starts_with($k, 'ket_'))
            ->map(fn($k) => str_replace('ket_', '', $k));

        foreach ($questionIds as $qid) {
            $status = $request->input("ket_$qid");
            $valueText = $request->input("bukti_$qid");
            $reason = $request->input("alasan_$qid");

            // Handle File Upload
            $filePath = null;
            if ($request->hasFile("bukti_file_$qid")) {
                $filePath = $request->file("bukti_file_$qid")->store('evidence', 'public');
            }

            // Save Answer
            $answer = Answer::updateOrCreate(
                ['assessment_id' => $assessment->id, 'question_id' => $qid],
                [
                    'status' => $status,
                    'value_text' => $valueText,
                    'reason' => $reason,
                ]
            );

            if ($filePath) {
                $answer->update(['file_path' => $filePath]);
            }

            // Handle PTK logic
            if ($status === 'tidak' || $status === 'sebagian') {
                $ptkData = [
                    'audit_area_ids' => $request->input("ptk_area_$qid", []), // Now Array
                    'condition_desc' => $request->input("ptk_kondisi_$qid"),
                    'root_cause' => $request->input("ptk_akar_$qid"),
                    'impact' => $request->input("ptk_akibat_$qid"),
                    'recommendation' => $request->input("ptk_rekom_$qid"),
                    'category' => $request->input("ptk_kategori_$qid"),
                    'corrective_plan' => $request->input("ptk_rencana_$qid"),
                    'due_date' => $request->input("ptk_due_$qid"),
                ];

                Ptk::updateOrCreate(
                    ['assessment_id' => $assessment->id, 'question_id' => $qid],
                    $ptkData
                );
            } else {
                // Remove PTK if status became compliant
                Ptk::where(['assessment_id' => $assessment->id, 'question_id' => $qid])->delete();
            }
        }

        // Handle Submit Final
        if ($request->input('submit') == '1') {
            $assessment->update(['status' => 'submitted']);
            return redirect()->route('assessor.assessments.index')->with('success', 'Assessment submitted successfully.');
        }

        return back()->with('success', 'Draft saved successfully.');
    }
}
