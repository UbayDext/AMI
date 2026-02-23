<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccreditationYear;
use App\Models\Assessment;
use App\Models\User;
use Illuminate\Http\Request;

class AssessmentController extends Controller
{
    public function index()
    {
        $assessments = Assessment::with(['accreditationYear', 'assessor'])
            ->latest()->paginate(20);

        return view('admin.assessments.index', compact('assessments'));
    }

    public function create()
    {
        $years = AccreditationYear::orderBy('year', 'desc')->get();
        $assessors = User::role('asesor')->orderBy('name')->get();
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
