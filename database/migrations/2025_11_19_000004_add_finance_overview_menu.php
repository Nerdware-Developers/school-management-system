<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $accountsMenu = DB::table('menus')
            ->whereNull('parent_id')
            ->where('title', 'Accounts')
            ->first();

        if (!$accountsMenu) {
            return;
        }

        $exists = DB::table('menus')
            ->where('parent_id', $accountsMenu->id)
            ->where('route', 'account/finance/overview')
            ->exists();

        if (!$exists) {
            DB::table('menus')->insert([
                'title' => 'Finance Overview',
                'icon' => null,
                'route' => 'account/finance/overview',
                'active_routes' => json_encode(['account/finance/overview']),
                'pattern' => null,
                'parent_id' => $accountsMenu->id,
                'order' => 5,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Update existing placeholders
        DB::table('menus')
            ->where('parent_id', $accountsMenu->id)
            ->where('title', 'Expenses')
            ->update([
                'route' => 'account/expenses',
                'active_routes' => json_encode(['account/expenses', 'account/expenses/create']),
            ]);

        DB::table('menus')
            ->where('parent_id', $accountsMenu->id)
            ->where('title', 'Salary')
            ->update([
                'route' => 'account/salary',
                'active_routes' => json_encode(['account/salary', 'account/salary/create']),
            ]);
    }

    public function down(): void
    {
        DB::table('menus')
            ->where('route', 'account/finance/overview')
            ->delete();

        DB::table('menus')
            ->where('route', 'account/expenses')
            ->update([
                'route' => null,
                'active_routes' => json_encode([]),
            ]);

        DB::table('menus')
            ->where('route', 'account/salary')
            ->update([
                'route' => null,
                'active_routes' => json_encode([]),
            ]);
    }
};

