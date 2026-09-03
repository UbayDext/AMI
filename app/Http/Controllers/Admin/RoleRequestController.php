<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RoleRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RoleRequestController extends Controller
{
    /** Daftar semua request (default: pending dulu) */
    public function index(Request $request)
    {
        $status = in_array($request->get('status'), ['pending', 'approved', 'rejected', 'all'], true)
            ? $request->get('status')
            : 'pending';
        $search = mb_substr($request->string('search')->trim()->toString(), 0, 100);

        $requests = RoleRequest::with(['user.roles', 'reviewer'])
            ->when($status !== 'all', fn($q) => $q->where('status', $status))
            ->when($search, fn ($q) => $q->whereHas('user', fn ($user) => $user
                ->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $counts = [
            'pending'  => RoleRequest::where('status', 'pending')->count(),
            'approved' => RoleRequest::where('status', 'approved')->count(),
            'rejected' => RoleRequest::where('status', 'rejected')->count(),
        ];

        return view('admin.role-requests.index', compact('requests', 'counts', 'status', 'search'));
    }

    /** Setujui permintaan: assign role + aktifkan user */
    public function approve(Request $request, RoleRequest $roleRequest): RedirectResponse
    {
        if (! $roleRequest->isPending()) {
            return back()->with('error', 'Permintaan ini sudah diproses sebelumnya.');
        }

        $data = $request->validate([
            'review_note' => ['nullable', 'string', 'max:500'],
        ]);

        // Assign semua standar yang diminta (pertahankan role lama kecuali admin)
        $user = $roleRequest->user;
        $existingRoles  = $user->roles->pluck('name')->reject(fn($r) => $r === 'admin')->toArray();
        $requestedCodes = $roleRequest->requested_role; // sudah array via accessor
        $user->syncRoles(array_unique(array_merge($existingRoles, $requestedCodes)));

        // Aktifkan akun jika masih inactive
        if (! $user->is_active) {
            $user->update(['is_active' => true]);
        }

        $roleRequest->update([
            'status'      => 'approved',
            'reviewed_by' => auth()->user()->id,
            'review_note' => $data['review_note'] ?? null,
            'reviewed_at' => now(),
        ]);

        return back()->with('success', "Permintaan {$user->name} disetujui. Role '{$roleRequest->roleLabel()}' berhasil diberikan.");
    }

    /** Tolak permintaan */
    public function reject(Request $request, RoleRequest $roleRequest): RedirectResponse
    {
        if (! $roleRequest->isPending()) {
            return back()->with('error', 'Permintaan ini sudah diproses sebelumnya.');
        }

        $data = $request->validate([
            'review_note' => ['required', 'string', 'max:500'],
        ]);

        $roleRequest->update([
            'status'      => 'rejected',
            'reviewed_by' => auth()->user()->id,
            'review_note' => $data['review_note'],
            'reviewed_at' => now(),
        ]);

        return back()->with('success', "Permintaan {$roleRequest->user->name} ditolak.");
    }
}
