<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DashboardWidget extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'widget_type',
        'title',
        'config',
        'position_x',
        'position_y',
        'width',
        'height',
        'is_visible',
        'sort_order',
    ];

    protected $casts = [
        'config' => 'array',
        'is_visible' => 'boolean',
    ];

    /**
     * Get the user that owns the widget.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get visible widgets.
     */
    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    /**
     * Scope to order by sort order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    /**
     * Get default widget configuration based on type.
     */
    public function getDefaultConfig(): array
    {
        return match ($this->widget_type) {
            'activity_log' => [
                'limit' => 10,
                'show_actions' => ['view', 'create', 'update', 'delete'],
            ],
            'quick_access' => [
                'show_favorites' => true,
                'show_recent' => true,
                'limit' => 8,
            ],
            'password_health' => [
                'show_weak' => true,
                'show_expired' => true,
                'show_expiring' => true,
            ],
            'recent_updates' => [
                'limit' => 5,
                'days' => 7,
            ],
            default => [],
        };
    }

    /**
     * Get merged configuration (default + user config).
     */
    public function getMergedConfig(): array
    {
        return array_merge($this->getDefaultConfig(), $this->config ?? []);
    }
}
