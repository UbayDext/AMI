<?php

namespace App\Http\Controllers;

use App\Models\RoleRequest;
use App\Models\Standard;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleRequestController extends Controller
{
    /** Halaman form pengajuan standar */
    public function create(Request $request)
    {
        $this->authorize('create', RoleRequest::class);

        $user = $request->user();
        $existing = $user->roleRequests()->latest()->first();

        // Standar yang belum dipegang user ini
        $assignedCodes = $user->roles->pluck('name');
        $standards = Standard::orderByRaw('LENGTH(code), code')
            ->whereNotIn('code', $assignedCodes)
            ->get();

        // Standar yang sudah ada evidence-nya (dibuat oleh user ini), untuk pre-select
        $availableCodes = $standards->pluck('code');
        $evidenceCodes = \App\Models\PreparationStage::where('created_by', $user->id)
            ->whereNotNull('standard_id')
            ->with('standard:id,code')
            ->get()
            ->pluck('standard.code')
            ->filter()
            ->unique()
            ->intersect($availableCodes)
            ->values()
            ->toArray();

        return view('role-requests.create', compact('existing', 'standards', 'evidenceCodes'));
    }

    /** Simpan pengajuan standar baru */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', RoleRequest::class);

        $validCodes = Standard::pluck('code');

        $data = $request->validate([
            'requested_roles'   => ['required', 'array', 'min:1'],
            'requested_roles.*' => ['string', Rule::in($validCodes)],
            'reason'            => ['nullable', 'string', 'max:1000'],
        ]);

        $user = $request->user();
        $currentCount = $user->roles->whereIn('name', $validCodes)->count();
        $addCount     = count($data['requested_roles']);

        if ($currentCount + $addCount > 20) {
            $remaining = 20 - $currentCount;
            return back()->withErrors(['requested_roles' => "Anda hanya dapat menambah {$remaining} standar lagi (batas 20)."]);
        }

        $user->roleRequests()->create([
            'requested_role' => $data['requested_roles'],
            'reason'         => $data['reason'] ?? null,
            'status'         => 'pending',
        ]);

        return redirect()->route('role-requests.create')
            ->with('success', 'Permintaan standar berhasil diajukan. Silakan tunggu persetujuan admin.');
    }

    /** Batalkan request pending milik sendiri */
    public function destroy(RoleRequest $roleRequest): RedirectResponse
    {
        $this->authorize('delete', $roleRequest);

        $roleRequest->delete();

        return redirect()->route('role-requests.create')
            ->with('success', 'Permintaan berhasil dibatalkan.');
    }
}
