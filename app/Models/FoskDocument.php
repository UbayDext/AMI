<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FoskDocument extends Model
{
    protected $fillable = [
        'criteria_id',
        'accreditation_year_id',
        'type',
        'title',
        'description',
        'data_value',
        'pic',
        'status',
        'sort_order',
        'created_by',
    ];

    public function criteria(): BelongsTo
    {
        return $this->belongsTo(FoskCriteria::class, 'criteria_id');
    }

    public function accreditationYear(): BelongsTo
    {
        return $this->belongsTo(AccreditationYear::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function evidences(): HasMany
    {
        return $this->hasMany(FoskEvidence::class, 'document_id');
    }
}
