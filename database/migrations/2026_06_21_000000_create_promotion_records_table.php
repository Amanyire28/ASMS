<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromotionRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promotion_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('from_class_id')->nullable();
            $table->unsignedBigInteger('to_class_id')->nullable(); // null for retention
            $table->enum('type', ['promoted', 'retained']);
            $table->string('academic_year', 20); // e.g. "2025-2026"
            $table->unsignedBigInteger('promoted_by')->nullable();
            $table->timestamp('promoted_at');
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('from_class_id')->references('id')->on('classes')->onDelete('set null');
            $table->foreign('to_class_id')->references('id')->on('classes')->onDelete('set null');
            $table->foreign('promoted_by')->references('id')->on('users')->onDelete('set null');

            // Indexes for fast lookup
            $table->index(['student_id', 'academic_year']);
            $table->index(['from_class_id', 'academic_year']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('promotion_records');
    }
}
