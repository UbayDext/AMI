<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Ptk extends Model
{
    protected $fillable = [
        'assessment_id',
        'question_id',
        'standard_id',
        'audit_area_ids',
        'code',
        'sequence',
        'category',
        'condition_desc',
        'root_cause',
        'impact',
        'recommendation',
        'corrective_plan',
        'start_date',
        'end_date',
        'due_date',
        'realisasi',
        'efektifitas',
        'tl_status',
    ];

    protected $casts = [
        'audit_area_ids' => 'array',
        'start_date'     => 'date',
        'end_date'       => 'date',
        'due_date'       => 'date',
    ];

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function standard(): BelongsTo
    {
        return $this->belongsTo(Standard::class);
    }

    public function pts(): HasOne
    {
        return $this->hasOne(Pts::class);
    }

    public function getAuditAreaNamesAttribute(): string
    {
        if (empty($this->audit_area_ids)) {
            return '-';
        }

        return AuditArea::whereIn('id', $this->audit_area_ids)
            ->pluck('name')
            ->join(', ');
    }
}
