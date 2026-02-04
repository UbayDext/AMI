<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ptk extends Model
{
    protected $fillable = [
        'assessment_id','question_id','standard_id','audit_area_id',
        'code','sequence',
        'condition_desc','root_cause','impact','recommendation',
        'category','corrective_plan','due_date',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function auditArea()
    {
        return $this->belongsTo(\App\Models\AuditArea::class);
    }

    public function standard()
    {
        return $this->belongsTo(\App\Models\Standard::class);
    }
}
