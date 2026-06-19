<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('student_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('fee_id')->constrained('fees')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->string('term')->nullable(); // e.g., '1', '2', '3'
            $table->enum('status', ['outstanding', 'partial', 'paid', 'waived'])->default('outstanding');
            $table->date('due_date')->nullable();
            $table->text('notes')->nullable();
            $table->decimal('discount_amount', 10, 2)->nullable()->default(0);
            $table->string('discount_reason')->nullable();
            $table->boolean('waived')->default(false);
            $table->string('waived_reason')->nullable();
            $table->timestamps();

            $table->index('student_id');
            $table->index('fee_id');
            $table->index('status');
            $table->index('due_date');
            $table->unique(['student_id', 'fee_id', 'term']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_fees');
    }
};
