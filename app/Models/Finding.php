<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Finding extends Model
{
    protected $fillable = ['assessment_id', 'question_id', 'standard_id', 'audit_area_id', 'sequence', 'code', 'title', 'description', 'severity', 'condition_desc', 'root_cause', 'impact', 'recommendation', 'category', 'corrective_plan', 'due_date'];

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function standard(): BelongsTo
    {
        return $this->belongsTo(Standard::class);
    }

    public function auditArea(): BelongsTo
    {
        return $this->belongsTo(AuditArea::class);
    }
}
