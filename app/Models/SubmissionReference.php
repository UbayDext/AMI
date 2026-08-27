<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubmissionReference extends Model
{
    protected $fillable = ['submission_id', 'ami_question_id', 'title', 'url', 'notes', 'added_by'];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(AmiSubmission::class, 'submission_id');
    }

    public function amiQuestion(): BelongsTo
    {
        return $this->belongsTo(AmiChecklistQuestion::class, 'ami_question_id');
    }

    public function addedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by');
    }
}
