<?php

namespace App\Http\Controllers;

use App\Models\AccreditationYear;
use App\Models\Assessment;
use App\Models\Finding;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function index()
    {
        $totalAssessments = Assessment::count();
        $submittedAssessments = Assessment::where('status', 'submitted')->count();
        $totalFindings = Finding::count();
        
        $recentAssessments = Assessment::with(['accreditationYear', 'assessor'])
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'totalAssessments', 
            'submittedAssessments', 
            'totalFindings', 
            'recentAssessments'
        ));
    }

    public function data(): JsonResponse
    {
        $years = AccreditationYear::orderBy('year')->get(['id','year']);

        $assCounts = Assessment::query()
            ->selectRaw('accreditation_year_id, COUNT(*) as total')
            ->where('status', 'submitted')
            ->groupBy('accreditation_year_id')
            ->pluck('total', 'accreditation_year_id');

        $findingCounts = Finding::query()
            ->join('assessments', 'findings.assessment_id', '=', 'assessments.id')
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
}
