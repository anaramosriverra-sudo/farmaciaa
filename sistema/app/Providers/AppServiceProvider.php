<?php

namespace App\Providers;

use App\Models\Medicine;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Alertas de stock bajo para la campana del encabezado
        View::composer('partials.header', function ($view) {
            $low = Medicine::where('stock', '<=', Medicine::LOW_STOCK);

            $view->with([
                'lowStockCount' => (clone $low)->count(),
                'lowStockAlerts' => (clone $low)->orderBy('stock')->orderBy('name')->limit(5)->get(),
            ]);
        });
    }
}
