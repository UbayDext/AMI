<?php

namespace App\Http\Controllers\Assessor;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Finding;
use App\Models\Standard;
use App\Models\AuditArea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FindingController extends Controller
{
    public function store(Request $request, Assessment $assessment)
    {
        abort_unless($assessment->assessor_id === auth()->id(), 403);

        $data = $request->validate([
            'standard_id' => ['required', 'exists:standards,id'],
            'audit_area_ids' => ['required', 'array'],
            'audit_area_ids.*' => ['exists:audit_areas,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'severity' => ['required', 'in:minor,major,critical'],
        ]);

        $finding = DB::transaction(function () use ($assessment, $data) {
            $standard = Standard::findOrFail($data['standard_id']);

            // Use the first area for the code generation
            $firstAreaId = $data['audit_area_ids'][0];
            $area = AuditArea::findOrFail($firstAreaId);

            // ambil urutan terakhir dalam assessment ini (lock biar aman)
            $lastSeq = Finding::where('assessment_id', $assessment->id)->lockForUpdate()->max('sequence');

            // mulai dari 002 biar mirip Excel contoh
            $nextSeq = ($lastSeq ?? 1) + 1;

            do {
                $code = sprintf('PTK/%03d/%s/%s', $nextSeq, $standard->code, $area->code);

                $exists = Finding::where('assessment_id', $assessment->id)->where('code', $code)->exists();

                if ($exists) {
                    $nextSeq++;
                }
            } while ($exists);

            $code = sprintf('PTK/%03d/%s/%s', $nextSeq, $standard->code, $area->code);

            return Finding::create([
                'assessment_id' => $assessment->id,
                'standard_id' => $standard->id,
                'audit_area_ids' => $data['audit_area_ids'],
                'sequence' => $nextSeq,
                'code' => $code,
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'severity' => $data['severity'],
            ]);
        });

        return back()->with('success', "Temuan dibuat: {$finding->code}");
    }
}
