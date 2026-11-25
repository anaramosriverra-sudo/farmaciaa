<?php

namespace App\Providers;

<<<<<<< Updated upstream
=======
use App\Models\Medicamento;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
>>>>>>> Stashed changes
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
<<<<<<< Updated upstream
    /**
     * Register any application services.
     */
=======
>>>>>>> Stashed changes
    public function register(): void
    {
        //
    }

<<<<<<< Updated upstream
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
=======
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // Notificaciones de stock bajo en la cabecera (campana)
        View::composer('partials.header', function ($view) {
            $alertas = collect();
            $total = 0;

            if (Schema::hasTable('medicamentos')) {
                $total = Medicamento::stockBajo()->count();
                $alertas = Medicamento::stockBajo()->orderBy('stock')->limit(5)->get();
            }

            $view->with('alertasStock', $alertas)->with('totalAlertasStock', $total);
        });
>>>>>>> Stashed changes
    }
}
