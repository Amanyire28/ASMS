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
        Schema::table('students', function (Blueprint $table) {
            $table->string('admission_number')->nullable()->unique()->after('student_id');
            $table->dateTime('admission_date')->nullable()->after('admission_number');
            $table->boolean('is_admitted')->default(false)->after('admission_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['admission_number', 'admission_date', 'is_admitted']);
        });
    }
};
