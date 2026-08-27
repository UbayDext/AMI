<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PreparationTaskFile extends Model
{
    protected $fillable = [
        'task_id',
        'prodi_id',
        'uploaded_by',
        'file_path',
        'original_name',
        'mime_type',
        'size',
        'link_url',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(PreparationTask::class, 'task_id');
    }

    public function prodi(): BelongsTo
    {
        return $this->belongsTo(Prodi::class, 'prodi_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
