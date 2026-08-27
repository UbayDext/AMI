<?php

namespace App\Policies;

use App\Models\Assessment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

/**
 * Policy untuk model Assessment.
 *
 * Hierarki akses:
 *   admin   → semua tindakan (di-bypass via before())
 *   auditor → hanya assessment yang dimilikinya (assessor_id)
 *   auditee → tidak punya akses langsung ke Assessment
 */
class AssessmentPolicy
{
    /**
     * Super-admin bypass: admin melewati semua gate tanpa cek lebih lanjut.
     * Kembalikan null agar role lain tetap diproses oleh method di bawah.
     */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        return null;
    }

    /**
     * viewAny — melihat daftar assessment.
     * Auditor hanya butuh terautentikasi; data di-filter di controller/query scope.
     */
    public function viewAny(User $user): bool
    {
        // Auditor boleh melihat daftar (hanya miliknya, di-scope di controller)
        return $user->hasRole('auditor');
    }

    /**
     * view — melihat detail satu assessment.
     * Hanya pemilik assessment (assessor_id) yang boleh akses.
     */
    public function view(User $user, Assessment $assessment): Response
    {
        return $user->id === $assessment->assessor_id
            ? Response::allow()
            : Response::deny('Anda tidak memiliki akses ke assessment ini.');
    }

    /**
     * create — membuat assessment baru.
     * Hanya admin yang boleh membuat assignment (sudah di-bypass via before()).
     * Method ini sebagai explicit fallback.
     */
    public function create(User $user): Response
    {
        // Auditor tidak bisa membuat assessment sendiri
        return Response::deny('Hanya admin yang dapat membuat assessment baru.');
    }

    /**
     * update — mengisi / menyimpan jawaban assessment.
     * Pemilik boleh update HANYA jika status belum 'submitted'.
     */
    public function update(User $user, Assessment $assessment): Response
    {
        // Bukan pemilik → tolak
        if ($user->id !== $assessment->assessor_id) {
            return Response::deny('Anda bukan assessor yang ditugaskan untuk assessment ini.');
        }

        // Assessment yang sudah di-submit tidak bisa diubah lagi
        if ($assessment->status === 'submitted') {
            return Response::deny('Assessment ini sudah disubmit dan tidak dapat diubah.');
        }

        return Response::allow();
    }

    /**
     * delete — menghapus assessment.
     * Hanya admin (sudah di-bypass via before()). Fallback untuk non-admin.
     */
    public function delete(User $user, Assessment $assessment): Response
    {
        return Response::deny('Hanya admin yang dapat menghapus assessment.');
    }

    /**
     * restore — memulihkan dari soft-delete.
     * Model Assessment tidak menggunakan SoftDeletes, selalu ditolak.
     */
    public function restore(User $user, Assessment $assessment): Response
    {
        return Response::deny('Assessment tidak mendukung restore.');
    }

    /**
     * forceDelete — hapus permanen dari database.
     * Model Assessment tidak menggunakan SoftDeletes, selalu ditolak.
     */
    public function forceDelete(User $user, Assessment $assessment): Response
    {
        return Response::deny('Assessment tidak mendukung force delete.');
    }
}
