<!doctype html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Venta {{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}</title>
{{-- Comprobante de venta en PDF (DomPDF): estilos simples en línea --}}
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #323232; }
    .header { border-bottom: 3px solid #ff0a41; padding-bottom: 8px; margin-bottom: 12px; }
    .brand { font-size: 13px; font-weight: bold; }
    .brand span { color: #ff0a41; }
    .muted { color: #6b7280; }
    table { width: 100%; border-collapse: collapse; }
    .info td { padding: 3px 0; vertical-align: top; }
    .data { margin-top: 14px; }
    .data th { background: #323232; color: #fff; padding: 5px; text-align: left; }
    .data td { border-bottom: 1px solid #e5e7eb; padding: 5px; }
    .data tfoot td { border: 0; padding: 3px 5px; }
    .end { text-align: right !important; }
    .center { text-align: center !important; }
    .total { font-size: 13px; font-weight: bold; }
</style>
</head>
<body>
    <div class="header">
        <table>
            <tr>
                <td>
                    <div class="brand"><span>+</span> FARM SJB</div>
                    <div>Farmacia San Juan</div>
                    <div class="muted">Sistema de Venta de Medicamentos</div>
                </td>
                <td class="end">
                    <div style="font-size:11px; font-weight:bold; white-space:nowrap">COMPROBANTE DE VENTA</div>
                    <div>N.° {{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}</div>
                </td>
            </tr>
        </table>
    </div>

    <table class="info">
        <tr>
            <td style="width:50%"><strong>Fecha:</strong> {{ $sale->date->format('d/m/Y H:i') }}</td>
            <td><strong>Vendedor:</strong> {{ $sale->user->name }}</td>
        </tr>
        <tr>
            <td><strong>Cliente:</strong> {{ $sale->customer->name }}</td>
            <td><strong>DNI:</strong> {{ $sale->customer->dni }}</td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr><th class="center">N.°</th><th>Medicamento</th><th class="center">Cantidad</th><th class="end">Precio unit.</th><th class="end">Subtotal</th></tr>
        </thead>
        <tbody>
            @foreach ($sale->details as $detail)
                <tr>
                    <td class="center">{{ $loop->iteration }}</td>
                    <td>{{ $detail->medicine->name }}</td>
                    <td class="center">{{ $detail->amount }}</td>
                    <td class="end">S/ {{ number_format($detail->price_unit, 2) }}</td>
                    <td class="end">S/ {{ number_format($detail->subtotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            {{-- El precio ya incluye IGV: op. gravada = total - igv --}}
            <tr><td colspan="4" class="end">Op. gravada</td><td class="end">S/ {{ number_format($sale->total - $sale->igv, 2) }}</td></tr>
            <tr><td colspan="4" class="end">IGV (18%)</td><td class="end">S/ {{ number_format($sale->igv, 2) }}</td></tr>
            <tr><td colspan="4" class="end total">Total</td><td class="end total">S/ {{ number_format($sale->total, 2) }}</td></tr>
        </tfoot>
    </table>

    <p class="muted center" style="margin-top:30px">Gracias por su compra.</p>
</body>
</html>
