<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Prodi extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'jenjang',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function taskProgress(): HasMany
    {
        return $this->hasMany(ProdiTaskProgress::class, 'prodi_id');
    }

    public function taskFiles(): HasMany
    {
        return $this->hasMany(PreparationTaskFile::class, 'prodi_id');
    }

    public function preparationStages(): HasMany
    {
        return $this->hasMany(PreparationStage::class);
    }

    public function preparationTasks(): HasMany
    {
        return $this->hasMany(PreparationTask::class);
    }

    public function assignedPreparationTasks(): BelongsToMany
    {
        return $this->belongsToMany(PreparationTask::class, 'preparation_task_prodi')->withTimestamps();
    }

    public function amiSubmissions(): HasMany
    {
        return $this->hasMany(AmiSubmission::class);
    }
}
