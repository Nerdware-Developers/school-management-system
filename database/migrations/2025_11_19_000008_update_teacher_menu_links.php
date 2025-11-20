<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $teacherMenuId = DB::table('menus')
            ->whereNull('parent_id')
            ->where('title', 'Teachers')
            ->value('id');

        if (!$teacherMenuId) {
            return;
        }

        DB::table('menus')
            ->updateOrInsert(
                ['parent_id' => $teacherMenuId, 'title' => 'Teacher View'],
                [
                    'icon' => null,
                    'route' => 'teacher/profiles',
                    'active_routes' => json_encode(['teacher/profiles', 'teacher/profile']),
                    'pattern' => 'teacher/profile/*',
                    'order' => 2,
                    'is_active' => true,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
    }

    public function down(): void
    {
        DB::table('menus')
            ->where('route', 'teacher/profiles')
            ->update([
                'route' => null,
                'active_routes' => json_encode([]),
                'pattern' => null,
            ]);
    }
};

