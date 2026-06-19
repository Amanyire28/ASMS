<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SchoolSetting;

class AddSchoolDetailsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'key' => 'school_name',
                'value' => "ST. MARY'S COLLEGE SCHOOL",
                'type' => 'text',
                'group' => 'general',
            ],
            [
                'key' => 'school_address',
                'value' => 'P.O. Box 12345, Kampala, Uganda',
                'type' => 'text',
                'group' => 'general',
            ],
            [
                'key' => 'school_phone',
                'value' => '+256700000000',
                'type' => 'text',
                'group' => 'general',
            ],
            [
                'key' => 'school_email',
                'value' => 'info@asms.ac.ug',
                'type' => 'text',
                'group' => 'general',
            ],
        ];

        foreach ($settings as $setting) {
            SchoolSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }

        $this->command->info('School settings added successfully!');
    }
}
