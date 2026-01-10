<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.

     */
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,    // Create roles & permissions first
            AdminUserSeeder::class,     // Then create users with roles
            SchoolSettingSeeder::class,
             ClassLevelSeeder::class,
             AnnouncementSeeder::class,

        ]);
    }
}
