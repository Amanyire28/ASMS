<?php

namespace Database\Seeders;

use App\Models\Fee;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fees = [
            // Tuition fees
            [
                'name' => 'Tuition Fee',
                'description' => 'Regular tuition fee for academic instruction',
                'category' => 'tuition',
                'type' => 'fixed',
                'is_active' => true,
            ],
            // Registration fees
            [
                'name' => 'Registration Fee',
                'description' => 'Student registration and enrollment fee',
                'category' => 'registration',
                'type' => 'fixed',
                'is_active' => true,
            ],
            [
                'name' => 'Admission Fee',
                'description' => 'One-time admission fee for new students',
                'category' => 'registration',
                'type' => 'fixed',
                'is_active' => true,
            ],
            // Activity fees
            [
                'name' => 'Activity Fee',
                'description' => 'Fee for extracurricular activities and clubs',
                'category' => 'activities',
                'type' => 'fixed',
                'is_active' => true,
            ],
            [
                'name' => 'Sports Fee',
                'description' => 'Fee for sports programs and athletics',
                'category' => 'activities',
                'type' => 'fixed',
                'is_active' => true,
            ],
            [
                'name' => 'Culture Day Fee',
                'description' => 'Fee for cultural events and performances',
                'category' => 'activities',
                'type' => 'fixed',
                'is_active' => true,
            ],
            // Facility fees
            [
                'name' => 'Library Fee',
                'description' => 'Library resource and maintenance fee',
                'category' => 'facilities',
                'type' => 'fixed',
                'is_active' => true,
            ],
            [
                'name' => 'Laboratory Fee',
                'description' => 'Science laboratory usage fee',
                'category' => 'facilities',
                'type' => 'fixed',
                'is_active' => true,
            ],
            [
                'name' => 'Technology Fee',
                'description' => 'Computer lab and technology resource fee',
                'category' => 'facilities',
                'type' => 'fixed',
                'is_active' => true,
            ],
            // Examination fees
            [
                'name' => 'Examination Fee',
                'description' => 'End of term and standardized exam fees',
                'category' => 'examination',
                'type' => 'fixed',
                'is_active' => true,
            ],
            [
                'name' => 'Revision Materials Fee',
                'description' => 'Fee for exam revision materials and resources',
                'category' => 'examination',
                'type' => 'fixed',
                'is_active' => true,
            ],
            // Other fees
            [
                'name' => 'Development Fee',
                'description' => 'School development and improvement projects',
                'category' => 'other',
                'type' => 'fixed',
                'is_active' => true,
            ],
            [
                'name' => 'Maintenance Fee',
                'description' => 'Building and facility maintenance',
                'category' => 'other',
                'type' => 'fixed',
                'is_active' => true,
            ],
        ];

        foreach ($fees as $fee) {
            Fee::updateOrCreate(
                ['name' => $fee['name']],
                $fee
            );
        }
    }
}
