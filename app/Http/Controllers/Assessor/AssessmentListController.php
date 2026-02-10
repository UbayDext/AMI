<?php

namespace App\Http\Controllers\Assessor;

use App\Http\Controllers\Controller;
use App\Models\AccreditationYear;
use App\Models\Assessment;
use App\Models\Finding;
use Illuminate\Http\Request;

class AssessmentListController extends Controller
{
   public function index(Request $request)
{
    $years = AccreditationYear::orderByDesc('year')->get();

    $q = Assessment::query()
        ->with('accreditationYear')
        ->withMax('findings as last_finding_id', 'id')
        ->orderByDesc('id');

    if ($request->filled('search')) {
        $s = $request->string('search')->toString();
        $q->where('unit_name', 'like', "%{$s}%");
    }

    if ($request->filled('year')) {
        $q->where('accreditation_year_id', $request->integer('year'));
    }

    if ($request->filled('status')) {
        $q->where('status', $request->string('status')->toString());
    }

    $assessments = $q->paginate(10)->withQueryString();

    // ambil data finding terakhir sekaligus
    $lastFindingMap = Finding::whereIn('id', $assessments->pluck('last_finding_id')->filter())
        ->get()
        ->keyBy('id');

    return view('assessor.assessments.index', compact('assessments', 'years', 'lastFindingMap'));
}
}
