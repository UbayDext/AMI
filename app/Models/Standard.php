<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Standard extends Model
{
    protected $fillable = ['code', 'name'];

    public function preparationStages(): HasMany
    {
        return $this->hasMany(PreparationStage::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function findings(): HasMany
    {
        return $this->hasMany(Finding::class);
    }

    public function auditAreas(): BelongsToMany
    {
        return $this->belongsToMany(AuditArea::class, 'standard_audit_areas')
            ->withPivot(['auditee', 'sort_order'])->withTimestamps();
    }
}
