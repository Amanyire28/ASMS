<?php

namespace Database\Seeders;

use App\Models\FeeSchedule;
use App\Models\Fee;
use App\Models\ClassModel;
use App\Models\Student;
use Illuminate\Database\Seeder;

class FeeScheduleSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $classes = ClassModel::all();
        $fees = Fee::where('is_active', true)->get();

        if ($fees->isEmpty()) {
            $this->command->error('No active fees found. Cannot create schedules.');
            return;
        }

        $academicYear = date('Y') . '/' . (date('Y') + 1);

        foreach ($classes as $class) {
            // Create fee schedule for each class
            $feeAmounts = [];
            $totalAmount = 0;

            foreach ($fees as $fee) {
                // Assign random amount to each fee (1M to 5M)
                $amount = rand(1000000, 5000000);
                $feeAmounts[$fee->id] = $amount;
                $totalAmount += $amount;
            }

            FeeSchedule::updateOrCreate(
                [
                    'class_id' => $class->id,
                    'term' => '1',
                    'academic_year' => $academicYear,
                ],
                [
                    'fee_id' => $fees->first()->id, // Keep for backward compatibility
                    'amount' => $totalAmount,
                    'fee_amounts' => json_encode($feeAmounts),
                    'total_amount' => $totalAmount,
                    'due_date' => now()->addMonths(2),
                ]
            );

            $this->command->info("Created fee schedule for class: {$class->name}");
        }

        // Assign fees to all students
        $students = Student::all();
        foreach ($students as $student) {
            $student->assignFeesFromClass();
        }

        $this->command->info("✅ Assigned fees to {$students->count()} students");
    }
}
