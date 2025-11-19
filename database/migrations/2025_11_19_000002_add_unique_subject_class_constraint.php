<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teacher_subject_class', function (Blueprint $table) {
            if (!Schema::hasColumn('teacher_subject_class', 'subject_id')) {
                return;
            }

            $table->unique(['subject_id', 'class_id'], 'teacher_subject_class_subject_class_unique');
        });
    }

    public function down(): void
    {
        Schema::table('teacher_subject_class', function (Blueprint $table) {
            $table->dropUnique('teacher_subject_class_subject_class_unique');
        });
    }
};

