<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PreparationTask extends Model
{
    protected $fillable = [
        'stage_id',
        'prodi_id',
        'title',
        'description',
        'link',
        'category',
        'is_required',
        'sort_order',
        'created_by',
    ];

    protected $casts = [
        'is_required' => 'boolean',
    ];

    public const CATEGORIES = [
        'kebijakan',
        'pelaksanaan',
        'evaluasi',
        'pendukung_digital',
    ];

    public function stage(): BelongsTo
    {
        return $this->belongsTo(PreparationStage::class, 'stage_id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(PreparationTaskFile::class, 'task_id');
    }

    public function filesForProdi(int $prodiId): HasMany
    {
        return $this->hasMany(PreparationTaskFile::class, 'task_id')
            ->where('prodi_id', $prodiId);
    }

    public function progress(): HasMany
    {
        return $this->hasMany(ProdiTaskProgress::class, 'task_id');
    }

    public function progressForProdi(int $prodiId): ?ProdiTaskProgress
    {
        return $this->progress()->where('prodi_id', $prodiId)->first();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function prodi(): BelongsTo
    {
        return $this->belongsTo(Prodi::class, 'prodi_id');
    }

    public function prodis(): BelongsToMany
    {
        return $this->belongsToMany(Prodi::class, 'preparation_task_prodi')->withTimestamps();
    }
}
