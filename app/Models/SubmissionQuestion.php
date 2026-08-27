<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubmissionQuestion extends Model
{
    protected $fillable = ['submission_id', 'bidang', 'question_text', 'sort_order'];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(AmiSubmission::class, 'submission_id');
    }

    public function evidences(): HasMany
    {
        return $this->hasMany(SubmissionEvidence::class, 'question_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(ReviewAnswer::class, 'question_id');
    }
}
