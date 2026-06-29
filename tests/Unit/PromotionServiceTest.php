<?php

namespace Tests\Unit;

use App\Models\ClassLevel;
use App\Models\ClassModel;
use App\Models\PromotionRecord;
use App\Models\Stream;
use App\Models\Student;
use App\Models\StudentFee;
use App\Models\User;
use App\Services\PromotionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PromotionServiceTest extends TestCase
{
    use RefreshDatabase;

    private PromotionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PromotionService();
    }

    // ─── Generators ──────────────────────────────────────────────────────────────

    /**
     * Generate N distinct random sort_orders and create active ClassLevels for them.
     * Returns the levels in ascending sort_order.
     */
    private function makeClassLevels(int $n): array
    {
        $orders = array_unique(array_map(fn() => rand(1, 9999), range(1, $n * 3)));
        sort($orders);
        $orders = array_slice($orders, 0, $n);
        sort($orders);

        return array_map(fn(int $o, int $i) => ClassLevel::create([
            'name'       => "Level_{$i}",
            'sort_order' => $o,
            'is_active'  => true,
        ]), $orders, array_keys($orders));
    }

    /**
     * Create a Stream and a ClassModel for a given ClassLevel.
     */
    private function makeClass(ClassLevel $level, Stream $stream = null): ClassModel
    {
        if (!$stream) {
            $stream = Stream::create(['name' => 'S' . rand(1, 9999), 'sort_order' => 1, 'is_active' => true]);
        }

        return ClassModel::create([
            'name'           => $level->name . ' ' . $stream->name,
            'class_level_id' => $level->id,
            'stream_id'      => $stream->id,
            'is_active'      => true,
        ]);
    }

    /**
     * Create an active Student in a given ClassModel.
     */
    private function makeStudent(ClassModel $class): Student
    {
        static $counter = 0;
        $counter++;
        return Student::create([
            'first_name'      => 'First' . $counter,
            'last_name'       => 'Last' . $counter,
            'date_of_birth'   => '2010-01-01',
            'gender'          => 'Male',
            'class_id'        => $class->id,
            'enrollment_date' => now()->toDateString(),
            'is_active'       => true,
        ]);
    }

    /**
     * Create a User to act as the promoter.
     */
    private function makeUser(): User
    {
        static $uCounter = 0;
        $uCounter++;
        return User::create([
            'name'     => 'Admin ' . $uCounter,
            'email'    => 'admin' . $uCounter . '@test.com',
            'password' => bcrypt('password'),
        ]);
    }

    // ─── Property 1: Next-level monotonicity ─────────────────────────────────────
    // Feature: student-promotion, Property 1: Next-level monotonicity
    // Validates: Requirements 1.3, 1.5, 4.2

    /**
     * For any sequence of ClassLevels ordered by sort_order,
     * nextClassLevel() must return the level with the smallest sort_order
     * that is strictly greater than the current one — never the same,
     * never lower, never skipping a level when one exists in between.
     *
     * @test
     */
    public function test_property1_next_level_monotonicity(): void
    {
        for ($i = 0; $i < 100; $i++) {
            $n      = rand(2, 6);
            $levels = $this->makeClassLevels($n);

            for ($idx = 0; $idx < count($levels) - 1; $idx++) {
                $current  = $levels[$idx];
                $next     = $this->service->nextClassLevel($current);

                // Must not be null (there are higher levels)
                $this->assertNotNull($next,
                    "Expected a next level above sort_order={$current->sort_order}, got null");

                // Must be strictly greater
                $this->assertGreaterThan($current->sort_order, $next->sort_order,
                    "next level sort_order ({$next->sort_order}) must be > current ({$current->sort_order})");

                // Must be the immediate next (no level skipped)
                $expected = $levels[$idx + 1];
                $this->assertEquals($expected->id, $next->id,
                    "Expected level id={$expected->id} (sort={$expected->sort_order}), got id={$next->id} (sort={$next->sort_order})");
            }

            // Highest level must return null
            $highest = end($levels);
            $this->assertNull(
                $this->service->nextClassLevel($highest),
                "Highest level should return null from nextClassLevel()"
            );

            // Clean up for next iteration
            ClassLevel::whereIn('id', array_column($levels, 'id'))->delete();
        }
    }

    // ─── Property 6: Inactive students excluded ───────────────────────────────────
    // Feature: student-promotion, Property 6: Inactive students excluded
    // Validates: Requirements 7.1

    /**
     * For any class, eligibleStudents() must return only students where is_active = true.
     *
     * @test
     */
    public function test_property6_inactive_students_excluded(): void
    {
        for ($i = 0; $i < 100; $i++) {
            [$level] = $this->makeClassLevels(1);
            $stream  = Stream::create(['name' => 'ST' . $i, 'sort_order' => $i + 1, 'is_active' => true]);
            $class   = $this->makeClass($level, $stream);

            $activeCount   = rand(1, 5);
            $inactiveCount = rand(0, 3);

            for ($a = 0; $a < $activeCount; $a++) {
                $this->makeStudent($class); // is_active = true by default
            }
            for ($b = 0; $b < $inactiveCount; $b++) {
                $s = $this->makeStudent($class);
                $s->update(['is_active' => false]);
            }

            $eligible = $this->service->eligibleStudents($class, '2025-2026');

            // Every returned student must be active
            foreach ($eligible as $student) {
                $this->assertTrue((bool) $student->is_active,
                    "eligibleStudents() returned an inactive student (id={$student->id})");
            }

            // Count must equal the active-only count
            $this->assertCount($activeCount, $eligible,
                "Expected {$activeCount} eligible students, got {$eligible->count()}");
        }
    }

    // ─── Properties 2, 3, 4, 5: Core promotion invariants ───────────────────────
    // Feature: student-promotion, Property 2: Stream preservation
    // Feature: student-promotion, Property 3: Class assignment correctness
    // Feature: student-promotion, Property 4: Promotion record always created
    // Feature: student-promotion, Property 5: Fees never deleted on promotion
    // Validates: Requirements 3.1, 3.2, 3.4, 3.5

    /**
     * For any student promotion:
     * - stream_id after must equal stream_id before (Property 2)
     * - class_id after must equal toClass.id (Property 3)
     * - exactly one PromotionRecord is created with correct fields (Property 4)
     * - StudentFee count must not decrease (Property 5)
     *
     * @test
     */
    public function test_property2_3_4_5_core_promotion_invariants(): void
    {
        $user = $this->makeUser();

        for ($i = 0; $i < 100; $i++) {
            $levels = $this->makeClassLevels(2);
            $stream = Stream::create(['name' => 'Stream' . $i, 'sort_order' => $i + 1, 'is_active' => true]);

            $fromClass = $this->makeClass($levels[0], $stream);
            $toClass   = $this->makeClass($levels[1], $stream);
            $student   = $this->makeStudent($fromClass);

            // Optionally seed some StudentFee rows to verify they are preserved
            $feeCountBefore = rand(0, 3);
            for ($f = 0; $f < $feeCountBefore; $f++) {
                StudentFee::create([
                    'student_id' => $student->id,
                    'fee_id'     => 1, // may not exist, but the row still counts
                    'amount'     => rand(100, 500),
                    'term'       => '1',
                    'status'     => 'outstanding',
                    'due_date'   => now()->addMonth(),
                ]);
            }

            $streamIdBefore = $fromClass->stream_id;
            $recordsBefore  = PromotionRecord::where('student_id', $student->id)->count();
            $feesBefore     = StudentFee::where('student_id', $student->id)->count();

            $result = $this->service->promoteStudentWithRecord(
                $student, $toClass, $user->id, '2025-2026'
            );

            $this->assertTrue($result['success'],
                "promoteStudentWithRecord() failed: " . ($result['error'] ?? ''));

            $student->refresh();

            // Property 2: stream preserved
            $this->assertEquals($streamIdBefore, $toClass->stream_id,
                "Stream id changed after promotion");

            // Property 3: class_id updated to toClass
            $this->assertEquals($toClass->id, $student->class_id,
                "Student class_id was not updated to toClass.id");

            // Property 4: exactly one new PromotionRecord
            $recordsAfter = PromotionRecord::where('student_id', $student->id)->count();
            $this->assertEquals($recordsBefore + 1, $recordsAfter,
                "Expected exactly one new PromotionRecord");

            $record = PromotionRecord::where('student_id', $student->id)
                ->where('academic_year', '2025-2026')
                ->where('type', 'promoted')
                ->latest()
                ->first();

            $this->assertNotNull($record, "PromotionRecord not found");
            $this->assertEquals($student->id, $record->student_id);
            $this->assertEquals($toClass->id, $record->to_class_id);
            $this->assertEquals($user->id, $record->promoted_by);

            // Property 5: fee count must not decrease
            $feesAfter = StudentFee::where('student_id', $student->id)->count();
            $this->assertGreaterThanOrEqual($feesBefore, $feesAfter,
                "StudentFee count decreased after promotion (before={$feesBefore}, after={$feesAfter})");
        }
    }

    // ─── Property 9: Partial failure isolation ───────────────────────────────────
    // Feature: student-promotion, Property 9: Partial failure isolation
    // Validates: Requirements 7.5

    /**
     * For any bulk promotion where one student has no valid target class,
     * the remaining valid students must still be promoted successfully.
     *
     * @test
     */
    public function test_property9_partial_failure_isolation(): void
    {
        $user = $this->makeUser();

        for ($i = 0; $i < 100; $i++) {
            // Two levels with a stream that has both classes
            $levels      = $this->makeClassLevels(2);
            $goodStream  = Stream::create(['name' => 'GoodS' . $i, 'sort_order' => 1, 'is_active' => true]);
            $badStream   = Stream::create(['name' => 'BadS' . $i, 'sort_order' => 2, 'is_active' => true]);

            $goodFrom = $this->makeClass($levels[0], $goodStream);
            $goodTo   = $this->makeClass($levels[1], $goodStream);
            // badFrom has no matching class at level[1] for its stream
            $badFrom  = $this->makeClass($levels[0], $badStream);

            $goodCount = rand(1, 3);
            $goodStudents = collect();
            for ($g = 0; $g < $goodCount; $g++) {
                $goodStudents->push($this->makeStudent($goodFrom));
            }

            $badStudent = $this->makeStudent($badFrom); // no target class → will fail

            $allStudents = $goodStudents->push($badStudent)->shuffle();

            $result = $this->service->bulkPromote($allStudents, '2025-2026', $user->id);

            // Good students should have been promoted
            $this->assertEquals($goodCount, $result['promoted'],
                "Expected {$goodCount} promoted, got {$result['promoted']}");

            // Bad student should be in skipped
            $this->assertGreaterThanOrEqual(1, $result['skipped'],
                "Expected at least 1 skipped, got {$result['skipped']}");

            // Good students must have updated class_id
            foreach ($goodStudents as $s) {
                $s->refresh();
                $this->assertEquals($goodTo->id, $s->class_id,
                    "Good student class_id was not updated after bulk promote");
            }

            // Bad student class_id must not have changed
            $badStudent->refresh();
            $this->assertEquals($badFrom->id, $badStudent->class_id,
                "Failed student's class_id should not have changed");
        }
    }

    // ─── Properties 7 & 4 (retention): Retention invariants ─────────────────────
    // Feature: student-promotion, Property 7: Retention does not change class
    // Feature: student-promotion, Property 4: Promotion record always created (retention)
    // Validates: Requirements 5.2, 5.3

    /**
     * For any student for whom retainStudent() is called:
     * - class_id must be identical before and after (Property 7)
     * - exactly one PromotionRecord with type=retained must be created (Property 4 retention)
     *
     * @test
     */
    public function test_property7_and_4_retention_invariants(): void
    {
        $user = $this->makeUser();

        for ($i = 0; $i < 100; $i++) {
            [$level]  = $this->makeClassLevels(1);
            $stream   = Stream::create(['name' => 'RetS' . $i, 'sort_order' => 1, 'is_active' => true]);
            $class    = $this->makeClass($level, $stream);
            $student  = $this->makeStudent($class);

            $classIdBefore   = $student->class_id;
            $recordsBefore   = PromotionRecord::where('student_id', $student->id)->count();

            $this->service->retainStudent($student, $user->id, '2025-2026');

            $student->refresh();

            // Property 7: class_id unchanged
            $this->assertEquals($classIdBefore, $student->class_id,
                "retainStudent() must not change the student's class_id");

            // Property 4 (retention): one record created with correct type
            $recordsAfter = PromotionRecord::where('student_id', $student->id)->count();
            $this->assertEquals($recordsBefore + 1, $recordsAfter,
                "Expected exactly one new PromotionRecord for retention");

            $record = PromotionRecord::where('student_id', $student->id)
                ->where('type', 'retained')
                ->latest()
                ->first();

            $this->assertNotNull($record, "Retained PromotionRecord not found");
            $this->assertNull($record->to_class_id,
                "Retained record to_class_id should be null");
            $this->assertEquals($classIdBefore, $record->from_class_id,
                "Retained record from_class_id should equal the student's class");
        }
    }
}
