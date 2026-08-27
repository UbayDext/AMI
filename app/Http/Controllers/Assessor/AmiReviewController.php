<?php

namespace App\Http\Controllers\Assessor;

use App\Http\Controllers\Controller;
use App\Models\AmiChecklistQuestion;
use App\Models\AmiReview;
use App\Models\AuditArea;
use App\Models\ReviewAnswer;
use App\Models\ReviewFinding;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AmiReviewController extends Controller
{
    public function index(): View
    {
        $reviews = AmiReview::with(['submission.standard', 'submission.prodi', 'submission.cycle'])
            ->where('reviewer_id', auth()->id())
            ->latest()
            ->get();

        return view('assessor.ami.index', compact('reviews'));
    }

    public function show(AmiReview $review): View
    {
        abort_if($review->reviewer_id !== auth()->id(), 403);

        $review->load([
            'submission.prodi',
            'submission.standard',
            'submission.cycle',
            'submission.references',
            'submission.evidences.preparationTask',
            'submission.answers',
            'answers',
            'findings',
        ]);

        if ($review->status === 'pending') {
            $review->update(['status' => 'in_progress', 'started_at' => now()]);
            $review->submission->update(['status' => 'under_review']);

            // Pre-fill hasil review dari jawaban relasional Auditee.
            $statuses = $review->submission->answers->keyBy('question_id');
            if (! empty($statuses)) {
                ReviewAnswer::insertOrIgnore(
                    collect($statuses)->map(fn ($v, $qId) => [
                        'review_id' => $review->id,
                        'question_id' => (int) $qId,
                        'status' => $v->status,
                        'notes' => $v->notes,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ])->values()->all()
                );
                $review->load('answers');
            }
        }

        // Pertanyaan dari checklist seeder — normalisasi ST1 → ST01
        $stdCode = $this->padStandardCode($review->submission->standard->code);
        $questions = AmiChecklistQuestion::where('standard_code', $stdCode)
            ->orderBy('question_number')
            ->get();

        // answersMap[question_id] => ReviewAnswer
        $answersMap = $review->answers->keyBy('question_id');

        // auditeeStatuses[ami_question_id] => ['status'=>..., 'notes'=>...]
        $auditeeStatuses = $review->submission->answers->keyBy('question_id')->map(fn ($answer) => [
            'status' => $answer->status,
            'notes' => $answer->notes,
        ])->all();

        // refMap[ami_question_id] => Collection<SubmissionReference>
        $refMap = $review->submission->references->groupBy('ami_question_id');

        // evidenceMap[ami_question_id][category] => Collection<SubmissionEvidence>
        $evidenceMap = $review->submission->evidences
            ->groupBy('ami_question_id')
            ->map(fn ($evs) => $evs->groupBy('category'));

        $users = User::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $areas = AuditArea::orderBy('name')->get();

        return view('assessor.ami.show', compact(
            'review', 'questions', 'answersMap', 'auditeeStatuses', 'refMap', 'evidenceMap', 'users', 'areas'
        ));
    }

    public function saveAnswers(Request $request, AmiReview $review): RedirectResponse
    {
        abort_if($review->reviewer_id !== auth()->id(), 403);
        abort_if($review->status === 'completed', 403);

        $data = $request->validate([
            'answers' => 'nullable|array',
            'answers.*.status' => 'required|in:sesuai,sebagian,tidak_bukti_tidak_memadai,tidak_ada_bukti_tidak_dilaksanakan,tidak_bukti_tidak_memadai_tidak_konsisten,tidak_tidak_ada_bukti,tidak_dilaksanakan_tidak_ada_bukti,belum_diperiksa',
            'answers.*.notes' => 'nullable|string|max:500',
        ]);

        foreach ($data['answers'] ?? [] as $questionId => $ans) {
            ReviewAnswer::updateOrCreate(
                ['review_id' => $review->id, 'question_id' => $questionId],
                ['status' => $ans['status'], 'notes' => $ans['notes'] ?? null]
            );
        }

        return back()->with('success', 'Jawaban disimpan.');
    }

    public function storeFinding(Request $request, AmiReview $review): RedirectResponse
    {
        abort_if($review->reviewer_id !== auth()->id(), 403);
        abort_if($review->status === 'completed', 403);

        $data = $request->validate([
            'question_id' => 'nullable|exists:ami_checklist_questions,id',
            'audit_area_ids' => 'nullable|array',
            'audit_area_ids.*' => 'exists:audit_areas,id',
            'category' => 'required|string|max:100',
            'condition_desc' => 'required|string',
            'root_cause' => 'nullable|string',
            'impact' => 'nullable|string',
            'recommendation' => 'nullable|string',
            'corrective_plan' => 'nullable|string',
            'due_date' => 'nullable|date',
            'pic' => 'nullable|string|max:255',
            'realisasi' => 'nullable|string',
            'efektifitas' => 'nullable|in:Efektif,Kurang,Tidak Efektif',
            'tl_status' => 'nullable|in:Open,On Progress,Close,Toleran',
        ]);

        $review->findings()->create($data);

        return back()->with('success', 'Temuan berhasil disimpan.');
    }

    public function updateFindingAtl(Request $request, AmiReview $review, ReviewFinding $finding): RedirectResponse
    {
        abort_if($review->reviewer_id !== auth()->id(), 403);
        abort_if($finding->review_id !== $review->id, 403);

        $data = $request->validate([
            'realisasi' => 'nullable|string',
            'efektifitas' => 'nullable|in:Efektif,Kurang,Tidak Efektif',
            'tl_status' => 'nullable|in:Open,On Progress,Close,Toleran',
            'pic' => 'nullable|string|max:255',
        ]);

        $finding->update($data);

        return back()->with('success', 'Tindak lanjut disimpan.');
    }

    public function destroyFinding(AmiReview $review, ReviewFinding $finding): RedirectResponse
    {
        abort_if($review->reviewer_id !== auth()->id(), 403);
        abort_if($finding->review_id !== $review->id, 403);
        $finding->delete();

        return back()->with('success', 'Temuan dihapus.');
    }

    public function complete(AmiReview $review): RedirectResponse
    {
        abort_if($review->reviewer_id !== auth()->id(), 403);
        abort_if($review->status === 'completed', 403);

        $review->update(['status' => 'completed', 'completed_at' => now()]);
        $review->submission->update(['status' => 'accepted']);

        return back()->with('success', 'Review selesai. Submission auditee ditandai Diterima.');
    }

    private function padStandardCode(string $code): string
    {
        return preg_replace_callback('/^([A-Z]+)(\d+)$/', function ($m) {
            return $m[1].str_pad($m[2], 2, '0', STR_PAD_LEFT);
        }, $code) ?? $code;
    }
}
