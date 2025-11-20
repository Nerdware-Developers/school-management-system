<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add Attendance parent menu
        DB::table('menus')->updateOrInsert(
            ['title' => 'Attendance', 'parent_id' => null],
            [
                'route' => 'attendance.index',
                'icon' => 'fas fa-calendar-check',
                'active_routes' => json_encode(['attendance.index', 'attendance.reports']),
                'is_active' => 1,
                'order' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        
        $attendanceId = DB::table('menus')->where('title', 'Attendance')->whereNull('parent_id')->value('id');

        // Add Attendance submenu items
        if ($attendanceId) {
            DB::table('menus')->updateOrInsert(
                ['title' => 'Mark Attendance', 'parent_id' => $attendanceId],
                [
                    'route' => 'attendance.index',
                    'icon' => 'fas fa-check-circle',
                    'active_routes' => json_encode(['attendance.index']),
                    'is_active' => 1,
                    'order' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
            
            DB::table('menus')->updateOrInsert(
                ['title' => 'Attendance Reports', 'parent_id' => $attendanceId],
                [
                    'route' => 'attendance.reports',
                    'icon' => 'fas fa-chart-bar',
                    'active_routes' => json_encode(['attendance.reports']),
                    'is_active' => 1,
                    'order' => 2,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        // Add Timetable menu
        DB::table('menus')->updateOrInsert(
            ['title' => 'Timetable'],
            [
                'route' => 'timetable.index',
                'icon' => 'fas fa-calendar-alt',
                'active_routes' => json_encode(['timetable.index', 'timetable.create']),
                'parent_id' => null,
                'is_active' => 1,
                'order' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Add Report Cards menu
        DB::table('menus')->updateOrInsert(
            ['title' => 'Report Cards'],
            [
                'route' => 'report-cards.index',
                'icon' => 'fas fa-file-alt',
                'active_routes' => json_encode(['report-cards.index', 'report-cards.generate', 'report-cards.transcript']),
                'parent_id' => null,
                'is_active' => 1,
                'order' => 7,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('menus')->where('title', 'Attendance')->delete();
        DB::table('menus')->where('title', 'Timetable')->delete();
        DB::table('menus')->where('title', 'Report Cards')->delete();
    }
};

