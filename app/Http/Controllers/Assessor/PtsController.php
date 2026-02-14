<?php

namespace App\Http\Controllers\Assessor;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Pts;
use App\Models\Ptk;
use Illuminate\Http\Request;

class PtsController extends Controller
{
    public function update(Request $request, Assessment $assessment)
    {
        abort_unless($assessment->assessor_id === auth()->id(), 403);

        // Get all PTK IDs for this assessment
        $ptkIds = Ptk::where('assessment_id', $assessment->id)->pluck('id', 'id');

        foreach ($ptkIds as $ptkId) {
            $realisasi = $request->input("pts_realisasi_$ptkId");
            $efektifitas = $request->input("pts_efektifitas_$ptkId");
            $status = $request->input("pts_status_$ptkId");

            // Only save if at least one field has data
            if ($realisasi || $efektifitas || $status) {
                Pts::updateOrCreate(
                    ['ptk_id' => $ptkId],
                    [
                        'realisasi' => $realisasi,
                        'efektifitas' => $efektifitas,
                        'status' => $status ?? 'Open',
                    ]
                );
            }
        }

        return back()->with('success', 'PTS data saved successfully.');
    }
}
