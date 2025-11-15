<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            if (!Schema::hasColumn('subjects', 'teacher_name')) {
                $table->string('teacher_name')->nullable();
            }

            if (!Schema::hasColumn('subjects', 'class')) {
                $table->string('class')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropColumn(['teacher_name', 'class']);
        });
    }
};
