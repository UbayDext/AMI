<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PreparationTaskFile extends Model
{
    protected $fillable = ['task_id','uploaded_by','file_path','original_name','mime_type','size'];

    public function task()
    {
        return $this->belongsTo(PreparationTask::class, 'task_id');
    }
}
