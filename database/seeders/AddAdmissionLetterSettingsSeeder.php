<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SchoolSetting;

class AddAdmissionLetterSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'key' => 'admission_letter_opening',
                'value' => 'We are delighted to inform you that you have been selected for admission to our institution. This is a recognition of your academic excellence and the qualities we value in our students.',
                'type' => 'textarea',
                'group' => 'admission_letter',
            ],
            [
                'key' => 'admission_letter_requirements',
                'value' => "To finalize your admission, please complete the following:\n• Submit all required documentation\n• Provide certified academic records\n• Pay the admission and registration fees\n• Complete the orientation program",
                'type' => 'textarea',
                'group' => 'admission_letter',
            ],
            [
                'key' => 'admission_letter_closing',
                'value' => 'Should you have any questions or require further information, please do not hesitate to contact our admissions office. We look forward to welcoming you to our school community.',
                'type' => 'textarea',
                'group' => 'admission_letter',
            ],
            [
                'key' => 'admission_letter_contact_info',
                'value' => 'Admissions Office: +256 700 000 000 | Email: admissions@asms.ac.ug',
                'type' => 'text',
                'group' => 'admission_letter',
            ],
        ];

        foreach ($settings as $setting) {
            SchoolSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }

        $this->command->info('Admission letter settings added successfully!');
    }
}
