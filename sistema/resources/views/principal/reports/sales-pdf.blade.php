<!doctype html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Reporte de ventas</title>
{{-- DomPDF no usa Bootstrap: los estilos van aquí, simples y en línea --}}
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #323232; }
    .header { border-bottom: 3px solid #ff0a41; padding-bottom: 8px; margin-bottom: 12px; }
    .brand { font-size: 16px; font-weight: bold; }
    .brand span { color: #ff0a41; }
    .muted { color: #6b7280; }
    h1 { font-size: 14px; margin: 6px 0 2px; }
    table { width: 100%; border-collapse: collapse; }
    .filters td { padding: 2px 0; }
    .summary td { border: 1px solid #e5e7eb; padding: 6px; text-align: center; width: 25%; }
    .summary strong { display: block; font-size: 12px; margin-top: 2px; }
    .data { margin-top: 12px; }
    .data th { background: #323232; color: #fff; padding: 5px; text-align: left; }
    .data td { border-bottom: 1px solid #e5e7eb; padding: 4px 5px; }
    .data tfoot th { background: #f3f4f6; color: #323232; }
    .end { text-align: right !important; }
    .footer { position: fixed; bottom: -10px; left: 0; right: 0; text-align: center; font-size: 8px; color: #9ca3af; }
</style>
</head>
<body>
    <div class="header">
        <div class="brand"><span>+</span> FARM SJB · Farmacia San Juan</div>
        <div class="muted">Sistema de Venta de Medicamentos</div>
        <h1>Reporte de ventas</h1>
        <div class="muted">Del {{ \Carbon\Carbon::parse($from)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($to)->format('d/m/Y') }}
            · Generado el {{ now()->format('d/m/Y H:i') }} por {{ auth()->user()->name }}</div>
    </div>

    <table class="filters">
        @foreach ($filters as $label => $value)
            <tr><td style="width:80px"><strong>{{ $label }}:</strong></td><td>{{ $value }}</td></tr>
        @endforeach
    </table>

    <table class="summary" style="margin-top:10px">
        <tr>
            <td>N.° de ventas<strong>{{ $summary['count'] }}</strong></td>
            <td>Total vendido<strong>S/ {{ number_format($summary['total'], 2) }}</strong></td>
            <td>IGV<strong>S/ {{ number_format($summary['igv'], 2) }}</strong></td>
            <td>Venta promedio<strong>S/ {{ number_format($summary['average'], 2) }}</strong></td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr><th>N.°</th><th>Fecha</th><th>Cliente</th><th>Vendedor</th><th class="end">IGV</th><th class="end">Total</th></tr>
        </thead>
        <tbody>
            @forelse ($sales as $sale)
                <tr>
                    <td>{{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $sale->date->format('d/m/Y H:i') }}</td>
                    <td>{{ $sale->customer->name }}</td>
                    <td>{{ $sale->user->name }}</td>
                    <td class="end">S/ {{ number_format($sale->igv, 2) }}</td>
                    <td class="end">S/ {{ number_format($sale->total, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="6" style="text-align:center" class="muted">No hay ventas con esos filtros.</td></tr>
            @endforelse
        </tbody>
        @if ($sales->isNotEmpty())
            <tfoot>
                <tr><th colspan="4" class="end">Totales</th>
                    <th class="end">S/ {{ number_format($summary['igv'], 2) }}</th>
                    <th class="end">S/ {{ number_format($summary['total'], 2) }}</th></tr>
            </tfoot>
        @endif
    </table>

    <div class="footer">FARM SJB · Reporte generado automáticamente</div>
</body>
</html>
