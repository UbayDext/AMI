<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AmiAuditeeAssignmentGroup;
use App\Models\AmiCycle;
use App\Models\AmiReview;
use App\Models\AmiSubmission;
use App\Models\AuditorDecree;
use App\Models\Prodi;
use App\Models\Standard;
use App\Models\User;
use App\Models\OnboardingProgress;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AmiCycleController extends Controller
{
    public function index(Request $request): View
    {
        $cycles = AmiCycle::withCount('submissions')->latest()->get();
        $amiCycleOnboarding = $request->user()->hasRole('admin')
            ? OnboardingProgress::firstOrCreate(
                ['user_id' => $request->user()->id, 'onboarding_key' => 'admin_ami_cycles', 'version' => 1],
                ['current_step' => 0, 'status' => 'started', 'started_at' => now(), 'last_seen_at' => now()]
            ) : null;

        return view('admin.ami.cycles.index', compact('cycles', 'amiCycleOnboarding'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'period_start' => 'nullable|date',
            'period_end' => 'nullable|date|after_or_equal:period_start',
        ]);

        AmiCycle::create($data);

        return back()->with('success', 'Siklus AMI berhasil dibuat.');
    }

    public function show(AmiCycle $cycle): View
    {
        $cycle->load(['submissions.prodi', 'submissions.standard', 'submissions.owner', 'submissions.evidences', 'submissions.review.reviewer']);

        $prodis = Prodi::where('is_active', true)->orderBy('name')->get();
        $standards = Standard::orderByRaw('LENGTH(code), code')->get();
        $auditors = User::role('auditor')->orderBy('name')->select(['id', 'name'])->get();
        $auditees = User::role('auditee')->orderBy('name')->select(['id', 'name'])->get();
        $decrees = AuditorDecree::orderByDesc('id')->get();

        $submissions = $cycle->submissions;

        return view('admin.ami.cycles.show', compact('cycle', 'submissions', 'prodis', 'standards', 'auditors', 'auditees', 'decrees'));
    }

    public function patch(Request $request, AmiCycle $cycle): RedirectResponse
    {
        $data = $request->validate(['status' => 'required|in:draft,active,closed']);
        $cycle->update($data);

        if ($data['status'] === 'closed') {
            AmiReview::whereHas('submission', fn ($q) => $q->where('cycle_id', $cycle->id))
                ->whereIn('status', ['pending', 'in_progress'])
                ->update(['status' => 'completed', 'completed_at' => now()]);
        }

        return back()->with('success', 'Status siklus diperbarui.');
    }

    public function destroy(AmiCycle $cycle): RedirectResponse
    {
        $cycle->delete();

        return redirect()->route('admin.ami.cycles.index')->with('success', 'Siklus AMI dihapus.');
    }

    public function storeSubmission(Request $request, AmiCycle $cycle): RedirectResponse
    {
        $data = $request->validate([
            'prodi_id' => 'required|exists:prodis,id',
            'standard_id' => 'required|exists:standards,id',
        ]);

        $exists = AmiSubmission::where('cycle_id', $cycle->id)
            ->where('prodi_id', $data['prodi_id'])
            ->where('standard_id', $data['standard_id'])
            ->exists();

        if ($exists) {
            return back()->with('error', 'Submission untuk prodi + standar ini sudah ada.');
        }

        DB::transaction(function () use ($cycle, $data) {
            $group = AmiAuditeeAssignmentGroup::create([
                'cycle_id' => $cycle->id, 'standard_id' => $data['standard_id'],
                'prodi_id' => $data['prodi_id'], 'assignment_mode' => 'all_auditees',
                'can_create' => true, 'can_edit' => true,
                'assigned_by' => auth()->id(), 'assigned_at' => now(),
            ]);
            $now = now();
            $members = User::role('auditee')->where('is_active', true)->pluck('id')->map(fn ($userId) => [
                'assignment_group_id' => $group->id, 'user_id' => $userId,
                'can_edit' => true, 'assigned_by' => auth()->id(), 'joined_at' => $now,
                'created_at' => $now, 'updated_at' => $now,
            ])->all();
            DB::table('ami_auditee_assignment_members')->insertOrIgnore($members);

            AmiSubmission::create([
                'cycle_id' => $cycle->id, 'prodi_id' => $data['prodi_id'],
                'standard_id' => $data['standard_id'], 'assignment_group_id' => $group->id,
                'status' => 'draft',
            ]);
        });

        return back()->with('success', 'Submission berhasil ditugaskan kepada semua auditee aktif.');
    }

    public function destroySubmission(AmiCycle $cycle, AmiSubmission $submission): RedirectResponse
    {
        abort_if($submission->cycle_id !== $cycle->id, 404);
        $submission->delete();

        return back()->with('success', 'Submission dihapus.');
    }

    public function assignReviewer(Request $request, AmiCycle $cycle, AmiSubmission $submission): RedirectResponse
    {
        abort_if($submission->cycle_id !== $cycle->id, 404);
        $data = $request->validate([
            'reviewer_id' => 'required|exists:users,id',
            'decree_id' => 'nullable|exists:auditor_decrees,id',
        ]);
        abort_unless(User::findOrFail($data['reviewer_id'])->hasRole('auditor'), 422, 'User yang dipilih bukan auditor.');

        AmiReview::updateOrCreate(
            ['submission_id' => $submission->id],
            [
                'reviewer_id' => $data['reviewer_id'],
                'decree_id' => $data['decree_id'] ?: null,
                'status' => 'pending',
            ]
        );

        return back()->with('success', 'Auditor berhasil ditugaskan.');
    }
}
