<?php

namespace App\Providers;

use App\Models\Product;
use Illuminate\Support\Facades\Auth;
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
        // Share low stock count with the master navigation bar
        View::composer('layouts.app', function ($view) {
            if (Auth::check()) {
                try {
                    $lowStockCount = Product::where('stock_qty', '<=', 10)->count();
                    $view->with('navLowStockCount', $lowStockCount);
                } catch (\Throwable $e) {
                    $view->with('navLowStockCount', 0);
                }
            }
        });
    }
}
