<?php

namespace App\Http\Controllers;

use App\Models\OnboardingProgress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OnboardingController extends Controller
{
    private const KEY = 'dashboard_admin';
    private const VERSION = 3;

    public function update(Request $request): JsonResponse
    {
        abort_unless($request->user()->hasRole('admin'), 403);
        $data = $request->validate([
            'current_step' => ['required', 'integer', 'min:0', 'max:30'],
            'status' => ['required', 'in:started,completed,skipped'],
        ]);
        $now = now();
        $progress = OnboardingProgress::updateOrCreate(
            ['user_id' => $request->user()->id, 'onboarding_key' => self::KEY, 'version' => self::VERSION],
            ['current_step' => $data['current_step'], 'status' => $data['status'], 'started_at' => $now,
                'last_seen_at' => $now, 'completed_at' => $data['status'] === 'completed' ? $now : null]
        );

        return response()->json(['saved' => true, 'progress' => $progress]);
    }

    public function restart(Request $request): JsonResponse
    {
        abort_unless($request->user()->hasRole('admin'), 403);
        OnboardingProgress::updateOrCreate(
            ['user_id' => $request->user()->id, 'onboarding_key' => self::KEY, 'version' => self::VERSION],
            ['current_step' => 0, 'status' => 'started', 'started_at' => now(), 'completed_at' => null, 'last_seen_at' => now()]
        );
        return response()->json(['saved' => true]);
    }

    public function assessmentUpdate(Request $request): JsonResponse
    {
        abort_unless($request->user()->hasRole('admin'), 403);
        $data = $request->validate(['current_step' => ['required', 'integer', 'min:0', 'max:10'], 'status' => ['required', 'in:started,completed,skipped']]);
        $now = now();
        $progress = OnboardingProgress::updateOrCreate(
            ['user_id' => $request->user()->id, 'onboarding_key' => 'admin_assessments', 'version' => 1],
            ['current_step' => $data['current_step'], 'status' => $data['status'], 'started_at' => $now, 'last_seen_at' => $now, 'completed_at' => $data['status'] === 'completed' ? $now : null]
        );
        return response()->json(['saved' => true, 'progress' => $progress]);
    }

    public function assessmentRestart(Request $request): JsonResponse
    {
        abort_unless($request->user()->hasRole('admin'), 403);
        OnboardingProgress::updateOrCreate(
            ['user_id' => $request->user()->id, 'onboarding_key' => 'admin_assessments', 'version' => 1],
            ['current_step' => 0, 'status' => 'started', 'started_at' => now(), 'completed_at' => null, 'last_seen_at' => now()]
        );
        return response()->json(['saved' => true]);
    }

    public function amiCycleUpdate(Request $request): JsonResponse
    {
        abort_unless($request->user()->hasRole('admin'), 403);
        $data = $request->validate(['current_step' => ['required', 'integer', 'min:0', 'max:10'], 'status' => ['required', 'in:started,completed,skipped']]);
        $now = now();
        $progress = OnboardingProgress::updateOrCreate(
            ['user_id' => $request->user()->id, 'onboarding_key' => 'admin_ami_cycles', 'version' => 1],
            ['current_step' => $data['current_step'], 'status' => $data['status'], 'started_at' => $now, 'last_seen_at' => $now, 'completed_at' => $data['status'] === 'completed' ? $now : null]
        );
        return response()->json(['saved' => true, 'progress' => $progress]);
    }

    public function amiCycleRestart(Request $request): JsonResponse
    {
        abort_unless($request->user()->hasRole('admin'), 403);
        OnboardingProgress::updateOrCreate(
            ['user_id' => $request->user()->id, 'onboarding_key' => 'admin_ami_cycles', 'version' => 1],
            ['current_step' => 0, 'status' => 'started', 'started_at' => now(), 'completed_at' => null, 'last_seen_at' => now()]
        );
        return response()->json(['saved' => true]);
    }

    public function standardUpdate(Request $request): JsonResponse
    {
        abort_unless($request->user()->hasRole('admin'), 403);
        $data = $request->validate(['current_step' => ['required', 'integer', 'min:0', 'max:10'], 'status' => ['required', 'in:started,completed,skipped']]);
        $now = now();
        $progress = OnboardingProgress::updateOrCreate(
            ['user_id' => $request->user()->id, 'onboarding_key' => 'admin_evidence_standards', 'version' => 1],
            ['current_step' => $data['current_step'], 'status' => $data['status'], 'started_at' => $now, 'last_seen_at' => $now, 'completed_at' => $data['status'] === 'completed' ? $now : null]
        );
        return response()->json(['saved' => true, 'progress' => $progress]);
    }

    public function standardRestart(Request $request): JsonResponse
    {
        abort_unless($request->user()->hasRole('admin'), 403);
        OnboardingProgress::updateOrCreate(
            ['user_id' => $request->user()->id, 'onboarding_key' => 'admin_evidence_standards', 'version' => 1],
            ['current_step' => 0, 'status' => 'started', 'started_at' => now(), 'completed_at' => null, 'last_seen_at' => now()]
        );
        return response()->json(['saved' => true]);
    }

    public function amiQuestionUpdate(Request $request): JsonResponse
    {
        abort_unless($request->user()->hasRole('admin'), 403);
        $data = $request->validate(['current_step' => ['required', 'integer', 'min:0', 'max:12'], 'status' => ['required', 'in:started,completed,skipped']]);
        $now = now();
        $progress = OnboardingProgress::updateOrCreate(
            ['user_id' => $request->user()->id, 'onboarding_key' => 'admin_ami_questions', 'version' => 1],
            ['current_step' => $data['current_step'], 'status' => $data['status'], 'started_at' => $now, 'last_seen_at' => $now, 'completed_at' => $data['status'] === 'completed' ? $now : null]
        );
        return response()->json(['saved' => true, 'progress' => $progress]);
    }

    public function amiQuestionRestart(Request $request): JsonResponse
    {
        abort_unless($request->user()->hasRole('admin'), 403);
        OnboardingProgress::updateOrCreate(
            ['user_id' => $request->user()->id, 'onboarding_key' => 'admin_ami_questions', 'version' => 1],
            ['current_step' => 0, 'status' => 'started', 'started_at' => now(), 'completed_at' => null, 'last_seen_at' => now()]
        );
        return response()->json(['saved' => true]);
    }

    public function auditeeDashboardUpdate(Request $request): JsonResponse
    {
        abort_unless($request->user()->hasRole('auditee'), 403);
        $data = $request->validate(['current_step' => ['required', 'integer', 'min:0', 'max:12'], 'status' => ['required', 'in:started,completed,skipped']]);
        $now = now();
        $progress = OnboardingProgress::updateOrCreate(
            ['user_id' => $request->user()->id, 'onboarding_key' => 'dashboard_auditee', 'version' => 1],
            ['current_step' => $data['current_step'], 'status' => $data['status'], 'started_at' => $now, 'last_seen_at' => $now, 'completed_at' => $data['status'] === 'completed' ? $now : null]
        );
        return response()->json(['saved' => true, 'progress' => $progress]);
    }

    public function auditeeDashboardRestart(Request $request): JsonResponse
    {
        abort_unless($request->user()->hasRole('auditee'), 403);
        OnboardingProgress::updateOrCreate(
            ['user_id' => $request->user()->id, 'onboarding_key' => 'dashboard_auditee', 'version' => 1],
            ['current_step' => 0, 'status' => 'started', 'started_at' => now(), 'completed_at' => null, 'last_seen_at' => now()]
        );
        return response()->json(['saved' => true]);
    }

    public function internalPreparationsUpdate(Request $request): JsonResponse
    {
        abort_unless($request->user()->hasRole('auditee'), 403);
        $data = $request->validate(['current_step' => ['required', 'integer', 'min:0', 'max:10'], 'status' => ['required', 'in:started,completed,skipped']]);
        $now = now();
        $progress = OnboardingProgress::updateOrCreate(
            ['user_id' => $request->user()->id, 'onboarding_key' => 'internal_preparations', 'version' => 1],
            ['current_step' => $data['current_step'], 'status' => $data['status'], 'started_at' => $now, 'last_seen_at' => $now, 'completed_at' => $data['status'] === 'completed' ? $now : null]
        );
        return response()->json(['saved' => true, 'progress' => $progress]);
    }

    public function internalPreparationsRestart(Request $request): JsonResponse
    {
        abort_unless($request->user()->hasRole('auditee'), 403);
        OnboardingProgress::updateOrCreate(
            ['user_id' => $request->user()->id, 'onboarding_key' => 'internal_preparations', 'version' => 1],
            ['current_step' => 0, 'status' => 'started', 'started_at' => now(), 'completed_at' => null, 'last_seen_at' => now()]
        );
        return response()->json(['saved' => true]);
    }

    public function internalPreparationsDetailUpdate(Request $request): JsonResponse
    {
        abort_unless($request->user()->hasRole('auditee'), 403);
        $data = $request->validate(['current_step' => ['required', 'integer', 'min:0', 'max:12'], 'status' => ['required', 'in:started,completed,skipped']]);
        $now = now();
        $progress = OnboardingProgress::updateOrCreate(
            ['user_id' => $request->user()->id, 'onboarding_key' => 'internal_preparations_detail', 'version' => 1],
            ['current_step' => $data['current_step'], 'status' => $data['status'], 'started_at' => $now, 'last_seen_at' => $now, 'completed_at' => $data['status'] === 'completed' ? $now : null]
        );
        return response()->json(['saved' => true, 'progress' => $progress]);
    }

    public function internalPreparationsDetailRestart(Request $request): JsonResponse
    {
        abort_unless($request->user()->hasRole('auditee'), 403);
        OnboardingProgress::updateOrCreate(
            ['user_id' => $request->user()->id, 'onboarding_key' => 'internal_preparations_detail', 'version' => 1],
            ['current_step' => 0, 'status' => 'started', 'started_at' => now(), 'completed_at' => null, 'last_seen_at' => now()]
        );
        return response()->json(['saved' => true]);
    }
}
