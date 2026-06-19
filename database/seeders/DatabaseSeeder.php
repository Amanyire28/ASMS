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
            ClassSeeder::class,         // Classes for students
            SubjectSeeder::class,       // Subjects
            TeacherSeeder::class,       // Teachers
            StudentSeeder::class,       // Students with fees
            PaymentMethodSeeder::class, // Payment methods
            FeeSeeder::class,           // Fees
            FeeScheduleSeeder::class,   // Fee schedules and assign fees to students
            AnnouncementSeeder::class,
        ]);
    }
}
