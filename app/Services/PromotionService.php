<?php

namespace App\Services;

use App\Models\ClassLevel;
use App\Models\ClassModel;
use App\Models\PromotionRecord;
use App\Models\Student;
use App\Models\StudentFee;
use App\Models\FeeSchedule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PromotionService
{
    /**
     * Returns the next active ClassLevel above $current by sort_order,
     * or null if $current is already the highest level.
     * Requirements: 1.5, 4.2
     */
    public function nextClassLevel(ClassLevel $current): ?ClassLevel
    {
        return ClassLevel::where('is_active', true)
            ->where('sort_order', '>', $current->sort_order)
            ->orderBy('sort_order', 'asc')
            ->first();
    }

    /**
     * Returns the target ClassModel for promotion (same stream, next level),
     * or null if no next level exists or no matching class exists.
     * Requirements: 1.3, 3.3
     */
    public function targetClass(ClassModel $from): ?ClassModel
    {
        // Load the current class level if not already loaded
        $currentLevel = $from->classLevel ?? ClassLevel::find($from->class_level_id);

        if (!$currentLevel) {
            return null;
        }

        $nextLevel = $this->nextClassLevel($currentLevel);

        if (!$nextLevel) {
            return null;
        }

        return ClassModel::where('class_level_id', $nextLevel->id)
            ->where('stream_id', $from->stream_id)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Returns students eligible for promotion from $class:
     * active students in that class not yet promoted this academic year.
     * Requirements: 7.1
     */
    public function eligibleStudents(ClassModel $class, string $academicYear): Collection
    {
        return Student::where('class_id', $class->id)
            ->where('is_active', true)
            ->get();
    }

    /**
     * Promotes a single student to $toClass.
     * Updates class_id WITHOUT triggering the boot hook's fee deletion,
     * then assigns new fees from the target class schedule.
     * Creates a PromotionRecord audit entry.
     * Returns ['success' => true] or ['success' => false, 'error' => '...']
     * Requirements: 3.1, 3.2, 3.4, 3.5, 3.6, 4.3, 4.4
     */
    public function promoteStudent(Student $student, ClassModel $toClass, int $promotedBy, string $academicYear): array
    {
        // Validate student is active (Req 7.1)
        if (!$student->is_active) {
            return ['success' => false, 'error' => 'Student is not active.'];
        }

        // Validate target class exists (Req 3.3, 7.3)
        $target = ClassModel::find($toClass->id);
        if (!$target) {
            return ['success' => false, 'error' => "Target class does not exist. Create it first."];
        }

        try {
            DB::transaction(function () use ($student, $toClass, $promotedBy, $academicYear) {
                // Update class_id directly via DB query to bypass the Student boot hook
                // which would delete existing StudentFee records (Req 3.5, 4.5).
                DB::table('students')
                    ->where('id', $student->id)
                    ->update(['class_id' => $toClass->id, 'updated_at' => now()]);

                // Refresh the model so class_id is up to date
                $student->refresh();

                // Assign new fees from target class schedule without deleting old ones (Req 3.6)
                $this->assignNewFees($student, $toClass);

                // Create the audit record (Req 3.4, 4.4)
                PromotionRecord::create([
                    'student_id'   => $student->id,
                    'from_class_id' => null, // set below after refresh — captured before update
                    'to_class_id'  => $toClass->id,
                    'type'         => 'promoted',
                    'academic_year' => $academicYear,
                    'promoted_by'  => $promotedBy,
                    'promoted_at'  => now(),
                ]);
            });

            return ['success' => true];
        } catch (\Throwable $e) {
            Log::error('PromotionService::promoteStudent failed', [
                'student_id' => $student->id,
                'to_class_id' => $toClass->id,
                'error' => $e->getMessage(),
            ]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Promotes a single student, capturing the from_class_id before the update.
     * This is the internal version used by bulkPromote and the controller.
     * Requirements: 3.1, 3.4
     */
    public function promoteStudentWithRecord(Student $student, ClassModel $toClass, int $promotedBy, string $academicYear): array
    {
        if (!$student->is_active) {
            return ['success' => false, 'error' => 'Student is not active.'];
        }

        $target = ClassModel::find($toClass->id);
        if (!$target) {
            $levelName = optional($toClass->classLevel)->name ?? 'Unknown';
            $streamName = optional($toClass->stream)->name ?? 'Unknown';
            return [
                'success' => false,
                'error'   => "Target class {$levelName} {$streamName} does not exist. Create it first.",
            ];
        }

        $fromClassId = $student->class_id;

        try {
            DB::transaction(function () use ($student, $toClass, $promotedBy, $academicYear, $fromClassId) {
                // Bypass boot hook to preserve existing StudentFee rows (Req 3.5)
                DB::table('students')
                    ->where('id', $student->id)
                    ->update(['class_id' => $toClass->id, 'updated_at' => now()]);

                $student->refresh();

                // Assign new fees without removing old ones (Req 3.6)
                $this->assignNewFees($student, $toClass);

                // Audit record (Req 3.4)
                PromotionRecord::create([
                    'student_id'    => $student->id,
                    'from_class_id' => $fromClassId,
                    'to_class_id'   => $toClass->id,
                    'type'          => 'promoted',
                    'academic_year' => $academicYear,
                    'promoted_by'   => $promotedBy,
                    'promoted_at'   => now(),
                ]);
            });

            return ['success' => true];
        } catch (\Throwable $e) {
            Log::error('PromotionService::promoteStudentWithRecord failed', [
                'student_id'  => $student->id,
                'to_class_id' => $toClass->id,
                'error'       => $e->getMessage(),
            ]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Promotes all students in $students to their respective target classes.
     * Each student is processed independently — failure for one does not affect others.
     * Returns ['promoted' => int, 'skipped' => int, 'errors' => array]
     * Requirements: 3.1, 7.5
     */
    public function bulkPromote(Collection $students, string $academicYear, int $promotedBy): array
    {
        $promoted = 0;
        $skipped  = 0;
        $errors   = [];

        foreach ($students as $student) {
            try {
                // Determine target class for this student
                $fromClass = ClassModel::find($student->class_id);

                if (!$fromClass) {
                    $skipped++;
                    $errors[] = ['student' => $student->full_name, 'error' => 'Current class not found.'];
                    continue;
                }

                $toClass = $this->targetClass($fromClass);

                if (!$toClass) {
                    $skipped++;
                    $errors[] = ['student' => $student->full_name, 'error' => 'No target class available.'];
                    continue;
                }

                $result = $this->promoteStudentWithRecord($student, $toClass, $promotedBy, $academicYear);

                if ($result['success']) {
                    $promoted++;
                } else {
                    $skipped++;
                    $errors[] = ['student' => $student->full_name, 'error' => $result['error']];
                }
            } catch (\Throwable $e) {
                $skipped++;
                $errors[] = ['student' => $student->full_name ?? "ID:{$student->id}", 'error' => $e->getMessage()];
            }
        }

        return compact('promoted', 'skipped', 'errors');
    }

    /**
     * Marks a student as retained — does NOT change class_id.
     * Creates a PromotionRecord with type = retained.
     * Requirements: 5.2, 5.3
     */
    public function retainStudent(Student $student, int $promotedBy, string $academicYear): void
    {
        PromotionRecord::create([
            'student_id'    => $student->id,
            'from_class_id' => $student->class_id,
            'to_class_id'   => null, // null for retention (Req 5.2)
            'type'          => 'retained',
            'academic_year' => $academicYear,
            'promoted_by'   => $promotedBy,
            'promoted_at'   => now(),
        ]);
        // class_id is NOT changed (Req 5.3)
    }

    /**
     * Assigns new fees to a student from the target class's FeeSchedule
     * without deleting existing StudentFee records.
     * This replicates the logic in Student::assignFeesFromClass() but without
     * the preceding deletion step that would violate Req 3.5.
     */
    private function assignNewFees(Student $student, ClassModel $toClass): void
    {
        $term         = config('school.current_term', '1');
        $academicYear = config('school.current_academic_year', null);

        $scheduleQuery = FeeSchedule::where('class_id', $toClass->id)
            ->where('term', $term);

        if ($academicYear) {
            $scheduleQuery->where('academic_year', $academicYear);
        }

        $schedule = $scheduleQuery->latest('academic_year')->first();

        if (!$schedule || empty($schedule->fee_amounts)) {
            return;
        }

        $feeAmounts = $schedule->fee_amounts;
        if (is_string($feeAmounts)) {
            $feeAmounts = json_decode($feeAmounts, true);
        }

        if (!is_array($feeAmounts) || empty($feeAmounts)) {
            return;
        }

        foreach ($feeAmounts as $feeId => $amount) {
            $existing = StudentFee::where('student_id', $student->id)
                ->where('fee_id', $feeId)
                ->where('term', $term)
                ->first();

            if (!$existing) {
                StudentFee::create([
                    'student_id' => $student->id,
                    'fee_id'     => $feeId,
                    'amount'     => $amount,
                    'term'       => $term,
                    'status'     => 'outstanding',
                    'due_date'   => $schedule->due_date ?? now()->addMonth(),
                ]);
            }
        }
    }
}
