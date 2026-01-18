<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use App\Models\Menu;
use View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrapFive();

        View::composer('*', function ($view) {
            static $menus;

            if ($menus === null) {
                $menus = collect();

                try {
                    DB::connection()->getPdo();

                    $menus = Menu::whereNull('parent_id')
                        ->where('is_active', true)
                        ->where('title', '!=', 'Settings')
                        ->with('children')
                        ->orderBy('order')
                        ->get();
                } catch (\Throwable $e) {
                    report($e);
                }
            }

            $view->with('menus', $menus);
        });
    }
}
