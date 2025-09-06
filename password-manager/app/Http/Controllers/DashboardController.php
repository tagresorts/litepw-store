<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Credential;
use App\Models\DashboardWidget;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();
        
        // Get user's dashboard widgets
        $widgets = $user->dashboardWidgets()
            ->visible()
            ->ordered()
            ->get()
            ->map(function ($widget) use ($user) {
                return [
                    'id' => $widget->id,
                    'type' => $widget->widget_type,
                    'title' => $widget->title,
                    'config' => $widget->getMergedConfig(),
                    'position' => [
                        'x' => $widget->position_x,
                        'y' => $widget->position_y,
                        'w' => $widget->width,
                        'h' => $widget->height,
                    ],
                    'data' => $this->getWidgetData($widget, $user),
                ];
            });

        // Get navigation tree (groups hierarchy)
        $navigationTree = $this->getNavigationTree($user);

        // Get dashboard stats
        $stats = $this->getDashboardStats($user);

        return Inertia::render('Dashboard', [
            'widgets' => $widgets,
            'navigationTree' => $navigationTree,
            'stats' => $stats,
        ]);
    }

    /**
     * Get data for a specific widget.
     */
    private function getWidgetData(DashboardWidget $widget, User $user): array
    {
        switch ($widget->widget_type) {
            case 'activity_log':
                return $this->getActivityLogData($widget, $user);
            
            case 'quick_access':
                return $this->getQuickAccessData($widget, $user);
            
            case 'password_health':
                return $this->getPasswordHealthData($widget, $user);
            
            case 'recent_updates':
                return $this->getRecentUpdatesData($widget, $user);
            
            default:
                return [];
        }
    }

    /**
     * Get activity log data for widget.
     */
    private function getActivityLogData(DashboardWidget $widget, User $user): array
    {
        $config = $widget->getMergedConfig();
        $limit = $config['limit'] ?? 10;
        $showActions = $config['show_actions'] ?? ['view', 'create', 'update', 'delete'];

        $logs = AuditLog::with('user')
            ->whereIn('action', $showActions)
            ->latest('created_at')
            ->limit($limit)
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'action' => $log->action,
                    'resource_type' => $log->resource_type,
                    'resource_name' => $log->resource_name,
                    'user' => $log->user ? $log->user->name : 'System',
                    'created_at' => $log->created_at->diffForHumans(),
                ];
            });

        return ['logs' => $logs];
    }

    /**
     * Get quick access data for widget.
     */
    private function getQuickAccessData(DashboardWidget $widget, User $user): array
    {
        $config = $widget->getMergedConfig();
        $limit = $config['limit'] ?? 8;

        $data = [];

        if ($config['show_favorites'] ?? true) {
            $favorites = Credential::with('group')
                ->favorites()
                ->limit($limit / 2)
                ->get()
                ->map(function ($credential) {
                    return [
                        'id' => $credential->id,
                        'title' => $credential->title,
                        'username' => $credential->username,
                        'group' => $credential->group->name,
                        'type' => 'favorite',
                    ];
                });
            $data['favorites'] = $favorites;
        }

        if ($config['show_recent'] ?? true) {
            $recent = Credential::with('group')
                ->orderBy('last_accessed_at', 'desc')
                ->limit($limit / 2)
                ->get()
                ->map(function ($credential) {
                    return [
                        'id' => $credential->id,
                        'title' => $credential->title,
                        'username' => $credential->username,
                        'group' => $credential->group->name,
                        'type' => 'recent',
                        'last_accessed' => $credential->last_accessed_at?->diffForHumans(),
                    ];
                });
            $data['recent'] = $recent;
        }

        return $data;
    }

    /**
     * Get password health data for widget.
     */
    private function getPasswordHealthData(DashboardWidget $widget, User $user): array
    {
        $config = $widget->getMergedConfig();
        $data = [];

        if ($config['show_weak'] ?? true) {
            $weakCount = Credential::get()
                ->filter(fn($credential) => $credential->getPasswordStrength() < 50)
                ->count();
            $data['weak'] = $weakCount;
        }

        if ($config['show_expired'] ?? true) {
            $expiredCount = Credential::expired()->count();
            $data['expired'] = $expiredCount;
        }

        if ($config['show_expiring'] ?? true) {
            $expiringCount = Credential::expiringSoon(30)->count();
            $data['expiring'] = $expiringCount;
        }

        $data['total'] = Credential::count();

        return $data;
    }

    /**
     * Get recent updates data for widget.
     */
    private function getRecentUpdatesData(DashboardWidget $widget, User $user): array
    {
        $config = $widget->getMergedConfig();
        $limit = $config['limit'] ?? 5;
        $days = $config['days'] ?? 7;

        $updates = Credential::with(['group', 'updater'])
            ->where('updated_at', '>=', now()->subDays($days))
            ->orderBy('updated_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($credential) {
                return [
                    'id' => $credential->id,
                    'title' => $credential->title,
                    'group' => $credential->group->name,
                    'updated_by' => $credential->updater?->name ?? 'Unknown',
                    'updated_at' => $credential->updated_at->diffForHumans(),
                ];
            });

        return ['updates' => $updates];
    }

    /**
     * Get navigation tree for sidebar.
     */
    private function getNavigationTree(User $user): array
    {
        $groups = Group::with(['children', 'credentials'])
            ->roots()
            ->active()
            ->orderBy('sort_order')
            ->get();

        return $this->buildNavigationTree($groups);
    }

    /**
     * Build recursive navigation tree.
     */
    private function buildNavigationTree($groups): array
    {
        return $groups->map(function ($group) {
            return [
                'id' => $group->id,
                'name' => $group->name,
                'description' => $group->description,
                'level' => $group->level,
                'credential_count' => $group->credentials->count(),
                'children' => $this->buildNavigationTree($group->children),
            ];
        })->toArray();
    }

    /**
     * Get dashboard statistics.
     */
    private function getDashboardStats(User $user): array
    {
        return [
            'total_credentials' => Credential::count(),
            'total_groups' => Group::count(),
            'expired_passwords' => Credential::expired()->count(),
            'expiring_soon' => Credential::expiringSoon(30)->count(),
            'weak_passwords' => Credential::get()
                ->filter(fn($credential) => $credential->getPasswordStrength() < 50)
                ->count(),
            'recent_activity' => AuditLog::where('created_at', '>=', now()->subDay())->count(),
        ];
    }

    /**
     * Update widget configuration.
     */
    public function updateWidget(Request $request, DashboardWidget $widget)
    {
        $this->authorize('update', $widget);

        $validated = $request->validate([
            'title' => 'string|max:255',
            'config' => 'array',
            'position_x' => 'integer|min:0',
            'position_y' => 'integer|min:0',
            'width' => 'integer|min:1|max:12',
            'height' => 'integer|min:1|max:12',
            'is_visible' => 'boolean',
        ]);

        $widget->update($validated);

        AuditLog::logAction(
            $request->user()->id,
            'update',
            'dashboard_widget',
            $widget->id,
            $widget->title
        );

        return response()->json(['message' => 'Widget updated successfully']);
    }

    /**
     * Create new widget.
     */
    public function createWidget(Request $request)
    {
        $validated = $request->validate([
            'widget_type' => 'required|string|in:activity_log,quick_access,password_health,recent_updates',
            'title' => 'required|string|max:255',
            'config' => 'array',
            'position_x' => 'integer|min:0',
            'position_y' => 'integer|min:0',
            'width' => 'integer|min:1|max:12',
            'height' => 'integer|min:1|max:12',
        ]);

        $widget = $request->user()->dashboardWidgets()->create($validated);

        AuditLog::logAction(
            $request->user()->id,
            'create',
            'dashboard_widget',
            $widget->id,
            $widget->title
        );

        return response()->json(['widget' => $widget, 'message' => 'Widget created successfully']);
    }

    /**
     * Delete widget.
     */
    public function deleteWidget(Request $request, DashboardWidget $widget)
    {
        $this->authorize('delete', $widget);

        AuditLog::logAction(
            $request->user()->id,
            'delete',
            'dashboard_widget',
            $widget->id,
            $widget->title
        );

        $widget->delete();

        return response()->json(['message' => 'Widget deleted successfully']);
    }
}
