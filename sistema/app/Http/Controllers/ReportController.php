<?php

namespace App\Http\Controllers;

use App\Models\DetailSale;
use App\Models\Medicine;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReportController extends Controller
{
    /** Devuelve [desde, hasta] validados (por defecto: el mes en curso). */
    private function range(Request $request): array
    {
        $request->validate([
            'from' => 'nullable|date',
            'to' => 'nullable|date|after_or_equal:from',
        ]);

        $from = $request->filled('from') ? Carbon::parse($request->input('from')) : now()->startOfMonth();
        $to = $request->filled('to') ? Carbon::parse($request->input('to')) : now();

        return [$from->copy()->startOfDay(), $to->copy()->endOfDay()];
    }

    public function index(Request $request)
    {
        [$start, $end] = $this->range($request);

        $sales = Sale::with(['customer', 'user'])
            ->whereBetween('date', [$start, $end])
            ->orderByDesc('date')->orderByDesc('id')
            ->get();

        $summary = [
            'count' => $sales->count(),
            'total' => (float) $sales->sum('total'),
            'igv' => (float) $sales->sum('igv'),
            'average' => $sales->count() ? (float) $sales->avg('total') : 0.0,
        ];

        $perDay = $sales
            ->groupBy(fn ($sale) => $sale->date->toDateString())
            ->map(fn ($group, $day) => [
                'day' => Carbon::parse($day)->format('d/m/Y'),
                'count' => $group->count(),
                'total' => (float) $group->sum('total'),
            ])
            ->sortKeys()
            ->values();

        $byMedicine = DetailSale::query()
            ->join('sales', 'sales.id', '=', 'detail_sales.sale_id')
            ->join('medicines', 'medicines.id', '=', 'detail_sales.medicine_id')
            ->whereBetween('sales.date', [$start, $end])
            ->selectRaw('medicines.name as name, SUM(detail_sales.amount) as qty, SUM(detail_sales.subtotal) as total')
            ->groupBy('medicines.id', 'medicines.name')
            ->orderByDesc('qty')
            ->limit(10)
            ->get();

        $byCategory = DetailSale::query()
            ->join('sales', 'sales.id', '=', 'detail_sales.sale_id')
            ->join('medicines', 'medicines.id', '=', 'detail_sales.medicine_id')
            ->join('categories', 'categories.id', '=', 'medicines.category_id')
            ->whereBetween('sales.date', [$start, $end])
            ->selectRaw('categories.nombre as name, SUM(detail_sales.amount) as qty, SUM(detail_sales.subtotal) as total')
            ->groupBy('categories.id', 'categories.nombre')
            ->orderByDesc('total')
            ->get();

        $lowStock = Medicine::with('category')
            ->where('stock', '<=', Medicine::LOW_STOCK)
            ->orderBy('stock')->orderBy('name')
            ->get();

        return view('reports.index', [
            'from' => $start->toDateString(),
            'to' => $end->toDateString(),
            'sales' => $sales,
            'summary' => $summary,
            'perDay' => $perDay,
            'byMedicine' => $byMedicine,
            'byCategory' => $byCategory,
            'lowStock' => $lowStock,
        ]);
    }

    /** Exporta las ventas del rango a CSV (compatible con Excel). */
    public function export(Request $request)
    {
        [$start, $end] = $this->range($request);

        $sales = Sale::with(['customer', 'user'])
            ->whereBetween('date', [$start, $end])
            ->orderBy('date')->orderBy('id')
            ->get();

        $filename = 'ventas_' . $start->format('Ymd') . '_' . $end->format('Ymd') . '.csv';

        return response()->streamDownload(function () use ($sales) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM UTF-8 para Excel
            fputcsv($out, ['Código', 'Fecha', 'Cliente', 'DNI', 'Vendedor', 'Subtotal', 'IGV', 'Total']);

            foreach ($sales as $sale) {
                fputcsv($out, [
                    $sale->code,
                    $sale->date->format('d/m/Y H:i'),
                    $sale->customer->name ?? '',
                    $sale->customer->dni ?? '',
                    $sale->user->name ?? '',
                    $sale->subtotal,
                    $sale->igv,
                    $sale->total,
                ]);
            }

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
