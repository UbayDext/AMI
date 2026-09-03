<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Models\AmiAuditeeAssignment;
use App\Models\AmiAuditeeAssignmentGroup;
use App\Models\AmiChecklistQuestion;
use App\Models\AmiCycle;
use App\Models\AmiSubmission;
use App\Models\PreparationTask;
use App\Models\Prodi;
use App\Models\Standard;
use App\Models\SubmissionEvidence;
use App\Models\SubmissionReference;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class AmiSubmissionController extends Controller
{
    public function index(Request $request): View
    {
        $prodis = Prodi::where('is_active', true)->orderBy('name')->get();
        $prodi = $prodis->firstWhere('id', $request->integer('prodi'));
        $user = $request->user();
        $this->syncAllAuditeeMemberships($user);
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
        $questions = AmiChecklistQuestion::where(function ($query) use ($submission) {
                $query->where('standard_id', $submission->standard_id)
                    ->orWhere('standard_code', $this->standardCode($submission));
            })
            ->where('is_active', true)
            ->where(function ($query) use ($submission) {
                $query->whereDoesntHave('prodis')
                    ->orWhereHas('prodis', fn ($prodi) => $prodi->whereKey($submission->prodi_id));
            })
            ->orderBy('sort_order')->orderBy('question_number')->get();
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

    public function submit(Request $request, AmiSubmission $submission): RedirectResponse
    {
        $this->authorizeEdit($request->user(), $submission);

        $submitted = DB::transaction(function () use ($request, $submission) {
            $locked = AmiSubmission::whereKey($submission->id)->lockForUpdate()->firstOrFail();
            if (! in_array($locked->status, ['draft', 'revision'], true)) {
                return false;
            }
            if ($locked->references()->count() === 0 && $locked->evidences()->count() === 0) {
                return null;
            }
            $locked->update([
                'status' => 'submitted', 'submitted_at' => now(), 'submitted_by' => $request->user()->id,
            ]);

            return true;
        });

        if ($submitted === null) {
            return back()->with('error', 'Tambahkan minimal satu referensi atau bukti sebelum submit.');
        }
        if ($submitted === false) {
            return back()->with('error', 'Submission sudah dikirim oleh auditee lain.');
        }

        return back()->with('success', 'Submission berhasil dikirim ke auditor.');
    }

    private function accessibleStandardIds(User $user): array
    {
        if ($user->hasRole('admin')) {
            return Standard::pluck('id')->map(fn ($id) => (int) $id)->all();
        }
        $codes = $user->roles()->whereIn('name', Standard::pluck('code'))->pluck('name');

        $groupStandardIds = DB::table('ami_auditee_assignment_members as members')
            ->join('ami_auditee_assignment_groups as groups', 'groups.id', '=', 'members.assignment_group_id')
            ->where('members.user_id', $user->id)
            ->pluck('groups.standard_id');

        return Standard::whereIn('code', $codes)->pluck('id')
            ->merge($user->amiAssignments()->pluck('standard_id'))
            ->merge($groupStandardIds)->unique()
            ->map(fn ($id) => (int) $id)->values()->all();
    }

    private function authorizeView(User $user, AmiSubmission $submission): void
    {
        $this->syncAllAuditeeMemberships($user);
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
        if ($submission->assignment_group_id) {
            return DB::table('ami_auditee_assignment_members')
                ->where('assignment_group_id', $submission->assignment_group_id)
                ->where('user_id', $user->id)
                ->where('can_edit', true)
                ->exists();
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

    private function syncAllAuditeeMemberships(User $user): void
    {
        if (! $user->is_active || ! $user->hasRole('auditee')) {
            return;
        }

        $now = now();
        $rows = AmiAuditeeAssignmentGroup::query()
            ->where('assignment_mode', 'all_auditees')
            ->whereHas('cycle', fn ($query) => $query->whereIn('status', ['active', 'closed']))
            ->get(['id', 'can_edit', 'assigned_by'])
            ->map(fn ($group) => [
                'assignment_group_id' => $group->id,
                'user_id' => $user->id,
                'can_edit' => $group->can_edit,
                'assigned_by' => $group->assigned_by,
                'joined_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ])->all();

        DB::table('ami_auditee_assignment_members')->insertOrIgnore($rows);
    }
}
