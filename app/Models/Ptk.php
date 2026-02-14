<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ptk extends Model
{
    protected $fillable = [
        'assessment_id',
        'question_id',
        'standard_id',
        'audit_area_ids',
        'code',
        'sequence',
        'condition_desc',
        'root_cause',
        'impact',
        'recommendation',
        'category',
        'corrective_plan',
        'due_date',
    ];

    protected $casts = [
        'due_date' => 'date',
        'audit_area_ids' => 'array',
    ];

    public function getAuditAreaNamesAttribute()
    {
        if (empty($this->audit_area_ids)) return '-';
        return \App\Models\AuditArea::whereIn('id', $this->audit_area_ids)->pluck('name')->join(', ');
    }

    public function standard()
    {
        return $this->belongsTo(\App\Models\Standard::class);
    }

    public function pts()
    {
        return $this->hasOne(\App\Models\Pts::class);
    }
}
