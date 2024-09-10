<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\WebSetup\SidebarNav;

class ViewServiceProvider extends ServiceProvider
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
        // Share the sidebar navigation data with all views
        View::composer('*', function ($view) {
            $navItems = SidebarNav::whereNull('parent_id')
                ->where('status', 'A')
                ->with('children')
                ->orderBy('order')
                ->get();

            $view->with('navItems', $navItems);
        });
    }
}
