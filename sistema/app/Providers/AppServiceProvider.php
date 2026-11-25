<?php

namespace App\Providers;

use App\Models\Medicine;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
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

        // Iniciales del usuario autenticado para el avatar del menú
        View::composer('partials.sidebar', function ($view) {
            $name = auth()->user()->name ?? 'U';
            $initials = collect(preg_split('/\\s+/', trim($name)))
                ->filter()->take(2)
                ->map(fn ($word) => Str::upper(Str::substr($word, 0, 1)))
                ->implode('');

            $view->with('authInitials', $initials ?: 'U');
        });
    }
}
