<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProdiTaskProgress extends Model
{
    protected $table = 'prodi_task_progress';

    protected $fillable = [
        'task_id',
        'prodi_id',
        'is_applicable',
        'is_done',
        'done_at',
        'done_by',
    ];

    protected $casts = [
        'is_applicable' => 'boolean',
        'is_done' => 'boolean',
        'done_at' => 'datetime',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(PreparationTask::class, 'task_id');
    }

    public function prodi(): BelongsTo
    {
        return $this->belongsTo(Prodi::class, 'prodi_id');
    }

    public function doneBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'done_by');
    }
}
