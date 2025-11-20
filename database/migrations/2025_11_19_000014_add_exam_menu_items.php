<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Check if Exams menu exists, if not create it
        $examsMenu = DB::table('menus')
            ->whereNull('parent_id')
            ->where('title', 'Exams')
            ->first();

        if (!$examsMenu) {
            // Create main Exams menu
            $examsMenuId = DB::table('menus')->insertGetId([
                'title' => 'Exams',
                'icon' => 'fas fa-clipboard-list',
                'route' => null,
                'active_routes' => json_encode(['exams', 'exams/*', 'exam/*']),
                'pattern' => 'exam*',
                'parent_id' => null,
                'order' => 6, // Adjust order as needed
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $examsMenuId = $examsMenu->id;
        }

        // Add submenu items
        $submenuItems = [
            [
                'title' => 'Exam List',
                'route' => 'exams.page',
                'active_routes' => json_encode(['exams', 'exam/list/page']),
                'order' => 1,
            ],
            [
                'title' => 'Add Exam',
                'route' => 'add/exam/page',
                'active_routes' => json_encode(['add/exam/page', 'exam/add/page']),
                'order' => 2,
            ],
            [
                'title' => 'Enter Marks',
                'route' => 'exam.enter-marks',
                'active_routes' => json_encode(['exam/enter-marks']),
                'order' => 3,
            ],
        ];

        foreach ($submenuItems as $item) {
            DB::table('menus')->updateOrInsert(
                ['parent_id' => $examsMenuId, 'title' => $item['title']],
                array_merge($item, [
                    'icon' => null,
                    'pattern' => null,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        // Update pattern for main menu to activate on all exam routes
        DB::table('menus')
            ->where('id', $examsMenuId)
            ->update([
                'pattern' => 'exam*',
                'active_routes' => json_encode(['exams', 'exams/*', 'exam/*']),
            ]);
    }

    public function down(): void
    {
        $examsMenu = DB::table('menus')
            ->whereNull('parent_id')
            ->where('title', 'Exams')
            ->first();

        if ($examsMenu) {
            // Delete submenu items
            DB::table('menus')
                ->where('parent_id', $examsMenu->id)
                ->delete();

            // Delete main menu
            DB::table('menus')
                ->where('id', $examsMenu->id)
                ->delete();
        }
    }
};

