<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pts extends Model
{
    protected $fillable = [
        'ptk_id',
        'realisasi',
        'efektifitas',
        'status',
    ];

    public function ptk()
    {
        return $this->belongsTo(Ptk::class);
    }
}
