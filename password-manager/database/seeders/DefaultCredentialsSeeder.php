<?php

namespace Database\Seeders;

use App\Models\Credential;
use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Seeder;

class DefaultCredentialsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first user (admin)
        $user = User::first();
        if (!$user) {
            $this->command->error('No users found. Please run the UserSeeder first.');
            return;
        }

        // Create Infrastructure group
        $infrastructureGroup = Group::firstOrCreate([
            'name' => 'Infrastructure',
            'created_by' => $user->id,
        ], [
            'description' => 'Core infrastructure services',
            'level' => 0,
            'parent_id' => null,
        ]);

        // Create Backend Services group
        $backendGroup = Group::firstOrCreate([
            'name' => 'Backend Services',
            'created_by' => $user->id,
        ], [
            'description' => 'Backend application services',
            'level' => 1,
            'parent_id' => $infrastructureGroup->id,
        ]);

        // Create Cache Services group
        $cacheGroup = Group::firstOrCreate([
            'name' => 'Cache Services',
            'created_by' => $user->id,
        ], [
            'description' => 'Caching and session services',
            'level' => 1,
            'parent_id' => $infrastructureGroup->id,
        ]);

        // Create Database Services group
        $databaseGroup = Group::firstOrCreate([
            'name' => 'Database Services',
            'created_by' => $user->id,
        ], [
            'description' => 'Database and storage services',
            'level' => 1,
            'parent_id' => $infrastructureGroup->id,
        ]);

        // Create Backend credential with multiple entries
        Credential::firstOrCreate([
            'title' => 'Backend Services',
            'created_by' => $user->id,
        ], [
            'username' => 'admin',
            'encrypted_password' => encrypt('admin123'),
            'url' => 'http://localhost:8084',
            'username_2' => 'api_user',
            'encrypted_password_2' => encrypt('api123'),
            'username_3' => 'service_user',
            'encrypted_password_3' => encrypt('service123'),
            'url_2' => 'http://localhost:8084/api',
            'url_3' => 'http://localhost:8084/admin',
            'notes' => 'Main backend application with multiple access levels',
            'group_id' => $backendGroup->id,
        ]);

        // Create Cache credential with multiple entries
        Credential::firstOrCreate([
            'title' => 'Redis Cache',
            'created_by' => $user->id,
        ], [
            'username' => 'redis_admin',
            'encrypted_password' => encrypt('redis123'),
            'url' => 'redis://localhost:6379',
            'username_2' => 'redis_user',
            'encrypted_password_2' => encrypt('redis456'),
            'username_3' => 'redis_readonly',
            'encrypted_password_3' => encrypt('redis789'),
            'url_2' => 'redis://localhost:6380',
            'url_3' => 'redis://localhost:6381',
            'notes' => 'Redis cache instances with different access levels',
            'group_id' => $cacheGroup->id,
        ]);

        // Create Database credential with multiple entries
        Credential::firstOrCreate([
            'title' => 'MySQL Database',
            'created_by' => $user->id,
        ], [
            'username' => 'root',
            'encrypted_password' => encrypt('mysql123'),
            'url' => 'mysql://localhost:3306',
            'username_2' => 'app_user',
            'encrypted_password_2' => encrypt('app123'),
            'username_3' => 'readonly_user',
            'encrypted_password_3' => encrypt('readonly123'),
            'url_2' => 'mysql://localhost:3306/password_manager',
            'url_3' => 'mysql://localhost:3306/password_manager_readonly',
            'notes' => 'MySQL database with multiple user accounts for different access levels',
            'group_id' => $databaseGroup->id,
        ]);

        // Create PostgreSQL credential (alternative database)
        Credential::firstOrCreate([
            'title' => 'PostgreSQL Database',
            'created_by' => $user->id,
        ], [
            'username' => 'postgres',
            'encrypted_password' => encrypt('postgres123'),
            'url' => 'postgresql://localhost:5432',
            'username_2' => 'app_user',
            'encrypted_password_2' => encrypt('app456'),
            'url_2' => 'postgresql://localhost:5432/password_manager',
            'notes' => 'PostgreSQL database alternative',
            'group_id' => $databaseGroup->id,
        ]);

        // Create SQLite credential (development database)
        Credential::firstOrCreate([
            'title' => 'SQLite Database',
            'created_by' => $user->id,
        ], [
            'username' => 'sqlite_user',
            'encrypted_password' => encrypt('sqlite123'),
            'url' => 'sqlite://database/database.sqlite',
            'notes' => 'SQLite development database',
            'group_id' => $databaseGroup->id,
        ]);

        $this->command->info('Default infrastructure credentials created successfully!');
        $this->command->info('Created groups: Infrastructure > Backend Services, Cache Services, Database Services');
        $this->command->info('Created credentials: Backend Services, Redis Cache, MySQL Database, PostgreSQL Database, SQLite Database');
    }
}