<?php

namespace App\Http\Controllers\Assessor;

use App\Http\Controllers\Controller;
use App\Models\AccreditationYear;
use App\Models\Assessment;
use App\Models\Finding;
use App\Models\QuestionCategory;
use App\Models\Standard;
use Illuminate\Http\Request;

class AssessmentListController extends Controller
{
    public function index(Request $request)
    {
        $years = AccreditationYear::orderByDesc('year')->get();
        // Fetch for filters
        $categories = QuestionCategory::orderBy('name')->get();
        $standards = Standard::all()->sortBy(fn($s) => (int) filter_var($s->code, FILTER_SANITIZE_NUMBER_INT));

        $q = Assessment::query()
            ->with(['accreditationYear', 'assessor']) // eager load assessor too
            ->withMax('findings as last_finding_id', 'id')
            ->where('assessor_id', auth()->id())
            ->orderByDesc('id');

        if ($request->filled('year')) {
            $q->where('accreditation_year_id', $request->integer('year'));
        }

        if ($request->filled('status')) {
            $q->where('status', $request->string('status')->toString());
        }

        if ($request->filled('category_id')) {
            // Assessment stores 'unit_name', so we need to find the name from ID
            $catName = QuestionCategory::find($request->category_id)?->name;
            if ($catName) {
                $q->where('unit_name', $catName);
            }
        }

        // Note: Standard filter handled in collection processing since Assessment doesn't have standard_id directly

        $assessments = $q->get();

        // ambil data finding terakhir sekaligus
        $lastFindingMap = Finding::whereIn('id', $assessments->pluck('last_finding_id')->filter())
            ->get()
            ->keyBy('id');

        // Pre-fetch all questions to map Categories -> Standards -> Questions
        // This is needed to know which standards are relevant for a given Category (Assessment Unit)
        // Optimization: We could cache this mapping or just query distincts
        $categoryStandardMap = \App\Models\Question::select('category_id', 'standard_id')
            ->distinct()
            ->get()
            ->groupBy('category_id')
            ->map(fn($items) => $items->pluck('standard_id')->unique());

        // Grouping: Category Name -> Standard ID -> Assessments
        // Since an Asssessment = A Unit = A Category, we iterate Assessments.
        // For each Assessment, we look up which Standards apply to its Category.
        // Then we place the Assessment under each of those Standards.

        $nestedGroups = collect();

        foreach ($assessments as $assessment) {
            // Find Category ID by name (since assessment only has unit_name)
            // This is a bit inefficient, assuming unit_name == category name perfectly
            $category = $categories->firstWhere('name', $assessment->unit_name);

            if (!$category) {
                // Fallback for uncategorized or mismatched names
                $catKey = 'Uncategorized';
                $stdKey = 'General';
                // $nestedGroups[$catKey][$stdKey][] = $assessment; 
                continue;
            }

            $catId = $category->id;

            // Get standards for this category
            $relevantStandardIds = $categoryStandardMap[$catId] ?? collect();

            // Filter by Standard request if present
            if ($request->filled('standard_id')) {
                if (!$relevantStandardIds->contains($request->standard_id)) {
                    continue; // Skip this assessment if it doesn't contain the requested standard
                }
                $relevantStandardIds = collect([$request->standard_id]);
            }

            foreach ($relevantStandardIds as $stdId) {
                // Initialize structure
                if (!isset($nestedGroups[$catId])) {
                    $nestedGroups[$catId] = collect();
                }
                if (!isset($nestedGroups[$catId][$stdId])) {
                    $nestedGroups[$catId][$stdId] = collect();
                }

                $nestedGroups[$catId][$stdId]->push($assessment);
            }
        }

        return view('assessor.assessments.index', compact(
            'assessments',
            'nestedGroups',
            'years',
            'lastFindingMap',
            'categories',
            'standards'
        ));
    }
}
