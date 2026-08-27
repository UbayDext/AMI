<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FoskCriteria extends Model
{
    protected $table = 'fosk_criteria';

    protected $fillable = ['code', 'name', 'description', 'sort_order'];

    public function documents(): HasMany
    {
        return $this->hasMany(FoskDocument::class, 'criteria_id');
    }
}
