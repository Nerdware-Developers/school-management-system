<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Find the User Management menu
        $userManagementMenu = DB::table('menus')
            ->whereNull('parent_id')
            ->where('title', 'User Management')
            ->first();

        if ($userManagementMenu) {
            // Update User Management menu active_routes to include add user page
            $currentActiveRoutes = json_decode($userManagementMenu->active_routes ?? '[]', true);
            if (!in_array('user/add/page', $currentActiveRoutes)) {
                $currentActiveRoutes[] = 'user/add/page';
                DB::table('menus')
                    ->where('id', $userManagementMenu->id)
                    ->update([
                        'active_routes' => json_encode($currentActiveRoutes),
                        'updated_at' => now(),
                    ]);
            }

            // Check if "Add User" menu item already exists
            $existingAddUser = DB::table('menus')
                ->where('parent_id', $userManagementMenu->id)
                ->where('title', 'Add User')
                ->first();

            if (!$existingAddUser) {
                // Get the maximum order for User Management submenu items
                $maxOrder = DB::table('menus')
                    ->where('parent_id', $userManagementMenu->id)
                    ->max('order') ?? 0;

                // Insert "Add User" menu item
                DB::table('menus')->insert([
                    'title' => 'Add User',
                    'icon' => null,
                    'route' => 'user/add/page',
                    'active_routes' => json_encode(['user/add/page']),
                    'pattern' => null,
                    'parent_id' => $userManagementMenu->id,
                    'order' => $maxOrder + 1,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Find the User Management menu
        $userManagementMenu = DB::table('menus')
            ->whereNull('parent_id')
            ->where('title', 'User Management')
            ->first();

        if ($userManagementMenu) {
            // Remove "Add User" menu item
            DB::table('menus')
                ->where('parent_id', $userManagementMenu->id)
                ->where('title', 'Add User')
                ->delete();
        }
    }
};
