<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AmiReview extends Model
{
    protected $fillable = ['submission_id', 'reviewer_id', 'decree_id', 'status', 'started_at', 'completed_at'];

    protected $casts = [
        'started_at'   => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(AmiSubmission::class, 'submission_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function decree(): BelongsTo
    {
        return $this->belongsTo(AuditorDecree::class, 'decree_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(ReviewAnswer::class, 'review_id');
    }

    public function findings(): HasMany
    {
        return $this->hasMany(ReviewFinding::class, 'review_id');
    }
}
