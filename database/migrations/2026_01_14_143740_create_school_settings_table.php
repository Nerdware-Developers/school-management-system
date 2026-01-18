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
        Schema::create('school_settings', function (Blueprint $table) {
            $table->id();
            $table->string('financial_year')->default(date('Y'));
            $table->integer('term_duration_months')->default(3); // Duration of each term in months
            $table->decimal('default_fee_amount', 10, 2)->default(0.00); // Default fee amount per student per term
            $table->integer('terms_per_year')->default(3); // Number of terms per academic year
            $table->string('academic_year_start_month')->default('January'); // Month when academic year starts
            $table->timestamps();
        });

        // Insert default settings
        DB::table('school_settings')->insert([
            'financial_year' => date('Y'),
            'term_duration_months' => 3,
            'default_fee_amount' => 0.00,
            'terms_per_year' => 3,
            'academic_year_start_month' => 'January',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_settings');
    }
};
