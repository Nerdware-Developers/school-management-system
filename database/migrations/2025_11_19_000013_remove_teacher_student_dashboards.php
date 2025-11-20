<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Remove teacher and student dashboard menu items
        DB::table('menus')
            ->whereIn('route', ['teacher/dashboard', 'student/dashboard'])
            ->delete();

        // Update the Dashboard menu to only include admin dashboard
        $dashboardMenu = DB::table('menus')
            ->whereNull('parent_id')
            ->where('title', 'Dashboard')
            ->first();

        if ($dashboardMenu) {
            // Update active_routes to only include 'home'
            DB::table('menus')
                ->where('id', $dashboardMenu->id)
                ->update([
                    'active_routes' => json_encode(['home']),
                    'updated_at' => now(),
                ]);
        }
    }

    public function down(): void
    {
        // Restore teacher and student dashboard menu items if needed
        $dashboardMenu = DB::table('menus')
            ->whereNull('parent_id')
            ->where('title', 'Dashboard')
            ->first();

        if ($dashboardMenu) {
            // Restore teacher dashboard menu
            DB::table('menus')->insert([
                'title' => 'Teacher Dashboard',
                'icon' => null,
                'route' => 'teacher/dashboard',
                'active_routes' => json_encode(['teacher/dashboard']),
                'pattern' => null,
                'parent_id' => $dashboardMenu->id,
                'order' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Restore student dashboard menu
            DB::table('menus')->insert([
                'title' => 'Student Dashboard',
                'icon' => null,
                'route' => 'student/dashboard',
                'active_routes' => json_encode(['student/dashboard']),
                'pattern' => null,
                'parent_id' => $dashboardMenu->id,
                'order' => 3,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Restore active_routes for Dashboard menu
            DB::table('menus')
                ->where('id', $dashboardMenu->id)
                ->update([
                    'active_routes' => json_encode(['home', 'teacher/dashboard', 'student/dashboard']),
                    'updated_at' => now(),
                ]);
        }
    }
};

