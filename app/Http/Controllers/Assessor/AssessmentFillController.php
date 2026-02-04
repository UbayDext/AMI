<?php

namespace App\Http\Controllers\Assessor;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\Assessment;
use App\Models\Ptk;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class AssessmentFillController extends Controller
{
    public function edit(Assessment $assessment)
    {
        abort_unless($assessment->assessor_id === auth()->id(), 403);

        $questions = Question::with(['standard', 'options'])
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $answers = $assessment->answers()->get()->keyBy('question_id');

        // untuk form temuan
        $standards = \App\Models\Standard::orderBy('code')->get();
        $areas = \App\Models\AuditArea::orderBy('name')->get();
        $assessment->load(['findings.standard', 'findings.auditArea']);

        $ptks = Ptk::where('assessment_id', $assessment->id)->get()->keyBy('question_id');

        return view('assessor.assessments.fill', compact('assessment', 'questions', 'answers', 'standards', 'areas', 'ptks'));
    }

    public function update(Request $request, Assessment $assessment)
    {
        abort_unless($assessment->assessor_id === auth()->id(), 403);

        $questions = Question::with('options')->where('is_active', true)->orderBy('sort_order')->get();

        // ambil jawaban existing (buat rule file required kalau belum ada file)
        $existingAnswers = $assessment->answers()->get()->keyBy('question_id');

        $rules = [];

        foreach ($questions as $q) {
            $id = $q->id;
            $ans = $existingAnswers[$id] ?? null;

            // keterangan wajib
            $rules["ket_$id"] = ['required', Rule::in(['sesuai', 'sebagian', 'tidak'])];

            // alasan wajib kalau sebagian/tidak
            $rules["alasan_$id"] = ['nullable', 'string', 'max:5000', Rule::requiredIf(fn() => in_array($request->input("ket_$id"), ['sebagian', 'tidak'], true))];
            // === PTK: wajib kalau ket == 'tidak' ===
            $rules["ptk_area_$id"] = [Rule::requiredIf(fn() => $request->input("ket_$id") === 'tidak'), 'nullable', 'exists:audit_areas,id'];

            $rules["ptk_kondisi_$id"] = [Rule::requiredIf(fn() => $request->input("ket_$id") === 'tidak'), 'nullable', 'string', 'max:10000'];

            $rules["ptk_akar_$id"] = ['nullable', 'string', 'max:10000'];
            $rules["ptk_akibat_$id"] = ['nullable', 'string', 'max:10000'];
            $rules["ptk_rekom_$id"] = ['nullable', 'string', 'max:10000'];

            $rules["ptk_kategori_$id"] = [Rule::requiredIf(fn() => $request->input("ket_$id") === 'tidak'), 'nullable', Rule::in(['Observasi', 'Ketidaksesuaian', 'OFI'])];

            $rules["ptk_rencana_$id"] = ['nullable', 'string', 'max:10000'];
            $rules["ptk_due_$id"] = ['nullable', 'date'];

            // bukti (value_text) berdasarkan tipe soal
            $base = $q->is_required ? ['required'] : ['nullable'];

            if (in_array($q->type, ['select', 'radio'], true)) {
                $allowed = $q->options->pluck('value')->all();
                $rules["bukti_$id"] = array_merge($base, ['string', Rule::in($allowed)]);
            } elseif ($q->type === 'number') {
                $rules["bukti_$id"] = array_merge($base, ['numeric']);
            } elseif ($q->type === 'textarea') {
                $rules["bukti_$id"] = array_merge($base, ['string', 'max:10000']);
            } elseif ($q->type === 'checkbox') {
                // kalau suatu saat kamu pakai checkbox
                $rules["bukti_$id"] = array_merge($base, ['array']);
            } elseif ($q->type === 'file') {
                // untuk file: bukti text boleh nullable, file bisa required kalau required & belum ada file
                $rules["bukti_$id"] = ['nullable', 'string', 'max:10000'];

                $fileBase = $q->is_required && !$ans?->file_path ? ['required'] : ['nullable'];
                $rules["bukti_file_$id"] = array_merge($fileBase, ['file', 'max:5120']); // 5MB
            } else {
                // text default
                $rules["bukti_$id"] = array_merge($base, ['string', 'max:1000']);
            }
        }

        $validated = $request->validate($rules);

        DB::transaction(function () use ($assessment, $questions, $validated, $request) {
            foreach ($questions as $q) {
                $id = $q->id;

                $answer = Answer::firstOrNew([
                    'assessment_id' => $assessment->id,
                    'question_id' => $id,
                ]);

                // simpan keterangan + alasan
                $answer->status = $validated["ket_$id"];
                $answer->reason = $validated["alasan_$id"] ?? null;

                // reset value_json utk aman
                $answer->value_json = null;

                // simpan bukti / jawaban ke value_text
                if ($q->type === 'checkbox') {
                    $answer->value_json = $validated["bukti_$id"] ?? [];
                    $answer->value_text = null;
                } else {
                    $answer->value_text = $validated["bukti_$id"] ?? null;
                }

                // simpan file
                $fileKey = "bukti_file_$id";
                if ($request->hasFile($fileKey)) {
                    $path = $request->file($fileKey)->store("assessments/{$assessment->id}", 'public');
                    $answer->file_path = $path;
                }

                $answer->save();
                $ket = $validated["ket_$id"];

                // kalau "tidak sesuai" => simpan / update PTK
                if ($ket === 'tidak') {
                    Ptk::updateOrCreate(
                        [
                            'assessment_id' => $assessment->id,
                            'question_id' => $id,
                        ],
                        [
                            // kalau tabel PTK kamu punya kolom-kolom ini
                            'standard_id' => $q->standard_id,
                            'audit_area_id' => $validated["ptk_area_$id"],
                            'condition_desc' => $validated["ptk_kondisi_$id"],
                            'root_cause' => $validated["ptk_akar_$id"] ?? null,
                            'impact' => $validated["ptk_akibat_$id"] ?? null,
                            'recommendation' => $validated["ptk_rekom_$id"] ?? null,
                            'category' => $validated["ptk_kategori_$id"] ?? null,
                            'corrective_plan' => $validated["ptk_rencana_$id"] ?? null,
                            'due_date' => $validated["ptk_due_$id"] ?? null,
                        ],
                    );
                } else {
                    // kalau bukan "tidak sesuai" => hapus PTK supaya bersih
                    Ptk::where('assessment_id', $assessment->id)->where('question_id', $id)->delete();
                }
            }

            if ($request->boolean('submit')) {
                $assessment->status = 'submitted';
                $assessment->save();
            }
        });

        return back()->with('success', $request->boolean('submit') ? 'Berhasil submit.' : 'Berhasil simpan draft.');
    }
}
