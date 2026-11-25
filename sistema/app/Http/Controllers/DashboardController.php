<?php

namespace App\Http\Controllers;

<<<<<<< Updated upstream
use Illuminate\Http\Request;
=======
use App\Models\Customer;
use App\Models\Medicamento;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;
>>>>>>> Stashed changes

class DashboardController extends Controller
{
    public function index()
    {
<<<<<<< Updated upstream

        return view('principal.dashboard.index');

    }
}
=======
        $hoy = now()->startOfDay();
        $mes = now()->startOfMonth();

        $ventasHoy = Sale::where('date', '>=', $hoy);
        $ventasMes = Sale::where('date', '>=', $mes);

        $stats = [
            'ventas_hoy_cant' => (clone $ventasHoy)->count(),
            'ventas_hoy_total' => (float) (clone $ventasHoy)->sum('total'),
            'ventas_mes_total' => (float) (clone $ventasMes)->sum('total'),
            'ventas_mes_cant' => (clone $ventasMes)->count(),
            'medicamentos' => Medicamento::count(),
            'stock_bajo' => Medicamento::stockBajo()->count(),
            'clientes' => Customer::count(),
        ];

        // Ventas de los últimos 7 días
        $inicio = now()->subDays(6)->startOfDay();
        $porDia = Sale::where('date', '>=', $inicio)->get()
            ->groupBy(fn ($s) => $s->date->format('Y-m-d'))
            ->map(fn ($g) => (float) $g->sum('total'));

        $semana = collect(range(6, 0))->map(function ($i) use ($porDia) {
            $f = now()->subDays($i);

            return [
                'label' => ucfirst($f->translatedFormat('D d')),
                'total' => $porDia[$f->format('Y-m-d')] ?? 0,
            ];
        });
        $maxSemana = max(1, $semana->max('total'));

        $ultimasVentas = Sale::with(['customer', 'user'])->orderByDesc('date')->orderByDesc('id')->limit(5)->get();
        $stockBajo = Medicamento::with('category')->stockBajo()->orderBy('stock')->limit(5)->get();

        $topMedicamentos = DB::table('details')
            ->join('sales', 'sales.id', '=', 'details.sale_id')
            ->join('medicamentos', 'medicamentos.id', '=', 'details.medicine_id')
            ->where('sales.date', '>=', $mes)
            ->groupBy('medicamentos.id', 'medicamentos.name')
            ->select('medicamentos.name', DB::raw('SUM(details.amount) as cantidad'))
            ->orderByDesc('cantidad')
            ->limit(5)
            ->get();

        return view('dashboard', compact('stats', 'semana', 'maxSemana', 'ultimasVentas', 'stockBajo', 'topMedicamentos'));
    }
}
>>>>>>> Stashed changes
