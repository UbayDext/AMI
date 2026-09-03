<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccreditationYear;
use App\Models\Assessment;
use App\Models\User;
use App\Models\OnboardingProgress;
use Illuminate\Http\Request;

class AssessmentController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:draft,submitted,reviewed'],
            'year' => ['nullable', 'integer', 'exists:accreditation_years,id'],
        ]);

        $assessmentQuery = Assessment::query();
        $stats = [
            'total' => (clone $assessmentQuery)->count(),
            'draft' => (clone $assessmentQuery)->where('status', 'draft')->count(),
            'submitted' => (clone $assessmentQuery)->where('status', 'submitted')->count(),
            'reviewed' => (clone $assessmentQuery)->where('status', 'reviewed')->count(),
        ];

        $assessments = $assessmentQuery
            ->with(['accreditationYear', 'assessor'])
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('unit_name', 'like', "%{$search}%")
                        ->orWhereHas('assessor', fn ($query) => $query
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%"));
                });
            })
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($filters['year'] ?? null, fn ($query, $year) => $query->where('accreditation_year_id', $year))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $years = AccreditationYear::orderByDesc('year')->get(['id', 'year']);
        $assessmentOnboarding = $request->user()->hasRole('admin')
            ? OnboardingProgress::firstOrCreate(
                ['user_id' => $request->user()->id, 'onboarding_key' => 'admin_assessments', 'version' => 1],
                ['current_step' => 0, 'status' => 'started', 'started_at' => now(), 'last_seen_at' => now()]
            ) : null;

        return view('admin.assessments.index', compact('assessments', 'years', 'stats', 'filters', 'assessmentOnboarding'));
    }

    public function create()
    {
        $years = AccreditationYear::orderBy('year', 'desc')->get();
        $assessors = User::role('auditor')->orderBy('name')->get();
        $categories = \App\Models\QuestionCategory::orderBy('name')->get();
        $standards = \App\Models\Standard::orderByRaw('LENGTH(code), code')->get();

        // Build a map: standard_id => array of category ids that have questions for that standard
        $standardCategoryMap = \App\Models\Question::select('standard_id', 'category_id')
            ->whereNotNull('standard_id')
            ->whereNotNull('category_id')
            ->distinct()
            ->get()
            ->groupBy('standard_id')
            ->map(fn($rows) => $rows->pluck('category_id')->unique()->values());

        return view('admin.assessments.create', compact('years', 'assessors', 'categories', 'standards', 'standardCategoryMap'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'accreditation_year_id' => ['required', 'exists:accreditation_years,id'],
            'assessor_id' => ['required', 'exists:users,id'],
            'unit_name' => ['required', 'string', 'max:255'],
        ]);

        $assessment = Assessment::create($data);

        return redirect()->route('admin.assessments.show', $assessment)
            ->with('success', 'Assessment dibuat.');
    }

    public function show(Assessment $assessment)
    {
        $assessment->load(['accreditationYear', 'assessor', 'findings.standard']);

        // Fetch questions related to this assessment's category (unit_name)
        // Replicating logic from AssessmentReportController/AssessmentFillController
        $groupedQuestions = \App\Models\Question::with(['standard', 'category', 'options'])
            ->where('is_active', true)
            ->whereHas('category', function ($q) use ($assessment) {
                $q->where('name', $assessment->unit_name);
            })
            ->orderByRaw('COALESCE(category_id, 0) asc')
            ->orderBy('sort_order')
            ->get()
            ->groupBy(fn($q) => $q->category_id ?? 0);

        $answers = $assessment->answers()->get()->keyBy('question_id');
        $ptks = $assessment->ptks()->get()->keyBy('question_id');

        return view('admin.assessments.show', compact('assessment', 'groupedQuestions', 'answers', 'ptks'));
    }

    public function destroy(Assessment $assessment)
    {
        $assessment->delete();

        return back()->with('success', 'assessments asesor dihapus');
    }
}
