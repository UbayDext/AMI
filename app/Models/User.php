<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, HasRoles, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'avatar_path',
        'password',
        'is_active',
        'is_blocked',
        'failed_login_attempts',
        'last_failed_login_at',
        'login_start_time',
        'login_end_time',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'is_blocked' => 'boolean',
            'last_failed_login_at' => 'datetime',
            'login_start_time' => 'datetime:H:i',
            'login_end_time' => 'datetime:H:i',
        ];
    }

    public function roleRequests(): HasMany
    {
        return $this->hasMany(RoleRequest::class);
    }

    public function pendingRoleRequest(): HasOne
    {
        return $this->hasOne(RoleRequest::class)->where('status', 'pending')->latestOfMany();
    }

    public function amiAssignments(): HasMany
    {
        return $this->hasMany(AmiAuditeeAssignment::class);
    }

    public function ownedAmiSubmissions(): HasMany
    {
        return $this->hasMany(AmiSubmission::class, 'owner_id');
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return $this->hasRole('admin');
        }

        return false;
    }
}
