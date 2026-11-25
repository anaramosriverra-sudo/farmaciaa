@extends('template.administrador')

@section('title', 'VENTAS')
@section('name-section', 'VENTAS')
@section('descripcion', 'Detalle de la venta ' . $sale->code)
@section('module-breadcrumb', 'Gestión')
@section('section-breadcrumb', 'Ventas')
@section('item-breadcrumb', $sale->code)

@section('content')
<div class="card bg-white border-0 shadow-sm border-5 border-top border-secondary">
  <div class="card-body">
    <div class="row mb-4">
      <div class="col-md-4">
        <div class="text-secondary small text-uppercase">Código</div>
        <div class="fw-bold">{{ $sale->code }}</div>
      </div>
      <div class="col-md-4">
        <div class="text-secondary small text-uppercase">Fecha</div>
        <div>{{ $sale->date->format('d/m/Y H:i') }}</div>
      </div>
      <div class="col-md-4">
        <div class="text-secondary small text-uppercase">Vendedor</div>
        <div>{{ $sale->user->name ?? '—' }}</div>
      </div>
      <div class="col-md-4 mt-3">
        <div class="text-secondary small text-uppercase">Cliente</div>
        <div>{{ $sale->customer->name ?? '—' }}</div>
      </div>
      <div class="col-md-4 mt-3">
        <div class="text-secondary small text-uppercase">DNI</div>
        <div>{{ $sale->customer->dni ?? '—' }}</div>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table table-sm align-middle">
        <thead>
          <tr>
            <th class="text-uppercase small">Medicamento</th>
            <th class="text-center text-uppercase small">Cantidad</th>
            <th class="text-end text-uppercase small">Precio unit.</th>
            <th class="text-end text-uppercase small">Subtotal</th>
          </tr>
        </thead>
        <tbody>
          @foreach($sale->details as $detail)
            <tr>
              <td>{{ $detail->medicine->name ?? '—' }}</td>
              <td class="text-center">{{ $detail->amount }}</td>
              <td class="text-end">S/ {{ number_format($detail->price_unit, 2) }}</td>
              <td class="text-end">S/ {{ number_format($detail->subtotal, 2) }}</td>
            </tr>
          @endforeach
        </tbody>
        <tfoot>
          <tr>
            <td colspan="3" class="text-end text-secondary">Subtotal (sin IGV)</td>
            <td class="text-end">S/ {{ number_format($sale->subtotal, 2) }}</td>
          </tr>
          <tr>
            <td colspan="3" class="text-end text-secondary">IGV ({{ round(\App\Models\Sale::IGV_RATE * 100) }}%)</td>
            <td class="text-end">S/ {{ number_format($sale->igv, 2) }}</td>
          </tr>
          <tr>
            <td colspan="3" class="text-end fw-bold">TOTAL</td>
            <td class="text-end fw-bold">S/ {{ number_format($sale->total, 2) }}</td>
          </tr>
        </tfoot>
      </table>
    </div>

    <div class="d-flex gap-2 mt-3">
      <a href="{{ route('sales.index') }}" class="btn btn-secondary">Volver</a>
      <a href="{{ route('sales.create') }}" class="btn btn-primary text-white">Nueva venta</a>
      <button type="button" class="btn btn-outline-secondary ms-auto" onclick="window.print()">
        <i class="bi bi-printer me-1"></i>Imprimir
      </button>
    </div>
  </div>
</div>
@endsection
