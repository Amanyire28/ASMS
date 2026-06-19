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
        Schema::create('fee_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->foreignId('fee_id')->nullable()->constrained('fees')->onDelete('cascade');
            $table->string('term'); // e.g., '1', '2', '3'
            $table->decimal('amount', 15, 2)->default(0); // Legacy single fee amount
            $table->json('fee_amounts')->nullable(); // {fee_id: amount, fee_id: amount, ...}
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->date('due_date');
            $table->string('academic_year'); // e.g., '2024/2025'
            $table->timestamps();

            $table->index('academic_year');
            $table->index(['class_id', 'term']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fee_schedules');
    }
};
