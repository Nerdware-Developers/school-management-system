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
        Schema::create('grade_fee_structure', function (Blueprint $table) {
            $table->id();
            $table->string('grade')->unique(); // e.g., 'PLAY GROUP', 'PP1', 'PP2', 'GRADE 1', etc.
            $table->decimal('tuition_fee', 10, 2)->default(0);
            $table->decimal('exam_fee', 10, 2)->default(0);
            $table->decimal('total_fee', 10, 2)->default(0); // Calculated: tuition + exam
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grade_fee_structure');
    }
};
