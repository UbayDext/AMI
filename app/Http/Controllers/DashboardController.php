<?php

namespace App\Http\Controllers;

use App\Models\AccreditationYear;
use App\Models\Assessment;
use App\Models\Finding;
use App\Models\Ptk;
use App\Models\Prodi;
use App\Models\User;
use App\Models\OnboardingProgress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'year_id' => ['nullable', 'integer', 'exists:accreditation_years,id'],
            'unit' => ['nullable', 'string', 'max:255'],
        ]);

        $assessmentQuery = Assessment::query()
            ->when($filters['year_id'] ?? null, fn ($query, $yearId) => $query->where('accreditation_year_id', $yearId))
            ->when($filters['unit'] ?? null, fn ($query, $unit) => $query->where('unit_name', $unit));

        $totalAssessments = (clone $assessmentQuery)->count();
        $submittedAssessments = (clone $assessmentQuery)->where('status', 'submitted')->count();

        $totalFindings = Finding::query()
            ->whereHas('assessment', fn ($query) => $query
                ->when($filters['year_id'] ?? null, fn ($q, $yearId) => $q->where('accreditation_year_id', $yearId))
                ->when($filters['unit'] ?? null, fn ($q, $unit) => $q->where('unit_name', $unit)))
            ->count();

        $totalPtks = Ptk::query()
            ->whereHas('assessment', fn ($query) => $query
                ->when($filters['year_id'] ?? null, fn ($q, $yearId) => $q->where('accreditation_year_id', $yearId))
                ->when($filters['unit'] ?? null, fn ($q, $unit) => $q->where('unit_name', $unit)))
            ->count();

        $recentAssessments = Assessment::with(['accreditationYear', 'assessor'])
            ->when($filters['year_id'] ?? null, fn ($query, $yearId) => $query->where('accreditation_year_id', $yearId))
            ->when($filters['unit'] ?? null, fn ($query, $unit) => $query->where('unit_name', $unit))
            ->latest()
            ->take(5)
            ->get();

        $years = AccreditationYear::orderByDesc('year')->get(['id', 'year']);
        // The dashboard filter must follow the Prodi master data. Keep distinct
        // historical assessment units as a fallback for records whose prodi has
        // since been renamed or deactivated.
        $units = Prodi::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->pluck('name')
            ->merge(Assessment::query()
                ->whereNotNull('unit_name')
                ->where('unit_name', '!=', '')
                ->distinct()
                ->pluck('unit_name'))
            ->unique()
            ->sort()
            ->values();

        $blockedUsers = collect();
        $adminOnboarding = null;
        $auditeeOnboarding = null;
        if (auth()->user()?->hasRole('admin')) {
            $blockedUsers = User::where('is_blocked', true)->latest()->get();
            $adminOnboarding = OnboardingProgress::firstOrCreate(
                ['user_id' => auth()->id(), 'onboarding_key' => 'dashboard_admin', 'version' => 3],
                ['current_step' => 0, 'status' => 'started', 'started_at' => now(), 'last_seen_at' => now()]
            );
        }
        if (auth()->user()?->hasRole('auditee') && ! auth()->user()?->hasRole('admin')) {
            $auditeeOnboarding = OnboardingProgress::firstOrCreate(
                ['user_id' => auth()->id(), 'onboarding_key' => 'dashboard_auditee', 'version' => 1],
                ['current_step' => 0, 'status' => 'started', 'started_at' => now(), 'last_seen_at' => now()]
            );
        }

        return view('dashboard', compact(
            'totalAssessments',
            'submittedAssessments',
            'totalFindings',
            'totalPtks',
            'recentAssessments',
            'blockedUsers',
            'years',
            'units',
            'filters',
            'adminOnboarding',
            'auditeeOnboarding',
        ));
    }

    public function data(Request $request): JsonResponse
    {
        $yearId = $request->integer('year_id') ?: null;
        $unit = $request->string('unit')->trim()->toString();
        $years = AccreditationYear::query()->when($yearId, fn ($query) => $query->whereKey($yearId))->orderBy('year')->get(['id', 'year']);

        $assCounts = Assessment::query()
            ->selectRaw('accreditation_year_id, COUNT(*) as total')
            ->where('status', 'submitted')
            ->when($unit, fn ($query) => $query->where('unit_name', $unit))
            ->groupBy('accreditation_year_id')
            ->pluck('total', 'accreditation_year_id');

        $findingCounts = Finding::query()
            ->join('assessments', 'findings.assessment_id', '=', 'assessments.id')
            ->when($unit, fn ($query) => $query->where('assessments.unit_name', $unit))
            ->selectRaw('assessments.accreditation_year_id, COUNT(findings.id) as total')
            ->groupBy('assessments.accreditation_year_id')
            ->pluck('total', 'assessments.accreditation_year_id');

        $labels = $years->pluck('year')->map(fn($y) => (string)$y)->values();
        $assData = $years->map(fn($y) => (int)($assCounts[$y->id] ?? 0))->values();
        $findingData = $years->map(fn($y) => (int)($findingCounts[$y->id] ?? 0))->values();

        return response()->json([
            'labels' => $labels,
            'assessments' => $assData,
            'findings' => $findingData,
        ]);
    }

    public function kategoriData(Request $request): JsonResponse
    {
        $yearId = $request->query('year_id');
        $unit = $request->string('unit')->trim()->toString();

        $years        = AccreditationYear::orderBy('year')->get(['id', 'year']);
        $kategoriList = ['Sesuai', 'Observasi', 'KTS Minor', 'KTS Mayor', 'OFI'];

        $colors = [
            'Sesuai'    => ['bg' => 'rgba(34,197,94,0.3)',   'border' => '#16a34a'],
            'Observasi' => ['bg' => 'rgba(250,204,21,0.3)',  'border' => '#ca8a04'],
            'KTS Minor' => ['bg' => 'rgba(249,115,22,0.3)', 'border' => '#ea580c'],
            'KTS Mayor' => ['bg' => 'rgba(239,68,68,0.3)',  'border' => '#dc2626'],
            'OFI'       => ['bg' => 'rgba(59,130,246,0.3)', 'border' => '#2563eb'],
        ];

        $baseQuery = Ptk::query()
            ->join('assessments', 'ptks.assessment_id', '=', 'assessments.id')
            ->when($unit, fn ($query) => $query->where('assessments.unit_name', $unit))
            ->whereIn('ptks.category', $kategoriList);

        if ($yearId) {
            $baseQuery->where('assessments.accreditation_year_id', $yearId);
        }

        $rows = $baseQuery
            ->selectRaw('assessments.accreditation_year_id, ptks.category, COUNT(*) as total')
            ->groupBy('assessments.accreditation_year_id', 'ptks.category')
            ->get();

        // Build map: year_id => { category => count }
        $map = [];
        foreach ($rows as $row) {
            $map[$row->accreditation_year_id][$row->category] = (int) $row->total;
        }

        if ($yearId) {
            // ── Single year mode: labels = categories, one dataset ──────
            $yearObj = $years->firstWhere('id', $yearId);
            $yearLabel = $yearObj ? (string) $yearObj->year : 'Tahun';

            $data = array_map(fn($kat) => (int) ($map[$yearId][$kat] ?? 0), $kategoriList);

            $datasets = [[
                'label'           => $yearLabel,
                'data'            => array_values($data),
                'backgroundColor' => 'rgba(99,102,241,0.25)',
                'borderColor'     => '#6366f1',
                'borderWidth'     => 2,
                'pointBackgroundColor' => array_map(fn($kat) => $colors[$kat]['border'], $kategoriList),
                'pointRadius'     => 5,
            ]];

            return response()->json([
                'labels'   => $kategoriList,
                'datasets' => $datasets,
                'years'    => $years->map(fn($y) => ['id' => $y->id, 'year' => (string) $y->year])->values(),
                'mode'     => 'single',
            ]);
        } else {
            // ── All years mode: labels = years, one dataset per category ─
            $labels = $years->pluck('year')->map(fn($y) => (string) $y)->values()->toArray();
            $datasets = [];

            foreach ($kategoriList as $kat) {
                $data = $years->map(fn($y) => (int) ($map[$y->id][$kat] ?? 0))->values()->toArray();
                $datasets[] = [
                    'label'           => $kat,
                    'data'            => $data,
                    'backgroundColor' => $colors[$kat]['bg'],
                    'borderColor'     => $colors[$kat]['border'],
                    'borderWidth'     => 2,
                    'pointRadius'     => 4,
                ];
            }

            return response()->json([
                'labels'   => $labels,
                'datasets' => $datasets,
                'years'    => $years->map(fn($y) => ['id' => $y->id, 'year' => (string) $y->year])->values(),
                'mode'     => 'all',
            ]);
        }
    }
}
