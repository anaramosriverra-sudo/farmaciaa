<?php

namespace App\Http\Controllers;

use App\Models\Medicamento;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'desde' => ['nullable', 'date'],
            'hasta' => ['nullable', 'date', 'after_or_equal:desde'],
        ]);

        $desde = Carbon::parse($request->query('desde', now()->startOfMonth()->toDateString()))->startOfDay();
        $hasta = Carbon::parse($request->query('hasta', now()->toDateString()))->endOfDay();

        $sales = Sale::whereBetween('date', [$desde, $hasta])->get();

        $resumen = [
            'cantidad' => $sales->count(),
            'total' => (float) $sales->sum('total'),
            'igv' => (float) $sales->sum('igv'),
            'neto' => (float) $sales->sum('total') - (float) $sales->sum('igv'),
            'promedio' => $sales->count() ? (float) $sales->avg('total') : 0,
        ];

        $porDia = $sales->groupBy(fn ($s) => $s->date->format('Y-m-d'))
            ->map(fn ($g, $dia) => [
                'dia' => Carbon::parse($dia),
                'cantidad' => $g->count(),
                'total' => (float) $g->sum('total'),
            ])->sortKeys()->values();

        $base = DB::table('details')
            ->join('sales', 'sales.id', '=', 'details.sale_id')
            ->whereBetween('sales.date', [$desde, $hasta]);

        $topMedicamentos = (clone $base)
            ->join('medicamentos', 'medicamentos.id', '=', 'details.medicine_id')
            ->groupBy('medicamentos.id', 'medicamentos.name')
            ->select('medicamentos.name', DB::raw('SUM(details.amount) as cantidad'), DB::raw('SUM(details.subtotal) as monto'))
            ->orderByDesc('cantidad')->limit(10)->get();

        $porCategoria = (clone $base)
            ->join('medicamentos', 'medicamentos.id', '=', 'details.medicine_id')
            ->join('categories', 'categories.id', '=', 'medicamentos.category_id')
            ->groupBy('categories.id', 'categories.nombre')
            ->select('categories.nombre', DB::raw('SUM(details.amount) as cantidad'), DB::raw('SUM(details.subtotal) as monto'))
            ->orderByDesc('monto')->get();

        $porVendedor = DB::table('sales')
            ->join('users', 'users.id', '=', 'sales.user_id')
            ->whereBetween('sales.date', [$desde, $hasta])
            ->groupBy('users.id', 'users.name')
            ->select('users.name', DB::raw('COUNT(sales.id) as cantidad'), DB::raw('SUM(sales.total) as monto'))
            ->orderByDesc('monto')->get();

        $inventario = [
            'productos' => Medicamento::count(),
            'unidades' => (int) Medicamento::sum('stock'),
            'valor' => (float) Medicamento::sum(DB::raw('price * stock')),
            'stock_bajo' => Medicamento::with('category')->stockBajo()->orderBy('stock')->limit(10)->get(),
        ];

        return view('reports.index', [
            'desde' => $desde,
            'hasta' => $hasta,
            'resumen' => $resumen,
            'porDia' => $porDia,
            'topMedicamentos' => $topMedicamentos,
            'porCategoria' => $porCategoria,
            'porVendedor' => $porVendedor,
            'inventario' => $inventario,
        ]);
    }
}
