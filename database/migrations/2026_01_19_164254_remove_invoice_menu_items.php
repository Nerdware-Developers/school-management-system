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
        // Find and remove invoice menu items
        $invoiceMenu = DB::table('menus')
            ->whereNull('parent_id')
            ->where('title', 'Invoices')
            ->first();

        if ($invoiceMenu) {
            // Delete all child menu items first
            DB::table('menus')
                ->where('parent_id', $invoiceMenu->id)
                ->delete();

            // Delete the main invoice menu
            DB::table('menus')
                ->where('id', $invoiceMenu->id)
                ->delete();
        }

        // Also deactivate any invoice menu items that might exist with different titles
        DB::table('menus')
            ->where('title', 'LIKE', '%Invoice%')
            ->orWhere('title', 'LIKE', '%invoice%')
            ->update(['is_active' => false]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate invoice menu if needed (optional)
        $invoiceMenuId = DB::table('menus')->insertGetId([
            'title' => 'Invoices',
            'icon'  => 'fas fa-clipboard',
            'route' => null,
            'active_routes' => json_encode(['invoice/list/page']),
            'pattern'   => null,
            'parent_id' => null,
            'order'     => 8,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('menus')->insert([
            [
                'title' => 'Invoices List',
                'icon'  => null,
                'route' => 'invoice/list/page',
                'active_routes' => json_encode(['invoice/list/page']),
                'pattern'   => null,
                'parent_id' => $invoiceMenuId,
                'order'     => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Add Invoice',
                'icon'  => null,
                'route' => 'invoice/add/page',
                'active_routes' => json_encode(['invoice/add/page']),
                'pattern'   => null,
                'parent_id' => $invoiceMenuId,
                'order'     => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
};
