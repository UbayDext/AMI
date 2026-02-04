<?php

namespace App\Http\Controllers\Assessor;

use App\Http\Controllers\Controller;
use App\Models\Assessment;

class AssessmentListController extends Controller
{
    public function index()
    {
        $assessments = Assessment::with('accreditationYear')
            ->where('assessor_id', auth()->id())
            ->latest()
            ->paginate(20);

        return view('assessor.assessments.index', compact('assessments'));
    }
}
