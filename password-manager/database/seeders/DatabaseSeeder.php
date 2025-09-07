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
            'email' => 'cris.nayr@gmail.com',
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

        // Create default infrastructure credentials
        $this->call(DefaultCredentialsSeeder::class);

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
