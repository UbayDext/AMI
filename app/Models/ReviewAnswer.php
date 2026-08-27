<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReviewAnswer extends Model
{
    protected $fillable = ['review_id', 'question_id', 'status', 'notes'];

    public function review(): BelongsTo
    {
        return $this->belongsTo(AmiReview::class, 'review_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(AmiChecklistQuestion::class, 'question_id');
    }
}
