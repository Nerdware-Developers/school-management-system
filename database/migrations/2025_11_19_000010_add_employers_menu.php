<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Check if a main menu item for "Employers" exists, if not create it
        $employersMenu = DB::table('menus')
            ->whereNull('parent_id')
            ->where('title', 'Employers')
            ->first();

        if (!$employersMenu) {
            // Create main Employers menu
            $employersMenuId = DB::table('menus')->insertGetId([
                'title' => 'Employers',
                'icon' => 'fas fa-briefcase',
                'route' => null,
                'active_routes' => json_encode(['employers', 'employers/*']),
                'pattern' => 'employers*',
                'parent_id' => null,
                'order' => 10, // Adjust order as needed
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $employersMenuId = $employersMenu->id;
        }

        // Add submenu items
        $submenuItems = [
            [
                'title' => 'Employer List',
                'route' => 'employers.index',
                'active_routes' => json_encode(['employers']),
                'order' => 1,
            ],
            [
                'title' => 'Add Employer',
                'route' => 'employers.create',
                'active_routes' => json_encode(['employers/create']),
                'order' => 2,
            ],
        ];

        foreach ($submenuItems as $item) {
            DB::table('menus')->updateOrInsert(
                ['parent_id' => $employersMenuId, 'title' => $item['title']],
                array_merge($item, [
                    'icon' => null,
                    'pattern' => null,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        // Update pattern for main menu to activate on all employer routes
        DB::table('menus')
            ->where('id', $employersMenuId)
            ->update([
                'pattern' => 'employers*',
                'active_routes' => json_encode(['employers', 'employers/*']),
            ]);
    }

    public function down(): void
    {
        $employersMenu = DB::table('menus')
            ->whereNull('parent_id')
            ->where('title', 'Employers')
            ->first();

        if ($employersMenu) {
            // Delete submenu items
            DB::table('menus')
                ->where('parent_id', $employersMenu->id)
                ->delete();

            // Delete main menu
            DB::table('menus')
                ->where('id', $employersMenu->id)
                ->delete();
        }
    }
};

