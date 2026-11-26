<?php

namespace App\Http\Controllers;

use App\Exports\SalesExport;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    // GET /reports/sales: reporte en pantalla
    public function sales(Request $request)
    {
        // 1-3. Ventas filtradas, rango de fechas y totales (ver filteredSales())
        ['sales' => $sales, 'from' => $from, 'to' => $to, 'summary' => $summary] = $this->filteredSales($request);

        // 4. Datos para el gráfico: total por día dentro del rango
        $chart = $sales->groupBy(fn ($sale) => $sale->date->format('d/m'))
            ->map(fn ($group) => round($group->sum('total'), 2));

        // 5. Listas para los <select> de los filtros
        $customers = Customer::orderBy('name')->get();
        $users = User::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

        return view('principal.reports.sales', compact(
            'sales', 'summary', 'chart', 'from', 'to', 'customers', 'users', 'categories'
        ));
    }

    // GET /reports/sales/pdf: el mismo reporte en PDF (librería DomPDF)
    public function salesPdf(Request $request)
    {
        $data = $this->filteredSales($request);
        $data['filters'] = $this->filterLabels($request);

        // loadView() convierte una vista Blade en PDF; setPaper() elige el tamaño y la orientación
        $pdf = Pdf::loadView('principal.reports.sales-pdf', $data)->setPaper('a4', 'portrait')
            ->setOption('enable_font_subsetting', true); // solo incluye las letras usadas: PDF más liviano

        // download(): el navegador descarga el archivo
        return $pdf->download("reporte-ventas-{$data['from']}-al-{$data['to']}.pdf");
    }

    // GET /reports/sales/excel: el mismo reporte en Excel (librería maatwebsite/excel)
    public function salesExcel(Request $request)
    {
        ['sales' => $sales, 'from' => $from, 'to' => $to] = $this->filteredSales($request);

        // SalesExport (app/Exports) define las columnas y el formato de la hoja
        return Excel::download(new SalesExport($sales), "reporte-ventas-{$from}-al-{$to}.xlsx");
    }

    // Consulta compartida por la pantalla, el PDF y el Excel: así los tres muestran lo mismo
    private function filteredSales(Request $request): array
    {
        // 1. Valida los filtros. Todos son opcionales; si no llegan fechas, se usa el mes actual
        $request->validate([
            'from' => 'nullable|date',
            'to' => 'nullable|date|after_or_equal:from',
            'customer_id' => 'nullable|exists:customers,id',
            'user_id' => 'nullable|exists:users,id',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to = $request->input('to', now()->toDateString());

        // 2. Consulta con filtros dinámicos: cada when() se aplica solo si el filtro tiene valor
        $sales = Sale::with(['customer', 'user'])
            ->whereBetween('date', [$from . ' 00:00:00', $to . ' 23:59:59']) // rango de fechas
            ->when($request->customer_id, fn ($q, $id) => $q->where('customer_id', $id))
            ->when($request->user_id, fn ($q, $id) => $q->where('user_id', $id))
            // whereHas: ventas que tienen AL MENOS un detalle de un medicamento de esa categoría
            ->when($request->category_id, fn ($q, $id) =>
                $q->whereHas('details.medicine', fn ($m) => $m->where('category_id', $id)))
            ->orderBy('date')
            ->get();

        // 3. Totales del reporte (se calculan sobre la colección ya filtrada)
        $summary = [
            'count' => $sales->count(),
            'total' => $sales->sum('total'),
            'igv' => $sales->sum('igv'),
            'average' => $sales->avg('total') ?? 0,
        ];

        return compact('sales', 'from', 'to', 'summary');
    }

    // Nombres de los filtros elegidos, para mostrarlos en el encabezado del PDF
    private function filterLabels(Request $request): array
    {
        return [
            'Cliente' => $request->customer_id ? Customer::find($request->customer_id)?->name : 'Todos',
            'Vendedor' => $request->user_id ? User::find($request->user_id)?->name : 'Todos',
            'Categoría' => $request->category_id ? Category::find($request->category_id)?->name : 'Todas',
        ];
    }
}
