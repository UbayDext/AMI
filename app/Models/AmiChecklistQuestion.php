<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AmiChecklistQuestion extends Model
{
    protected $fillable = [
        'standard_code', 'standard_name', 'bidang',
        'auditi', 'question_number', 'question_text',
    ];

    public function references(): HasMany
    {
        return $this->hasMany(SubmissionReference::class, 'ami_question_id');
    }

    public function evidences(): HasMany
    {
        return $this->hasMany(SubmissionEvidence::class, 'ami_question_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(ReviewAnswer::class, 'question_id');
    }

    public function findings(): HasMany
    {
        return $this->hasMany(ReviewFinding::class, 'question_id');
    }
}
