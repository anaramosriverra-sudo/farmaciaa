<?php

namespace App\Providers;

use App\Models\Medicine;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;

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
        Schema::defaultStringLength(191);

        // Campana del encabezado: medicamentos con stock bajo (≤ 10), datos reales de la BD
        View::composer('partials.header', function ($view) {
            $lowStock = Medicine::where('stock', '<=', 10);

            $view->with([
                'lowStockCount' => (clone $lowStock)->count(),
                'lowStockAlerts' => (clone $lowStock)->orderBy('stock')->orderBy('name')->limit(5)->get(),
            ]);
        });
    }
}
