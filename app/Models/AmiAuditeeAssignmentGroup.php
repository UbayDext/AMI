<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AmiAuditeeAssignmentGroup extends Model
{
    protected $fillable = [
        'cycle_id', 'standard_id', 'prodi_id', 'assignment_mode',
        'can_create', 'can_edit', 'assigned_by', 'assigned_at',
    ];

    protected $casts = [
        'can_create' => 'boolean', 'can_edit' => 'boolean', 'assigned_at' => 'datetime',
    ];

    public function cycle(): BelongsTo { return $this->belongsTo(AmiCycle::class); }
    public function standard(): BelongsTo { return $this->belongsTo(Standard::class); }
    public function prodi(): BelongsTo { return $this->belongsTo(Prodi::class); }
    public function submissions(): HasMany { return $this->hasMany(AmiSubmission::class, 'assignment_group_id'); }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'ami_auditee_assignment_members', 'assignment_group_id', 'user_id')
            ->withPivot(['can_edit', 'assigned_by', 'joined_at'])->withTimestamps();
    }
}
