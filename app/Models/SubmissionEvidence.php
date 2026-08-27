<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubmissionEvidence extends Model
{
    protected $table = 'submission_evidences';

    protected $fillable = [
        'submission_id',
        'ami_question_id',
        'preparation_task_id',
        'category',
        'notes',
        'added_by',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(AmiSubmission::class, 'submission_id');
    }

    public function amiQuestion(): BelongsTo
    {
        return $this->belongsTo(AmiChecklistQuestion::class, 'ami_question_id');
    }

    public function preparationTask(): BelongsTo
    {
        return $this->belongsTo(PreparationTask::class, 'preparation_task_id');
    }

    public function addedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->preparationTask?->title ?? "Dokumen #{$this->preparation_task_id}";
    }

    public function getDisplayUrlAttribute(): ?string
    {
        return $this->preparationTask?->link;
    }
}
