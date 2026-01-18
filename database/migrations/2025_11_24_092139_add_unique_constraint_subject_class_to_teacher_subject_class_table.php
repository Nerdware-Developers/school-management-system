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
        Schema::table('teacher_subject_class', function (Blueprint $table) {
            // Remove any duplicate assignments (keep the first one for each subject-class combination)
            // This handles existing duplicates before adding the unique constraint
            \DB::statement('
                DELETE t1 FROM teacher_subject_class t1
                INNER JOIN teacher_subject_class t2 
                WHERE t1.id > t2.id 
                AND t1.subject_id = t2.subject_id 
                AND t1.class_id = t2.class_id
            ');
            
            // Add unique constraint to ensure only one teacher per subject-class combination
            $table->unique(['subject_id', 'class_id'], 'unique_subject_class');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teacher_subject_class', function (Blueprint $table) {
            $table->dropUnique('unique_subject_class');
        });
    }
};
