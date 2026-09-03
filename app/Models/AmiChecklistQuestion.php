<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AmiChecklistQuestion extends Model
{
    protected $fillable = [
        'standard_code', 'standard_name', 'bidang',
        'auditi', 'question_number', 'question_text', 'standard_id', 'audit_area_id', 'import_batch_id',
        'reference', 'is_required', 'is_active', 'sort_order', 'source_hash', 'created_by',
    ];

    protected $casts = ['is_required' => 'boolean', 'is_active' => 'boolean'];

    public function auditArea(): BelongsTo { return $this->belongsTo(AuditArea::class); }

    public function prodis(): BelongsToMany
    {
        return $this->belongsToMany(Prodi::class, 'ami_checklist_question_prodi', 'question_id', 'prodi_id')->withTimestamps();
    }

    public function references(): HasMany
    {
        return $this->hasMany(SubmissionReference::class, 'ami_question_id');
    }

    public function evidences(): HasMany
    {
        return $this->hasMany(SubmissionEvidence::class, 'ami_question_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(ReviewAnswer::class, 'question_id');
    }

    public function findings(): HasMany
    {
        return $this->hasMany(ReviewFinding::class, 'question_id');
    }
}
