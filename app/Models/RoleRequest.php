<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoleRequest extends Model
{
    protected $fillable = [
        'user_id',
        'requested_role',
        'reason',
        'status',
        'reviewed_by',
        'review_note',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    /** Baca: selalu return array meski data lama menyimpan string tunggal */
    public function getRequestedRoleAttribute(mixed $value): array
    {
        $decoded = json_decode($value, true);
        return is_array($decoded) ? $decoded : ($value ? [$value] : []);
    }

    /** Tulis: selalu simpan sebagai JSON array */
    public function setRequestedRoleAttribute(mixed $value): void
    {
        $this->attributes['requested_role'] = json_encode(array_values((array) $value));
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function isPending(): bool { return $this->status === 'pending'; }
    public function isApproved(): bool { return $this->status === 'approved'; }
    public function isRejected(): bool { return $this->status === 'rejected'; }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'pending'  => 'Menunggu',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            default    => $this->status,
        };
    }

    public function roleLabel(): string
    {
        $codes = $this->requested_role;
        if (empty($codes)) return '—';
        $standards = \App\Models\Standard::whereIn('code', $codes)->pluck('name', 'code');
        return collect($codes)->map(fn($c) => $standards->has($c) ? "{$c} – {$standards[$c]}" : $c)->implode(', ');
    }
}
