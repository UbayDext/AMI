<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AmiSubmission extends Model
{
    protected $fillable = [
        'cycle_id', 'prodi_id', 'standard_id',
        'owner_id', 'assignment_id', 'assignment_group_id', 'submitted_by', 'status', 'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    public function cycle(): BelongsTo
    {
        return $this->belongsTo(AmiCycle::class, 'cycle_id');
    }

    public function prodi(): BelongsTo
    {
        return $this->belongsTo(Prodi::class, 'prodi_id');
    }

    public function standard(): BelongsTo
    {
        return $this->belongsTo(Standard::class);
    }

    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(AmiAuditeeAssignment::class, 'assignment_id');
    }

    public function assignmentGroup(): BelongsTo
    {
        return $this->belongsTo(AmiAuditeeAssignmentGroup::class, 'assignment_group_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(AmiSubmissionAnswer::class, 'submission_id');
    }

    public function references(): HasMany
    {
        return $this->hasMany(SubmissionReference::class, 'submission_id');
    }

    public function evidences(): HasMany
    {
        return $this->hasMany(SubmissionEvidence::class, 'submission_id');
    }

    public function review(): HasOne
    {
        return $this->hasOne(AmiReview::class, 'submission_id');
    }
}
