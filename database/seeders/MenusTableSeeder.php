<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Menu;

class MenusTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('menus')->insert([
            // Main Menu
            [
                'title' => 'Main Menu',
                'icon'  => null,
                'route' => null,
                'active_routes' => null,
                'pattern'   => null,
                'parent_id' => null,
                'order'     => 1,
                'is_active' => false,
            ],
            [
                'title' => 'Dashboard',
                'icon'  => 'fas fa-tachometer-alt',
                'route' => null,
                'active_routes' => json_encode(['home', 'teacher/dashboard', 'student/dashboard']),
                'pattern'   => null,
                'parent_id' => null,
                'order'     => 3,
                'is_active' => true,
            ],
            [
                'title' => 'Admin Dashboard',
                'icon'  => null,
                'route' => 'home',
                'active_routes' => json_encode(['home']),
                'pattern'   => null,
                'parent_id' => 3,
                'order'     => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Teacher Dashboard',
                'icon'  => null,
                'route' => 'teacher/dashboard',
                'active_routes' => json_encode(['teacher/dashboard']),
                'pattern'   => null,
                'parent_id' => 3,
                'order'     => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Student Dashboard',
                'icon'  => null,
                'route' => 'student/dashboard',
                'active_routes' => json_encode(['student/dashboard']),
                'pattern'   => null,
                'parent_id' => 3,
                'order'     => 3,
                'is_active' => true,
            ],
            [
                'title' => 'User Management',
                'icon'  => 'fas fa-shield-alt',
                'route' => null,
                'active_routes' => json_encode(['list/users']),
                'pattern'   => null,
                'parent_id' => null,
                'order'     => 4,
                'is_active' => true,
            ],
            [
                'title' => 'List Users',
                'icon'  => null,
                'route' => 'list/users',
                'active_routes' => json_encode(['list/users']),
                'pattern'   => 'view/user/edit/*',
                'parent_id' => 7,
                'order'     => 1,
                'is_active' => true,
            ],
        ]);

        // Insert the "Settings" menu with a submenu for "General Settings"
        $settingsMenuId = DB::table('menus')->insertGetId([
            'title' => 'Settings',
            'icon'  => 'fas fa-cog',
            'route' => null,
            'active_routes' => json_encode(['setting/page']),
            'pattern'   => null,
            'parent_id' => null,
            'order'     => 2, // Adjust order as needed
            'is_active' => true,
        ]);

        // Insert submenu items under "Settings"
        DB::table('menus')->insert([
            [
                'title' => 'General Settings',
                'icon'  => null,
                'route' => 'setting/page',
                'active_routes' => json_encode(['setting/page']),
                'pattern'   => null,
                'parent_id' => $settingsMenuId,
                'order'     => 1,
                'is_active' => true,
            ]
        ]);
        
        // Insert the "Students" menu and its submenus
        $studentMenuId = DB::table('menus')->insertGetId([
            'title' => 'Students',
            'icon'  => 'fas fa-graduation-cap',
            'route' => null,
            'active_routes' => json_encode(['student/list', 'student/grid', 'student/add/page']),
            'pattern'   => 'student/edit/*|student/profile/*',
            'parent_id' => null,
            'order'     => 11,
            'is_active' => true,
        ]);
        
        DB::table('menus')->insert([
            [
                'title' => 'Student List',
                'icon'  => null,
                'route' => 'student/list',
                'active_routes' => json_encode(['student/list', 'student/grid']),
                'pattern'   => null,
                'parent_id' => $studentMenuId,
                'order'     => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Student Add',
                'icon'  => null,
                'route' => 'student/add/page',
                'active_routes' => json_encode(['student/add/page']),
                'pattern'   => null,
                'parent_id' => $studentMenuId,
                'order'     => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Student Edit',
                'icon'  => null,
                'route' => null,
                'active_routes' => json_encode([]),
                'pattern'   => 'student/edit/*',
                'parent_id' => $studentMenuId,
                'order'     => 3,
                'is_active' => true,
            ],
            [
                'title' => 'Student View',
                'icon'  => null,
                'route' => null,
                'active_routes' => json_encode([]),
                'pattern'   => 'student/profile/*',
                'parent_id' => $studentMenuId,
                'order'     => 4,
                'is_active' => true,
            ],
        ]);

        // Insert the "Teachers" menu and its submenus
        $teacherMenuId = DB::table('menus')->insertGetId([
            'title' => 'Teachers',
            'icon'  => 'fas fa-chalkboard-teacher',
            'route' => null,
            'active_routes' => json_encode(['teacher/add/page', 'teacher/list/page', 'teacher/grid/page']),
            'pattern'   => 'teacher/edit/*',
            'parent_id' => null,
            'order'     => 12, // Ensure this is a unique order number
            'is_active' => true,
        ]);

        DB::table('menus')->insert([
            [
                'title' => 'Teacher List',
                'icon'  => null,
                'route' => 'teacher/list/page',
                'active_routes' => json_encode(['teacher/list/page', 'teacher/grid/page']),
                'pattern'   => null,
                'parent_id' => $teacherMenuId,
                'order'     => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Teacher View',
                'icon'  => null,
                'route' => null, // Placeholder, update with an actual route if needed
                'active_routes' => json_encode([]),
                'pattern'   => null,
                'parent_id' => $teacherMenuId,
                'order'     => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Teacher Add',
                'icon'  => null,
                'route' => 'teacher/add/page',
                'active_routes' => json_encode(['teacher/add/page']),
                'pattern'   => null,
                'parent_id' => $teacherMenuId,
                'order'     => 3,
                'is_active' => true,
            ],
            [
                'title' => 'Teacher Edit',
                'icon'  => null,
                'route' => null, // Placeholder, update with an actual route if needed
                'active_routes' => json_encode([]),
                'pattern'   => 'teacher/edit/*',
                'parent_id' => $teacherMenuId,
                'order'     => 4,
                'is_active' => true,
            ],
        ]);

        // Insert the "Departments" menu and its submenus
        $departmentMenuId = DB::table('menus')->insertGetId([
            'title' => 'Departments',
            'icon'  => 'fas fa-building',
            'route' => null,
            'active_routes' => json_encode(['department/add/page', 'department/edit/page']),
            'pattern'   => null,
            'parent_id' => null,
            'order'     => 13, // Ensure this is a unique order number
            'is_active' => true,
        ]);

        DB::table('menus')->insert([
            [
                'title' => 'Department List',
                'icon'  => null,
                'route' => 'department/list/page',
                'active_routes' => json_encode(['department/list/page']),
                'pattern'   => null,
                'parent_id' => $departmentMenuId,
                'order'     => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Department Add',
                'icon'  => null,
                'route' => 'department/add/page',
                'active_routes' => json_encode(['department/add/page']),
                'pattern'   => null,
                'parent_id' => $departmentMenuId,
                'order'     => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Department Edit',
                'icon'  => null,
                'route' => null, // Placeholder, update with an actual route if needed
                'active_routes' => json_encode([]),
                'pattern'   => 'department/edit/*',
                'parent_id' => $departmentMenuId,
                'order'     => 3,
                'is_active' => true,
            ],
        ]);

        // Insert the "Subjects" menu and its submenus
        $subjectMenuId = DB::table('menus')->insertGetId([
            'title' => 'Subjects',
            'icon'  => 'fas fa-book-reader',
            'route' => null,
            'active_routes' => json_encode(['subject/list/page', 'subject/add/page']),
            'pattern'   => null,
            'parent_id' => null,
            'order'     => 14, // Ensure a unique order number
            'is_active' => true,
        ]);

        DB::table('menus')->insert([
            [
                'title' => 'Subject List',
                'icon'  => null,
                'route' => 'subject/list/page',
                'active_routes' => json_encode(['subject/list/page']),
                'pattern'   => null,
                'parent_id' => $subjectMenuId,
                'order'     => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Subject Add',
                'icon'  => null,
                'route' => 'subject/add/page',
                'active_routes' => json_encode(['subject/add/page']),
                'pattern'   => null,
                'parent_id' => $subjectMenuId,
                'order'     => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Subject Edit',
                'icon'  => null,
                'route' => null, // Placeholder; update with an actual route if needed
                'active_routes' => json_encode([]),
                'pattern'   => 'subject/edit/*',
                'parent_id' => $subjectMenuId,
                'order'     => 3,
                'is_active' => true,
            ],
        ]);

        // Insert the "Invoices" menu and its submenus
        $invoiceMenuId = DB::table('menus')->insertGetId([
            'title' => 'Invoices',
            'icon'  => 'fas fa-clipboard',
            'route' => null,
            'active_routes' => json_encode([
                'invoice/list/page', 'invoice/paid/page', 'invoice/overdue/page',
                'invoice/draft/page', 'invoice/recurring/page', 'invoice/cancelled/page',
                'invoice/grid/page', 'invoice/add/page', 'invoice/settings/page',
                'invoice/settings/tax/page', 'invoice/settings/bank/page'
            ]),
            'pattern'   => 'invoice/edit/*|invoice/view/*',
            'parent_id' => null,
            'order'     => 15, // Ensure this order number doesn't conflict
            'is_active' => true,
        ]);

        DB::table('menus')->insert([
            [
                'title' => 'Invoices List',
                'icon'  => null,
                'route' => 'invoice/list/page',
                'active_routes' => json_encode([
                    'invoice/list/page', 'invoice/paid/page', 'invoice/overdue/page',
                    'invoice/draft/page', 'invoice/recurring/page', 'invoice/cancelled/page'
                ]),
                'pattern'   => null,
                'parent_id' => $invoiceMenuId,
                'order'     => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Invoices Grid',
                'icon'  => null,
                'route' => 'invoice/grid/page',
                'active_routes' => json_encode(['invoice/grid/page']),
                'pattern'   => null,
                'parent_id' => $invoiceMenuId,
                'order'     => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Add Invoice',
                'icon'  => null,
                'route' => 'invoice/add/page',
                'active_routes' => json_encode(['invoice/add/page']),
                'pattern'   => null,
                'parent_id' => $invoiceMenuId,
                'order'     => 3,
                'is_active' => true,
            ],
            [
                'title' => 'Edit Invoice',
                'icon'  => null,
                'route' => null,
                'active_routes' => json_encode([]),
                'pattern'   => 'invoice/edit/*',
                'parent_id' => $invoiceMenuId,
                'order'     => 4,
                'is_active' => true,
            ],
            [
                'title' => 'Invoice Details',
                'icon'  => null,
                'route' => null,
                'active_routes' => json_encode([]),
                'pattern'   => 'invoice/view/*',
                'parent_id' => $invoiceMenuId,
                'order'     => 5,
                'is_active' => true,
            ],
            [
                'title' => 'Invoices Settings',
                'icon'  => null,
                'route' => 'invoice/settings/page',
                'active_routes' => json_encode(['invoice/settings/page', 'invoice/settings/tax/page', 'invoice/settings/bank/page']),
                'pattern'   => null,
                'parent_id' => $invoiceMenuId,
                'order'     => 6,
                'is_active' => true,
            ],
        ]);

        // Insert the "Accounts" menu as a parent menu
        $accountsMenuId = DB::table('menus')->insertGetId([
            'title' => 'Accounts',
            'icon'  => 'fas fa-file-invoice-dollar',
            'route' => null,
            'active_routes' => json_encode(['account/fees/collections/page', 'add/fees/collection/page']),
            'pattern'   => null,
            'parent_id' => null,
            'order'     => 16, // Adjust the order based on your menu structure
            'is_active' => true,
        ]);

        // Insert submenus for "Accounts"
        DB::table('menus')->insert([
            [
                'title' => 'Fees Collection',
                'icon'  => null,
                'route' => 'account/fees/collections/page',
                'active_routes' => json_encode(['account/fees/collections/page']),
                'pattern'   => null,
                'parent_id' => $accountsMenuId,
                'order'     => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Expenses',
                'icon'  => null,
                'route' => null, // Add the route if available
                'active_routes' => json_encode([]),
                'pattern'   => null,
                'parent_id' => $accountsMenuId,
                'order'     => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Salary',
                'icon'  => null,
                'route' => null, // Add the route if available
                'active_routes' => json_encode([]),
                'pattern'   => null,
                'parent_id' => $accountsMenuId,
                'order'     => 3,
                'is_active' => true,
            ],
            [
                'title' => 'Add Fees',
                'icon'  => null,
                'route' => 'add/fees/collection/page',
                'active_routes' => json_encode(['add/fees/collection/page']),
                'pattern'   => null,
                'parent_id' => $accountsMenuId,
                'order'     => 4,
                'is_active' => true,
            ],
            [
                'title' => 'Add Expenses',
                'icon'  => null,
                'route' => null, // Add the route if available
                'active_routes' => json_encode([]),
                'pattern'   => null,
                'parent_id' => $accountsMenuId,
                'order'     => 5,
                'is_active' => true,
            ],
            [
                'title' => 'Add Salary',
                'icon'  => null,
                'route' => null, // Add the route if available
                'active_routes' => json_encode([]),
                'pattern'   => null,
                'parent_id' => $accountsMenuId,
                'order'     => 6,
                'is_active' => true,
            ],
        ]);
    }
}

