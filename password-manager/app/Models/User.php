<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
        'last_login_at',
        'last_login_ip',
        'preferences',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
            'last_login_at' => 'datetime',
            'is_active' => 'boolean',
            'preferences' => 'array',
            'two_factor_recovery_codes' => 'array',
        ];
    }

    /**
     * Get the roles for the user.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    /**
     * Get the groups created by the user.
     */
    public function createdGroups(): HasMany
    {
        return $this->hasMany(Group::class, 'created_by');
    }

    /**
     * Get the credentials created by the user.
     */
    public function createdCredentials(): HasMany
    {
        return $this->hasMany(Credential::class, 'created_by');
    }

    /**
     * Get the group permissions for the user.
     */
    public function groupPermissions(): HasMany
    {
        return $this->hasMany(GroupPermission::class);
    }

    /**
     * Get the dashboard widgets for the user.
     */
    public function dashboardWidgets(): HasMany
    {
        return $this->hasMany(DashboardWidget::class);
    }

    /**
     * Get the audit logs for the user.
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    /**
     * Check if user has a specific role.
     */
    public function hasRole(string $roleName): bool
    {
        return $this->roles()->where('name', $roleName)->exists();
    }

    /**
     * Check if user has permission for a specific group.
     */
    public function hasGroupPermission(int $groupId, string $permission): bool
    {
        return $this->groupPermissions()
            ->where('group_id', $groupId)
            ->where('permission_type', $permission)
            ->exists();
    }

    /**
     * Check if user has 2FA enabled.
     */
    public function hasTwoFactorEnabled(): bool
    {
        return !is_null($this->two_factor_confirmed_at);
    }
}
