<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Standard extends Model
{
    protected $fillable = ['code', 'name'];

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function findings(): HasMany
    {
        return $this->hasMany(Finding::class);
    }
}
