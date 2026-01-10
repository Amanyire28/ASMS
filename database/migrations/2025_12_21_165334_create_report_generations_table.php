<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportGenerationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_generations', function (Blueprint $table) {
            $table->id();
            $table->string('report_number')->unique(); // "RPT-2025-001"
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->string('term'); // "Term 1", "Semester 1"
            $table->string('academic_year'); // "2025-2026"
            $table->string('report_type')->default('report_card'); // "report_card", "progress_report"
            $table->foreignId('generated_by')->constrained('users')->onDelete('cascade');
            $table->timestamp('generated_at');
            $table->timestamps();
            
            // Ensure unique report per student, term, year, and type
            $table->unique(['student_id', 'term', 'academic_year', 'report_type'], 'unique_student_report_gen');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('report_generations');
    }
}
