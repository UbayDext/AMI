<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AmiSubmissionAnswer extends Model
{
    protected $fillable = ['submission_id', 'question_id', 'status', 'notes', 'answered_by'];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(AmiSubmission::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(AmiChecklistQuestion::class);
    }

    public function answeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'answered_by');
    }
}
