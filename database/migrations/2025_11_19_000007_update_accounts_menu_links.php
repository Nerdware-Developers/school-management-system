<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $accountsMenuId = DB::table('menus')
            ->whereNull('parent_id')
            ->where('title', 'Accounts')
            ->value('id');

        if (!$accountsMenuId) {
            return;
        }

        $this->upsertChild($accountsMenuId, 'Fees Collection', 'account/fees/collections/page', ['account/fees/collections/page'], 1);
        $this->upsertChild($accountsMenuId, 'Expenses', 'account/expenses', ['account/expenses', 'account/expenses/create'], 2);
        $this->upsertChild($accountsMenuId, 'Salary', 'account/salary', ['account/salary', 'account/salary/create'], 3);
        $this->upsertChild($accountsMenuId, 'Add Fees', 'add/fees/collection/page', ['add/fees/collection/page'], 4);
        $this->upsertChild($accountsMenuId, 'Finance Overview', 'account/finance/overview', ['account/finance/overview'], 5);
    }

    public function down(): void
    {
        $accountsMenuId = DB::table('menus')
            ->whereNull('parent_id')
            ->where('title', 'Accounts')
            ->value('id');

        if (!$accountsMenuId) {
            return;
        }

        DB::table('menus')
            ->where('parent_id', $accountsMenuId)
            ->whereIn('route', [
                'account/expenses',
                'account/salary',
                'account/finance/overview',
            ])
            ->update([
                'route' => null,
                'active_routes' => json_encode([]),
            ]);
    }

    protected function upsertChild($parentId, string $title, ?string $route, array $activeRoutes, int $order): void
    {
        $existing = DB::table('menus')
            ->where('parent_id', $parentId)
            ->where('title', $title)
            ->first();

        $data = [
            'icon' => null,
            'route' => $route,
            'active_routes' => json_encode($activeRoutes),
            'pattern' => null,
            'order' => $order,
            'is_active' => true,
            'updated_at' => now(),
        ];

        if ($existing) {
            DB::table('menus')
                ->where('id', $existing->id)
                ->update($data);
        } else {
            DB::table('menus')->insert(array_merge($data, [
                'parent_id' => $parentId,
                'title' => $title,
                'created_at' => now(),
            ]));
        }
    }
};

