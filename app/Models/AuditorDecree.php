<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditorDecree extends Model
{
    protected $fillable = [
        'accreditation_year_id',
        'period_label',
        'decree_number',
        'decree_date',
        'period_start',
        'period_end',
        'file_path',
        'original_name',
        'mime_type',
        'size',
        'is_active',
    ];

    protected $casts = [
        'decree_date' => 'date',
        'period_start' => 'date',
        'period_end' => 'date',
        'is_active' => 'boolean',
    ];

    public function year(): BelongsTo
    {
        return $this->belongsTo(AccreditationYear::class, 'accreditation_year_id');
    }
}
