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

        return view('admin.assessments.create', compact('years', 'assessors', 'categories'));
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
        return view('admin.assessments.show', compact('assessment'));
    }

    public function destroy(Assessment $assessment)
    {
        $assessment->delete();

        return back()->with('success', 'assessments asesor dihapus');
    }
}
