@extends('template.administrador')

@section('title', 'Venta '.$sale->codigo)
@section('name-section', 'VENTAS')
@section('descripcion', 'Detalle de la venta '.$sale->codigo)
@section('module-breadcrumb', 'Gestión')
@section('section-breadcrumb', 'Ventas')
@section('section-url', route('sales.index'))
@section('item-breadcrumb', $sale->codigo)

@section('content')
<div class="card bg-white border-0 shadow-sm border-5 border-top border-secondary" id="comprobante">
  <div class="card-body">
    <div class="row g-3 mb-4">
      <div class="col-12 col-md-4">
        <div class="text-secondary small text-uppercase">Código</div>
        <div class="fw-bold fs-5">{{ $sale->codigo }}</div>
      </div>
      <div class="col-6 col-md-4">
        <div class="text-secondary small text-uppercase">Fecha</div>
        <div>{{ $sale->date->format('d/m/Y H:i') }}</div>
      </div>
      <div class="col-6 col-md-4">
        <div class="text-secondary small text-uppercase">Vendedor</div>
        <div>{{ $sale->user?->name }}</div>
      </div>
      <div class="col-12 col-md-4">
        <div class="text-secondary small text-uppercase">Cliente</div>
        <div>{{ $sale->customer?->name ?? 'Cliente general' }}</div>
      </div>
      <div class="col-6 col-md-4">
        <div class="text-secondary small text-uppercase">DNI</div>
        <div>{{ $sale->customer?->dni ?? '—' }}</div>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table table-sm align-middle">
        <thead>
          <tr class="small text-uppercase">
            <th>Medicamento</th>
            <th class="text-center">Cantidad</th>
            <th class="text-end">P. unitario</th>
            <th class="text-end">Subtotal</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($sale->details as $d)
            <tr>
              <td>{{ $d->medicamento?->name }}</td>
              <td class="text-center">{{ $d->amount }}</td>
              <td class="text-end">S/ {{ number_format($d->unit_price, 2) }}</td>
              <td class="text-end">S/ {{ number_format($d->subtotal, 2) }}</td>
            </tr>
          @endforeach
        </tbody>
        <tfoot>
          <tr><td colspan="3" class="text-end text-secondary">Subtotal (sin IGV)</td><td class="text-end">S/ {{ number_format($sale->total - $sale->igv, 2) }}</td></tr>
          <tr><td colspan="3" class="text-end text-secondary">IGV (18%)</td><td class="text-end">S/ {{ number_format($sale->igv, 2) }}</td></tr>
          <tr><td colspan="3" class="text-end fw-bold">TOTAL</td><td class="text-end fw-bold fs-5">S/ {{ number_format($sale->total, 2) }}</td></tr>
        </tfoot>
      </table>
    </div>

    <div class="mt-3 d-print-none">
      <button type="button" class="btn btn-outline-secondary" onclick="window.print()"><i class="bi bi-printer me-1"></i>Imprimir</button>
      <a href="{{ route('sales.index') }}" class="btn btn-secondary">Volver</a>
    </div>
  </div>
</div>
@endsection
