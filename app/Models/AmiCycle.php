<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AmiCycle extends Model
{
    protected $fillable = ['accreditation_year_id', 'title', 'period_start', 'period_end', 'status'];

    protected $casts = [
        'period_start' => 'date',
        'period_end'   => 'date',
    ];

    public function accreditationYear(): BelongsTo
    {
        return $this->belongsTo(AccreditationYear::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(AmiSubmission::class, 'cycle_id');
    }
}
