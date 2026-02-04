<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PreparationStage extends Model
{
    protected $fillable = ['accreditation_year_id','title','description','sort_order','is_active'];

    public function tasks()
    {
        return $this->hasMany(PreparationTask::class, 'stage_id');
    }
}
