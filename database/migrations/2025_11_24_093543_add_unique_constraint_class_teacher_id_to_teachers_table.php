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
        Schema::table('teachers', function (Blueprint $table) {
            // Remove any duplicate class teacher assignments (keep the first teacher assigned to each class)
            // This handles existing duplicates before adding the unique constraint
            \DB::statement('
                UPDATE teachers t1
                INNER JOIN (
                    SELECT class_teacher_id, MIN(id) as first_id
                    FROM teachers
                    WHERE class_teacher_id IS NOT NULL
                    GROUP BY class_teacher_id
                    HAVING COUNT(*) > 1
                ) t2 ON t1.class_teacher_id = t2.class_teacher_id
                SET t1.class_teacher_id = NULL
                WHERE t1.id != t2.first_id
            ');
            
            // Add unique constraint to ensure only one teacher per class
            // This allows multiple NULL values but ensures each non-null class_teacher_id is unique
            $table->unique('class_teacher_id', 'unique_class_teacher');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->dropUnique('unique_class_teacher');
        });
    }
};
