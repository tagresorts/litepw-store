<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GroupPermission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'group_id',
        'effect',
        'level',
        'can_export',
        'can_share',
    ];

    protected $casts = [
        'can_export' => 'boolean',
        'can_share' => 'boolean',
    ];

    /**
     * Get the user that owns the permission.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the group that the permission applies to.
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * Check if permission allows reading.
     */
    public function canRead(): bool
    {
        return in_array($this->level, ['read', 'write', 'admin']) && $this->effect === 'allow';
    }

    /**
     * Check if permission allows writing.
     */
    public function canWrite(): bool
    {
        return in_array($this->level, ['write', 'admin']) && $this->effect === 'allow';
    }

    /**
     * Check if permission is admin level.
     */
    public function isAdmin(): bool
    {
        return $this->level === 'admin' && $this->effect === 'allow';
    }
}
