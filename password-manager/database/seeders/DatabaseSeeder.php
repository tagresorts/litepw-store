<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed roles first
        $this->call(RoleSeeder::class);

        // Create admin user
        $adminUser = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@passwordmanager.com',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        // Create test user
        $testUser = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        // Assign roles
        $adminRole = \App\Models\Role::where('name', 'admin')->first();
        $userRole = \App\Models\Role::where('name', 'user')->first();

        $adminUser->roles()->attach($adminRole->id);
        $testUser->roles()->attach($userRole->id);

        // Create sample groups
        $rootGroup = \App\Models\Group::create([
            'name' => 'System Applications',
            'description' => 'Root group for all system applications',
            'created_by' => $adminUser->id,
        ]);

        $prodGroup = \App\Models\Group::create([
            'name' => 'Production',
            'description' => 'Production environment credentials',
            'parent_id' => $rootGroup->id,
            'created_by' => $adminUser->id,
        ]);

        $webAppGroup = \App\Models\Group::create([
            'name' => 'Web Applications',
            'description' => 'Web application credentials',
            'parent_id' => $prodGroup->id,
            'created_by' => $adminUser->id,
        ]);

        // Create sample credentials
        \App\Models\Credential::create([
            'title' => 'Database Server',
            'username' => 'db_admin',
            'password' => 'SecureDBPassword123!',
            'url' => 'mysql://db.example.com:3306',
            'notes' => 'Main production database server',
            'tags' => ['database', 'production', 'mysql'],
            'group_id' => $webAppGroup->id,
            'created_by' => $adminUser->id,
        ]);

        \App\Models\Credential::create([
            'title' => 'Redis Cache',
            'username' => 'redis_user',
            'password' => 'RedisPassword456!',
            'url' => 'redis://cache.example.com:6379',
            'notes' => 'Redis cache server for session storage',
            'tags' => ['cache', 'redis', 'production'],
            'group_id' => $webAppGroup->id,
            'created_by' => $adminUser->id,
        ]);

        // Create default dashboard widgets for admin user
        \App\Models\DashboardWidget::create([
            'user_id' => $adminUser->id,
            'widget_type' => 'activity_log',
            'title' => 'Recent Activity',
            'position_x' => 0,
            'position_y' => 0,
            'width' => 6,
            'height' => 4,
            'sort_order' => 1,
        ]);

        \App\Models\DashboardWidget::create([
            'user_id' => $adminUser->id,
            'widget_type' => 'password_health',
            'title' => 'Password Health',
            'position_x' => 6,
            'position_y' => 0,
            'width' => 6,
            'height' => 4,
            'sort_order' => 2,
        ]);

        \App\Models\DashboardWidget::create([
            'user_id' => $adminUser->id,
            'widget_type' => 'quick_access',
            'title' => 'Quick Access',
            'position_x' => 0,
            'position_y' => 4,
            'width' => 12,
            'height' => 3,
            'sort_order' => 3,
        ]);
    }
}
