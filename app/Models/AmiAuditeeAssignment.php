<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AmiAuditeeAssignment extends Model
{
    protected $fillable = [
        'cycle_id', 'user_id', 'standard_id', 'prodi_scope',
        'can_create', 'can_edit', 'assigned_by', 'assigned_at',
    ];

    protected $casts = [
        'can_create' => 'boolean',
        'can_edit' => 'boolean',
        'assigned_at' => 'datetime',
    ];

    public function cycle(): BelongsTo
    {
        return $this->belongsTo(AmiCycle::class, 'cycle_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function standard(): BelongsTo
    {
        return $this->belongsTo(Standard::class);
    }

    public function assigner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(AmiSubmission::class, 'assignment_id');
    }

    public function prodis(): BelongsToMany
    {
        return $this->belongsToMany(Prodi::class, 'ami_assignment_prodis', 'assignment_id', 'prodi_id')
            ->withTimestamps();
    }

    public function coversProdi(int $prodiId): bool
    {
        return $this->prodi_scope === 'all' || $this->prodis()->whereKey($prodiId)->exists();
    }
}
