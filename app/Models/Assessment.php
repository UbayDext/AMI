<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assessment extends Model
{
    protected $fillable = [
        'accreditation_year_id',
        'assessor_id',
        'unit_name',
        'status',
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

    public function questions()
{
    return $this->belongsToMany(
        \App\Models\Question::class,
        'assessment_answers',   // nama tabel pivot/jawaban
        'assessment_id',
        'question_id'
    )->distinct();
}

    public function ptks()
{
    return $this->hasMany(\App\Models\Ptk::class);
}


}
