<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'parent_id',
        'path',
        'level',
        'sort_order',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the parent group.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'parent_id');
    }

    /**
     * Get the child groups.
     */
    public function children(): HasMany
    {
        return $this->hasMany(Group::class, 'parent_id')->orderBy('sort_order');
    }

    /**
     * Get all descendant groups (recursive).
     */
    public function descendants(): HasMany
    {
        return $this->hasMany(Group::class, 'parent_id')->with('descendants');
    }

    /**
     * Get the user who created this group.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the credentials in this group.
     */
    public function credentials(): HasMany
    {
        return $this->hasMany(Credential::class);
    }

    /**
     * Get the permissions for this group.
     */
    public function permissions(): HasMany
    {
        return $this->hasMany(GroupPermission::class);
    }

    /**
     * Get the full path of the group (e.g., "System App > Prod > Web App").
     */
    public function getFullPathAttribute(): string
    {
        if ($this->parent) {
            return $this->parent->full_path . ' > ' . $this->name;
        }
        return $this->name;
    }

    /**
     * Update the path field when saving.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($group) {
            if ($group->parent_id) {
                $parent = self::find($group->parent_id);
                $group->level = $parent->level + 1;
                $group->path = $parent->path ? $parent->path . '/' . $parent->id : (string)$parent->id;
            } else {
                $group->level = 0;
                $group->path = null;
            }
        });

        static::deleting(function ($group) {
            // Delete all descendants when deleting a group
            $group->descendants()->delete();
        });
    }

    /**
     * Scope to get root groups (no parent).
     */
    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope to get active groups.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get all ancestors of this group.
     */
    public function getAncestors()
    {
        $ancestors = collect();
        $current = $this->parent;
        
        while ($current) {
            $ancestors->prepend($current);
            $current = $current->parent;
        }
        
        return $ancestors;
    }

    /**
     * Check if this group is an ancestor of another group.
     */
    public function isAncestorOf(Group $group): bool
    {
        return $group->getAncestors()->contains('id', $this->id);
    }

    /**
     * Check if this group is a descendant of another group.
     */
    public function isDescendantOf(Group $group): bool
    {
        return $group->isAncestorOf($this);
    }
}
