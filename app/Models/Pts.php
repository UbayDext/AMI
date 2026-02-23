<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pts extends Model
{
    protected $fillable = [
        'ptk_id',
        'realisasi',
        'efektifitas',
        'status',
    ];

    public function ptk(): BelongsTo
    {
        return $this->belongsTo(Ptk::class);
    }
}
