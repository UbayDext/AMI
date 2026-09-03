<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OnboardingProgress extends Model
{
    protected $table = 'onboarding_progress';
    protected $fillable = ['user_id', 'onboarding_key', 'version', 'current_step', 'status', 'started_at', 'completed_at', 'last_seen_at'];
    protected $casts = ['started_at' => 'datetime', 'completed_at' => 'datetime', 'last_seen_at' => 'datetime'];
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
