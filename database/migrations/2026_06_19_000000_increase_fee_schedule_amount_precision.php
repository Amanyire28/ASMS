<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Use raw SQL to avoid Doctrine DBAL issues
        DB::statement('ALTER TABLE fee_schedules MODIFY amount DECIMAL(15, 2)');
        DB::statement('ALTER TABLE student_fees MODIFY amount DECIMAL(15, 2)');
        DB::statement('ALTER TABLE student_fees MODIFY discount_amount DECIMAL(15, 2)');
        DB::statement('ALTER TABLE payments MODIFY amount DECIMAL(15, 2)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE fee_schedules MODIFY amount DECIMAL(10, 2)');
        DB::statement('ALTER TABLE student_fees MODIFY amount DECIMAL(10, 2)');
        DB::statement('ALTER TABLE student_fees MODIFY discount_amount DECIMAL(10, 2)');
        DB::statement('ALTER TABLE payments MODIFY amount DECIMAL(10, 2)');
    }
};
