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
    public function edit(Request $request, Assessment $assessment)
    {
        abort_unless($assessment->assessor_id === auth()->id(), 403);

        if ($assessment->status === 'submitted') {
            return redirect()->route('assessor.assessments.index')
                ->with('error', 'Assessment ini sudah disubmit dan tidak dapat diedit lagi.');
        }

        $questions = Question::with(['standard', 'options', 'category'])
            ->where('is_active', true)
            ->when($assessment->unit_name, function ($query) use ($assessment) {
                $query->whereHas('category', function ($q) use ($assessment) {
                    $q->where('name', $assessment->unit_name);
                });
            })
            ->when($request->filled('standard_id'), function ($query) use ($request) {
                $query->where('standard_id', $request->standard_id);
            })
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

        if ($assessment->status === 'submitted') {
            abort(403, 'Assessment ini sudah disubmit dan tidak dapat diubah lagi.');
        }



        $keys = collect($request->all())->keys();
        $questionIds = $keys->filter(fn($k) => str_starts_with($k, 'ket_'))
            ->map(fn($k) => str_replace('ket_', '', $k));

        \Log::info("Derived Question IDs:", $questionIds->toArray());

        // Pre-fetch questions to get standard_id and other details efficiently
        $questions = Question::whereIn('id', $questionIds)->get()->keyBy('id');

        foreach ($questionIds as $qid) {
            $status = $request->input("ket_$qid");
            $valueText = $request->input("bukti_$qid");
            $reason = $request->input("alasan_$qid");

            $question = $questions[$qid] ?? null;

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

            // Handle PTK logic - save PTK if category is present (includes "Sesuai" -> "Observasi")
            $ptkCategory = $request->input("ptk_kategori_$qid");

            \Log::info("Processing question $qid", [
                'status' => $status,
                'ptk_category' => $ptkCategory,
                'question_standard_id' => $question?->standard_id ?? 'null',
            ]);

            // Check if there is a category OR if status indicates non-compliance
            // We trust the category logic mostly, but as a fallback, if not 'sesuai', we generally expect a PTK.
            // However, relying on ptkCategory is safer given the new "Sesuai -> Observasi" rule.
            if ($ptkCategory || $status !== 'sesuai') {
                $ptkData = [
                    'standard_id' => $question?->standard_id, // Add Standard ID
                    'audit_area_ids' => $request->input("ptk_area_$qid", []),
                    'condition_desc' => $request->input("ptk_kondisi_$qid"),
                    'root_cause' => $request->input("ptk_akar_$qid"),
                    'impact' => $request->input("ptk_akibat_$qid"),
                    'recommendation' => $request->input("ptk_rekom_$qid"),
                    'category' => $ptkCategory,
                    'corrective_plan' => $request->input("ptk_rencana_$qid"),
                    'start_date'      => $request->input("ptk_start_date_$qid") ?: null,
                    'end_date'        => $request->input("ptk_end_date_$qid") ?: null,
                    'due_date'        => $request->input("ptk_due_$qid") ?: null,
                    'realisasi' => $request->input("ptk_realisasi_$qid"),
                    'efektifitas' => $request->input("ptk_efektifitas_$qid"),
                    'tl_status' => $request->input("ptk_tl_status_$qid"),
                ];

                \Log::info("Saving PTK for $qid", $ptkData);

                Ptk::updateOrCreate(
                    ['assessment_id' => $assessment->id, 'question_id' => $qid],
                    $ptkData
                );
            } else {
                // If NO Category and Status IS Sesuai (and logic implies no PTK), delete.
                // But with Sesuai->Observasi, this branch might rarely be hit unless user manually clears category?
                Ptk::where(['assessment_id' => $assessment->id, 'question_id' => $qid])->delete();
            }
        }

        // Handle Submit Final
        if ($request->input('submit') == '1') {
            $standardId = (int) $request->input('submit_standard_id');

            if ($standardId) {
                // Per-standard submit: only mark this standard as submitted
                $submitted = $assessment->submitted_standards ?? [];
                if (!in_array($standardId, $submitted)) {
                    $submitted[] = $standardId;
                }
                $assessment->update(['submitted_standards' => $submitted]);

                return redirect()->route('assessor.assessments.index')
                    ->with('success', 'Standar berhasil disubmit.');
            }

            // No specific standard → full assessment submit (fallback)
            $assessment->update(['status' => 'submitted']);
            return redirect()->route('assessor.assessments.index')
                ->with('success', 'Assessment submitted successfully.');
        }

        return back()->with('success', 'Draft saved successfully.');
    }
}
