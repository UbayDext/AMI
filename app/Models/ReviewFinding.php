<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReviewFinding extends Model
{
    protected $fillable = [
        'review_id', 'question_id', 'audit_area_ids', 'category', 'condition_desc',
        'root_cause', 'impact', 'recommendation', 'corrective_plan',
        'due_date', 'tl_status', 'pic', 'realisasi', 'efektifitas',
    ];

    protected $casts = [
        'due_date'       => 'date',
        'audit_area_ids' => 'array',
    ];

    public function review(): BelongsTo
    {
        return $this->belongsTo(AmiReview::class, 'review_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(AmiChecklistQuestion::class, 'question_id');
    }
}
