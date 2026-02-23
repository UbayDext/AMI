<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assessment extends Model
{
    protected $fillable = [
        'accreditation_year_id',
        'assessor_id',
        'unit_name',
        'status',
        'submitted_standards',
    ];

    protected $casts = [
        'submitted_standards' => 'array',
    ];

    public function accreditationYear(): BelongsTo
    {
        return $this->belongsTo(AccreditationYear::class);
    }

    public function assessor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assessor_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    public function findings(): HasMany
    {
        return $this->hasMany(Finding::class);
    }

    public function ptks(): HasMany
    {
        return $this->hasMany(Ptk::class);
    }

    public function questions(): BelongsToMany
    {
        return $this->belongsToMany(
            Question::class,
            'assessment_answers',
            'assessment_id',
            'question_id'
        )->distinct();
    }
}
