<?php

namespace App\Providers;

use App\Models\Venture;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.admin', function ($view) {
            try {
                $view->with('sidebarVentures', Venture::orderBy('name')->get());
            } catch (\Throwable $e) {
                $view->with('sidebarVentures', collect());
            }
        });
    }
}
