<?php

namespace App\Policies;

use App\Models\RoleRequest;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class RoleRequestPolicy
{
    /** Admin bypass semua gate */
    public function before(User $user, string $_ability): bool|null
    {
        return $user->hasRole('admin') ? true : null;
    }

    /** Hanya auditee yang boleh membuat request standar, belum 20 standar, dan tidak ada pending */
    public function create(User $user): Response
    {
        if (! $user->hasRole('auditee')) {
            return Response::deny('Hanya auditee yang dapat mengajukan permintaan standar.');
        }

        $standardCodes = \App\Models\Standard::pluck('code');
        $currentCount  = $user->roles->whereIn('name', $standardCodes)->count();

        if ($currentCount >= 20) {
            return Response::deny('Anda sudah mengelola 20 standar (batas maksimal).');
        }

        $hasPending = $user->roleRequests()->where('status', 'pending')->exists();

        return $hasPending
            ? Response::deny('Anda sudah memiliki permintaan yang sedang menunggu persetujuan.')
            : Response::allow();
    }

    /** Hanya pemilik yang bisa melihat request miliknya */
    public function view(User $user, RoleRequest $roleRequest): bool
    {
        return $user->id === $roleRequest->user_id;
    }

    /** Hanya request pending milik sendiri yang bisa dibatalkan */
    public function delete(User $user, RoleRequest $roleRequest): Response
    {
        if ($user->id !== $roleRequest->user_id) {
            return Response::deny('Bukan milik Anda.');
        }

        return $roleRequest->isPending()
            ? Response::allow()
            : Response::deny('Hanya request dengan status pending yang dapat dibatalkan.');
    }
}
