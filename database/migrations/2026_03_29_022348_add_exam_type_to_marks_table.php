<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add exam_type column (guard for partial previous run)
        if (!Schema::hasColumn('marks', 'exam_type')) {
            Schema::table('marks', function (Blueprint $table) {
                $table->string('exam_type', 100)->default('Final')->after('academic_year');
            });
        }

        // 2. Add a plain index on student_id so the FK constraint still has
        //    a supporting index after we drop the unique compound index.
        //    (MySQL refuses to drop the unique index if it's the sole index
        //    covering the FK column.)
        $hasPlainIdx = collect(DB::select("SHOW INDEX FROM marks WHERE Key_name = 'marks_student_id_plain_index'"))->isNotEmpty();
        if (!$hasPlainIdx) {
            Schema::table('marks', function (Blueprint $table) {
                $table->index('student_id', 'marks_student_id_plain_index');
            });
        }

        // 3. Drop the old 4-column unique constraint (if it still exists)
        $hasOldUnique = collect(DB::select("SHOW INDEX FROM marks WHERE Key_name = 'marks_student_id_subject_id_term_academic_year_unique'"))->isNotEmpty();
        if ($hasOldUnique) {
            Schema::table('marks', function (Blueprint $table) {
                $table->dropUnique('marks_student_id_subject_id_term_academic_year_unique');
            });
        }

        // 4. New 5-column unique: one row per student/subject/term/year/exam_type
        $hasNewUnique = collect(DB::select("SHOW INDEX FROM marks WHERE Key_name = 'marks_unique_student_subject_term_year_examtype'"))->isNotEmpty();
        if (!$hasNewUnique) {
            Schema::table('marks', function (Blueprint $table) {
                $table->unique(
                    ['student_id', 'subject_id', 'term', 'academic_year', 'exam_type'],
                    'marks_unique_student_subject_term_year_examtype'
                );
            });
        }
    }

    public function down(): void
    {
        // Re-add a plain student_id index before dropping the 5-col unique (same FK issue)
        Schema::table('marks', function (Blueprint $table) {
            $table->index('student_id', 'marks_student_id_plain_index_tmp');
        });

        Schema::table('marks', function (Blueprint $table) {
            $table->dropUnique('marks_unique_student_subject_term_year_examtype');
            $table->dropColumn('exam_type');
        });

        Schema::table('marks', function (Blueprint $table) {
            $table->dropIndex('marks_student_id_plain_index');
            $table->dropIndex('marks_student_id_plain_index_tmp');
            $table->unique(
                ['student_id', 'subject_id', 'term', 'academic_year'],
                'marks_student_id_subject_id_term_academic_year_unique'
            );
        });
    }
};
