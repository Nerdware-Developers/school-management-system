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
        // Get the maximum order value
        $maxOrder = DB::table('menus')->whereNull('parent_id')->max('order') ?? 0;

        // Insert Events menu
        $eventsMenuId = DB::table('menus')->insertGetId([
            'title' => 'Events',
            'icon' => 'fas fa-calendar-alt',
            'route' => 'events.index',
            'active_routes' => json_encode(['events.index', 'events.create', 'events.show', 'events.edit']),
            'pattern' => 'events/*',
            'parent_id' => null,
            'order' => $maxOrder + 1,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insert Transport menu
        $transportMenuId = DB::table('menus')->insertGetId([
            'title' => 'Transport',
            'icon' => 'fas fa-bus',
            'route' => null,
            'active_routes' => json_encode(['transport.buses', 'transport.routes', 'transport.assignments']),
            'pattern' => 'transport/*',
            'parent_id' => null,
            'order' => $maxOrder + 2,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insert Transport submenus
        DB::table('menus')->insert([
            [
                'title' => 'Buses',
                'icon' => null,
                'route' => 'transport.buses',
                'active_routes' => json_encode(['transport.buses', 'transport.buses.create', 'transport.buses.edit']),
                'pattern' => 'transport/buses/*',
                'parent_id' => $transportMenuId,
                'order' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Routes',
                'icon' => null,
                'route' => 'transport.routes',
                'active_routes' => json_encode(['transport.routes', 'transport.routes.create', 'transport.routes.edit']),
                'pattern' => 'transport/routes/*',
                'parent_id' => $transportMenuId,
                'order' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Assignments',
                'icon' => null,
                'route' => 'transport.assignments',
                'active_routes' => json_encode(['transport.assignments', 'transport.assignments.create']),
                'pattern' => 'transport/assignments/*',
                'parent_id' => $transportMenuId,
                'order' => 3,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Delete transport submenus first
        DB::table('menus')->where('title', 'Buses')->orWhere('title', 'Routes')->orWhere('title', 'Assignments')->delete();
        
        // Delete main menus
        DB::table('menus')->where('title', 'Events')->orWhere('title', 'Transport')->delete();
    }
};
