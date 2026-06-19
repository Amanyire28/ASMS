<?php

namespace App\Console\Commands;

use App\Models\Student;
use Illuminate\Console\Command;

class AssignFeesToStudents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fees:assign-to-students {--force : Force reassignment of fees}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Assign fees to all active students based on their class';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $force = $this->option('force');

        // Get all active students with a class assigned
        $students = Student::where('is_active', true)
            ->whereNotNull('class_id')
            ->get();

        if ($students->isEmpty()) {
            $this->info('No active students found.');
            return 0;
        }

        $this->info("Processing {$students->count()} students...");
        $bar = $this->output->createProgressBar($students->count());
        $bar->start();

        foreach ($students as $student) {
            if ($force) {
                // Delete existing fees
                $student->studentFees()->delete();
            }

            // Assign fees from class schedule
            $student->assignFeesFromClass();
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Fees assigned successfully!');

        return 0;
    }
}
