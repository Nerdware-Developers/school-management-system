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
        Schema::table('fees_information', function (Blueprint $table) {
            $table->foreignId('student_fee_term_id')
                ->nullable()
                ->after('student_id')
                ->constrained('student_fee_terms')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fees_information', function (Blueprint $table) {
            $table->dropConstrainedForeignId('student_fee_term_id');
        });
    }
};


