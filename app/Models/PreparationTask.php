<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PreparationTask extends Model
{
    protected $fillable = [
        'stage_id','title','description','is_required','sort_order',
        'is_done','done_at','done_by',
    ];

    protected $casts = [
        'is_done' => 'boolean',
        'done_at' => 'datetime',
        'is_required' => 'boolean',
    ];

    public function stage()
    {
        return $this->belongsTo(PreparationStage::class, 'stage_id');
    }

    public function files()
    {
        return $this->hasMany(PreparationTaskFile::class, 'task_id');
    }

    public function doneBy()
    {
        return $this->belongsTo(User::class, 'done_by');
    }
}
