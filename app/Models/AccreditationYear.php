<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AccreditationYear extends Model
{
    protected $fillable = ['year'];

    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class);
    }
}
