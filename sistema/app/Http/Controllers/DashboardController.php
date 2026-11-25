<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\DetailSale;
use App\Models\Medicine;
use App\Models\Sale;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();

        $salesToday = Sale::whereDate('date', $today);
        $salesMonth = Sale::whereBetween('date', [$monthStart, $monthEnd]);

        $kpis = [
            'today_total' => (float) (clone $salesToday)->sum('total'),
            'today_count' => (clone $salesToday)->count(),
            'month_total' => (float) (clone $salesMonth)->sum('total'),
            'month_count' => (clone $salesMonth)->count(),
            'medicines' => Medicine::count(),
            'low_stock' => Medicine::where('stock', '<=', Medicine::LOW_STOCK)->count(),
            'customers' => Customer::count(),
        ];

        // Ventas de los últimos 7 días (con ceros en los días sin ventas)
        $from = now()->subDays(6)->startOfDay();
        $perDay = Sale::where('date', '>=', $from)
            ->get(['date', 'total'])
            ->groupBy(fn ($sale) => $sale->date->toDateString())
            ->map(fn ($group) => (float) $group->sum('total'));

        $chart = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = now()->subDays($i);
            $chart[] = [
                'label' => $day->format('d/m'),
                'total' => $perDay->get($day->toDateString(), 0.0),
            ];
        }
        $chartMax = max(1, collect($chart)->max('total'));

        $latestSales = Sale::with('customer')->orderByDesc('date')->orderByDesc('id')->limit(5)->get();

        $lowStock = Medicine::where('stock', '<=', Medicine::LOW_STOCK)
            ->orderBy('stock')->orderBy('name')->limit(6)->get();

        $topMedicines = DetailSale::query()
            ->join('sales', 'sales.id', '=', 'detail_sales.sale_id')
            ->join('medicines', 'medicines.id', '=', 'detail_sales.medicine_id')
            ->whereBetween('sales.date', [$monthStart, $monthEnd])
            ->selectRaw('medicines.name as name, SUM(detail_sales.amount) as qty')
            ->groupBy('medicines.id', 'medicines.name')
            ->orderByDesc('qty')
            ->limit(5)
            ->get();

        return view('principal.dashboard.index', compact(
            'kpis', 'chart', 'chartMax', 'latestSales', 'lowStock', 'topMedicines'
        ));
    }
}
