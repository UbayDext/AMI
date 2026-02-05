<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    protected $fillable = [
        'standard_id','category_id','label','refrence','type','is_required','sort_order','is_active'
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function standard(): BelongsTo
    {
        return $this->belongsTo(Standard::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(QuestionOption::class)->orderBy('sort_order');
    }
     public function category(): BelongsTo
    {
        return $this->belongsTo(QuestionCategory::class, 'category_id');
    }
}
