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
        $accountsMenuId = DB::table('menus')
            ->whereNull('parent_id')
            ->where('title', 'Accounts')
            ->value('id');

        if (!$accountsMenuId) {
            return;
        }

        // Check if Financial Settings menu already exists
        $exists = DB::table('menus')
            ->where('parent_id', $accountsMenuId)
            ->where('route', 'financial.settings')
            ->exists();

        if (!$exists) {
            $maxOrder = DB::table('menus')
                ->where('parent_id', $accountsMenuId)
                ->max('order') ?? 0;

            DB::table('menus')->insert([
                'title' => 'Financial Settings',
                'icon' => null,
                'route' => 'financial.settings',
                'active_routes' => json_encode(['financial.settings', 'financial.settings.update', 'financial.settings.apply']),
                'pattern' => null,
                'parent_id' => $accountsMenuId,
                'order' => $maxOrder + 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('menus')
            ->where('route', 'financial.settings')
            ->delete();
    }
};
