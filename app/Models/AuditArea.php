<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AuditArea extends Model
{
    protected $fillable = ['name', 'code', 'is_active', 'sort_order'];

    protected $casts = ['is_active' => 'boolean'];

    public function findings(): HasMany
    {
        return $this->hasMany(Finding::class);
    }

    public function standards(): BelongsToMany
    {
        return $this->belongsToMany(Standard::class, 'standard_audit_areas')
            ->withPivot(['auditee', 'sort_order'])->withTimestamps();
    }
}
