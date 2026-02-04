<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AuditArea extends Model
{
    protected $fillable = ['name', 'code'];

    public function findings(): HasMany
    {
        return $this->hasMany(Finding::class);
    }
}
