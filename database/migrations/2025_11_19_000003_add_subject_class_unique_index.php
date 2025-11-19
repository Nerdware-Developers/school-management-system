<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('subjects')) {
            return;
        }

        // Remove duplicates keeping the earliest record for each class/subject pair.
        $duplicates = DB::table('subjects')
            ->select('class', 'subject_name', DB::raw('MIN(id) as keep_id'), DB::raw('COUNT(*) as total'))
            ->groupBy('class', 'subject_name')
            ->having('total', '>', 1)
            ->get();

        foreach ($duplicates as $duplicate) {
            DB::table('subjects')
                ->where('class', $duplicate->class)
                ->where('subject_name', $duplicate->subject_name)
                ->where('id', '!=', $duplicate->keep_id)
                ->delete();
        }

        Schema::table('subjects', function (Blueprint $table) {
            $table->unique(['class', 'subject_name'], 'subjects_class_subject_unique');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('subjects')) {
            return;
        }

        Schema::table('subjects', function (Blueprint $table) {
            $table->dropUnique('subjects_class_subject_unique');
        });
    }
};

