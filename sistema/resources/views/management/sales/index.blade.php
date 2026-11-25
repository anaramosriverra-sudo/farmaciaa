@extends('template.administrador')

@section('title', 'VENTAS')
@section('name-section', 'VENTAS')
@section('descripcion', 'Historial de ventas')
@section('module-breadcrumb', 'Gestión')
@section('section-breadcrumb', 'Ventas')
@section('item-breadcrumb', 'Listado')

@section('content')
<div class="card bg-white border-0 shadow-sm border-5 border-top border-secondary">
  <div class="card-header bg-white">
    <div class="row g-2">
      <div class="col-12 col-md-3">
        <a href="{{ route('sales.create') }}" class="btn btn-primary rounded-3 text-uppercase text-white w-100">
          <i class="bi bi-plus-lg me-2"></i><span class="small fw-medium">Nueva venta</span>
        </a>
      </div>
      <div class="col-12 col-md-4 ms-md-auto">
        <input type="search" class="form-control" placeholder="Buscar por código, cliente o vendedor..." data-table-filter="#tabla">
      </div>
    </div>
  </div>
  <div class="card-body">
    <h6 class="text-uppercase small text-muted mb-3">Total de registros encontrados: {{ $sales->count() }}</h6>
    <div class="table-responsive">
      <table id="tabla" class="table table-sm table-hover w-100 bg-white align-middle">
        <thead>
          <tr>
            <th class="text-center text-uppercase small">Código</th>
            <th class="text-center text-uppercase small">Fecha</th>
            <th class="text-center text-uppercase small">Cliente</th>
            <th class="text-center text-uppercase small">Vendedor</th>
            <th class="text-center text-uppercase small">IGV</th>
            <th class="text-center text-uppercase small">Total</th>
            <th class="text-center text-uppercase small">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse($sales as $sale)
            <tr>
              <td class="text-center fw-medium">{{ $sale->code }}</td>
              <td class="text-center">{{ $sale->date->format('d/m/Y H:i') }}</td>
              <td>{{ $sale->customer->name ?? '—' }}</td>
              <td>{{ $sale->user->name ?? '—' }}</td>
              <td class="text-end">S/ {{ number_format($sale->igv, 2) }}</td>
              <td class="text-end fw-semibold">S/ {{ number_format($sale->total, 2) }}</td>
              <td class="text-center">
                @include('partials.row-actions', [
                  'show' => route('sales.show', $sale),
                  'destroy' => route('sales.destroy', $sale),
                  'confirm' => '¿Anular esta venta? El stock será devuelto al inventario.',
                  'label' => 'Anular venta',
                ])
              </td>
            </tr>
          @empty
            <tr><td colspan="7" class="text-center text-secondary py-4">No hay ventas registradas.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
