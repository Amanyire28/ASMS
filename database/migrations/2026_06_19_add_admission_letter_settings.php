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
        Schema::table('school_settings', function (Blueprint $table) {
            // Admission letter customization fields
            $table->longText('admission_letter_opening')->nullable()->comment('Opening paragraph for admission letter');
            $table->longText('admission_letter_requirements')->nullable()->comment('Requirements section for admission letter');
            $table->longText('admission_letter_closing')->nullable()->comment('Closing paragraph for admission letter');
            $table->longText('admission_letter_contact_info')->nullable()->comment('Contact information for admissions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('school_settings', function (Blueprint $table) {
            $table->dropColumn([
                'admission_letter_opening',
                'admission_letter_requirements',
                'admission_letter_closing',
                'admission_letter_contact_info',
            ]);
        });
    }
};
