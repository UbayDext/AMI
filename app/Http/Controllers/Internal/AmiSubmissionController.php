<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Models\AmiAuditeeAssignment;
use App\Models\AmiChecklistQuestion;
use App\Models\AmiCycle;
use App\Models\AmiSubmission;
use App\Models\AmiSubmissionAnswer;
use App\Models\PreparationTask;
use App\Models\Prodi;
use App\Models\Standard;
use App\Models\SubmissionEvidence;
use App\Models\SubmissionReference;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AmiSubmissionController extends Controller
{
    public function index(Request $request): View
    {
        $prodis = Prodi::where('is_active', true)->orderBy('name')->get();
        $prodi = $prodis->firstWhere('id', $request->integer('prodi'));
        $user = $request->user();
        $standardIds = $this->accessibleStandardIds($user);

        $query = AmiSubmission::with(['cycle', 'standard', 'owner', 'evidences', 'references'])
            ->whereHas('cycle', fn ($q) => $q->whereIn('status', ['active', 'closed']));
        if (! $user->hasRole('admin')) {
            $query->whereIn('standard_id', $standardIds);
        }
        if ($prodi) {
            $query->where('prodi_id', $prodi->id);
        }

        $submissions = $query->latest()->get()->groupBy('cycle_id');
        $activeCycles = AmiCycle::where('status', 'active')->orderByDesc('id')->get();
        $standards = Standard::query()
            ->when(! $user->hasRole('admin'), fn ($q) => $q->whereIn('id', $standardIds))
            ->orderByRaw('LENGTH(code), code')->get();

        return view('internal.ami.index', compact('prodis', 'prodi', 'submissions', 'activeCycles', 'standards'));
    }

    public function storeSubmission(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'cycle_id' => 'required|exists:ami_cycles,id',
            'prodi_id' => 'required|exists:prodis,id',
            'standard_id' => 'required|exists:standards,id',
        ]);
        $cycle = AmiCycle::findOrFail($data['cycle_id']);
        abort_if($cycle->status !== 'active', 403, 'Siklus AMI sudah tidak aktif.');
        $assignment = $this->resolveWritableAssignment(
            $request->user(), $cycle->id, (int) $data['standard_id'], (int) $data['prodi_id']
        );

        if (AmiSubmission::where('cycle_id', $cycle->id)->where('prodi_id', $data['prodi_id'])
            ->where('standard_id', $data['standard_id'])->exists()) {
            return back()->with('error', 'Submission untuk prodi + standar ini sudah ada di siklus tersebut.');
        }

        AmiSubmission::create([
            'cycle_id' => $cycle->id,
            'prodi_id' => $data['prodi_id'],
            'standard_id' => $data['standard_id'],
            'owner_id' => $request->user()->id,
            'assignment_id' => $assignment?->id,
            'status' => 'draft',
        ]);

        return redirect()->route('internal.ami.index', ['prodi' => $data['prodi_id']])
            ->with('success', 'Submission berhasil ditambahkan.');
    }

    public function show(Request $request, AmiSubmission $submission): View
    {
        $this->authorizeView($request->user(), $submission);
        $submission->load([
            'cycle', 'prodi', 'standard', 'owner', 'answers', 'review.answers',
            'references', 'evidences.preparationTask',
        ]);
        $questions = AmiChecklistQuestion::where('standard_code', $this->standardCode($submission))
            ->orderBy('question_number')->get();
        $refMap = $submission->references->groupBy('ami_question_id');
        $evidenceMap = $submission->evidences->groupBy('ami_question_id')
            ->map(fn ($items) => $items->groupBy('category'));
        $prepFiles = $this->availableTasks($submission)->get()->map(fn ($task) => [
            'id' => $task->id, 'label' => $task->title, 'url' => $task->link,
            'category' => $task->category,
        ]);
        $auditorAnswers = $submission->review
            ? $submission->review->answers->keyBy('question_id') : collect();
        $answerMap = $submission->answers->keyBy('question_id')->map(fn ($answer) => [
            'status' => $answer->status, 'notes' => $answer->notes,
        ]);
        $canEditSubmission = $this->canEdit($request->user(), $submission);

        return view('internal.ami.show', compact(
            'submission', 'questions', 'refMap', 'evidenceMap', 'prepFiles',
            'auditorAnswers', 'answerMap', 'canEditSubmission'
        ));
    }

    public function storeReference(Request $request, AmiSubmission $submission): RedirectResponse
    {
        $this->authorizeEdit($request->user(), $submission);
        $data = $request->validate([
            'ami_question_id' => 'required|exists:ami_checklist_questions,id',
            'title' => 'required|string|max:255',
            'url' => 'required|url|max:2048',
            'notes' => 'nullable|string|max:500',
        ]);
        $this->assertQuestion((int) $data['ami_question_id'], $submission);
        $submission->references()->create([...$data, 'added_by' => $request->user()->id]);

        return back()->with('success', 'Referensi berhasil ditambahkan.');
    }

    public function destroyReference(Request $request, AmiSubmission $submission, SubmissionReference $reference): RedirectResponse
    {
        $this->authorizeEdit($request->user(), $submission);
        abort_if($reference->submission_id !== $submission->id, 403);
        $reference->delete();

        return back()->with('success', 'Referensi dihapus.');
    }

    public function storeEvidence(Request $request, AmiSubmission $submission): RedirectResponse
    {
        $this->authorizeEdit($request->user(), $submission);
        $data = $request->validate([
            'ami_question_id' => 'required|exists:ami_checklist_questions,id',
            'category' => 'required|in:kebijakan,pelaksanaan,evaluasi,pendukung_digital',
            'preparation_task_id' => 'required|exists:preparation_tasks,id',
            'notes' => 'nullable|string|max:500',
        ]);
        $this->assertQuestion((int) $data['ami_question_id'], $submission);
        abort_unless($this->availableTasks($submission)->whereKey($data['preparation_task_id'])->exists(), 422,
            'Dokumen tidak termasuk prodi atau standar submission.');
        abort_unless($this->availableTasks($submission)
            ->whereKey($data['preparation_task_id'])
            ->where('category', $data['category'])
            ->exists(), 422, 'Kategori dokumen tidak sesuai dengan kategori bukti.');

        if ($submission->evidences()->where('ami_question_id', $data['ami_question_id'])->count() >= 20) {
            return back()->with('error', 'Maksimal 20 bukti per pertanyaan.');
        }
        try {
            $submission->evidences()->create([...$data, 'added_by' => $request->user()->id]);
        } catch (UniqueConstraintViolationException) {
            return back()->with('error', 'Dokumen ini sudah ditambahkan ke kategori tersebut.');
        }

        return back()->with('success', 'Bukti berhasil ditambahkan.');
    }

    public function destroyEvidence(Request $request, AmiSubmission $submission, SubmissionEvidence $evidence): RedirectResponse
    {
        $this->authorizeEdit($request->user(), $submission);
        abort_if($evidence->submission_id !== $submission->id, 403);
        $evidence->delete();

        return back()->with('success', 'Bukti dihapus.');
    }

    public function saveStatuses(Request $request, AmiSubmission $submission): RedirectResponse
    {
        $this->authorizeEdit($request->user(), $submission);
        $data = $request->validate([
            'statuses' => 'nullable|array',
            'statuses.*.status' => 'required|in:sesuai,sebagian,tidak_bukti_tidak_memadai,tidak_ada_bukti_tidak_dilaksanakan,tidak_bukti_tidak_memadai_tidak_konsisten,tidak_tidak_ada_bukti,tidak_dilaksanakan_tidak_ada_bukti',
            'statuses.*.notes' => 'nullable|string|max:500',
        ]);
        $validIds = AmiChecklistQuestion::where('standard_code', $this->standardCode($submission))
            ->pluck('id')->map(fn ($id) => (int) $id)->all();

        DB::transaction(function () use ($data, $submission, $request, $validIds) {
            foreach ($data['statuses'] ?? [] as $questionId => $answer) {
                abort_unless(in_array((int) $questionId, $validIds, true), 422,
                    'Pertanyaan tidak termasuk standar submission.');
                AmiSubmissionAnswer::updateOrCreate(
                    ['submission_id' => $submission->id, 'question_id' => (int) $questionId],
                    ['status' => $answer['status'], 'notes' => $answer['notes'] ?? null, 'answered_by' => $request->user()->id]
                );
            }
        });

        return back()->with('success', 'Keterangan disimpan.');
    }

    public function submit(Request $request, AmiSubmission $submission): RedirectResponse
    {
        $this->authorizeEdit($request->user(), $submission);
        if ($submission->references()->count() === 0 && $submission->evidences()->count() === 0) {
            return back()->with('error', 'Tambahkan minimal satu referensi atau bukti sebelum submit.');
        }
        $submission->update([
            'status' => 'submitted', 'submitted_at' => now(), 'submitted_by' => $request->user()->id,
        ]);

        return back()->with('success', 'Submission berhasil dikirim ke auditor.');
    }

    private function accessibleStandardIds(User $user): array
    {
        if ($user->hasRole('admin')) {
            return Standard::pluck('id')->map(fn ($id) => (int) $id)->all();
        }
        $codes = $user->roles()->whereIn('name', Standard::pluck('code'))->pluck('name');

        return Standard::whereIn('code', $codes)->pluck('id')
            ->merge($user->amiAssignments()->pluck('standard_id'))->unique()
            ->map(fn ($id) => (int) $id)->values()->all();
    }

    private function authorizeView(User $user, AmiSubmission $submission): void
    {
        abort_unless($user->hasRole('admin') || in_array($submission->standard_id, $this->accessibleStandardIds($user), true), 403);
    }

    private function canEdit(User $user, AmiSubmission $submission): bool
    {
        if (! in_array($submission->status, ['draft', 'revision'], true)) {
            return false;
        }
        if ($user->hasRole('admin')) {
            return true;
        }
        if ($submission->owner_id !== $user->id) {
            return false;
        }

        return ! $submission->assignment_id || $user->amiAssignments()
            ->whereKey($submission->assignment_id)->where('can_edit', true)->exists();
    }

    private function authorizeEdit(User $user, AmiSubmission $submission): void
    {
        $this->authorizeView($user, $submission);
        abort_unless($this->canEdit($user, $submission), 403, 'Submission auditee lain hanya dapat dilihat.');
    }

    private function resolveWritableAssignment(User $user, int $cycleId, int $standardId, int $prodiId): ?AmiAuditeeAssignment
    {
        if ($user->hasRole('admin')) {
            return null;
        }
        $assignment = $user->amiAssignments()->where('cycle_id', $cycleId)
            ->where('standard_id', $standardId)->where('can_create', true)->first();
        if ($assignment) {
            abort_unless($assignment->coversProdi($prodiId), 403, 'Prodi tidak termasuk cakupan penugasan.');

            return $assignment;
        }
        $standard = Standard::findOrFail($standardId);
        abort_unless($user->hasRole($standard->code), 403, 'Standar tidak ditugaskan kepada Anda.');

        return AmiAuditeeAssignment::firstOrCreate(
            ['cycle_id' => $cycleId, 'user_id' => $user->id, 'standard_id' => $standardId],
            ['prodi_scope' => 'all', 'can_create' => true, 'can_edit' => true, 'assigned_at' => now()]
        );
    }

    private function assertQuestion(int $questionId, AmiSubmission $submission): void
    {
        abort_unless(AmiChecklistQuestion::whereKey($questionId)
            ->where('standard_code', $this->standardCode($submission))->exists(), 422,
            'Pertanyaan tidak termasuk standar submission.');
    }

    private function availableTasks(AmiSubmission $submission)
    {
        return PreparationTask::whereHas('stage', fn ($query) => $query
            ->where(fn ($scope) => $scope->where('prodi_id', $submission->prodi_id)->orWhereNull('prodi_id'))
            ->where(fn ($scope) => $scope->where('standard_id', $submission->standard_id)->orWhereNull('standard_id')))
            ->where(fn ($query) => $query
                ->whereHas('prodis', fn ($assigned) => $assigned->whereKey($submission->prodi_id))
                ->orWhere('prodi_id', $submission->prodi_id)
                ->orWhere(fn ($global) => $global->whereNull('prodi_id')->whereDoesntHave('prodis')))
            ->whereNotNull('link');
    }

    private function standardCode(AmiSubmission $submission): string
    {
        $code = $submission->standard->code;

        return preg_replace_callback('/^([A-Z]+)(\d+)$/', fn ($match) => $match[1].str_pad($match[2], 2, '0', STR_PAD_LEFT), $code) ?? $code;
    }
}
