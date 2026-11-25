@extends('template.administrador')

@section('title', 'Ventas')
@section('name-section', 'VENTAS')
@section('descripcion', 'Historial de ventas')
@section('module-breadcrumb', 'Gestión')
@section('section-breadcrumb', 'Ventas')

@section('content')
<div class="card bg-white border-0 shadow-sm border-5 border-top border-secondary">
  <div class="card-header bg-white">
    <div class="row g-2 justify-content-between">
      <div class="col-12 col-md-3">
        <a href="{{ route('sales.create') }}" class="btn btn-primary rounded-3 text-uppercase text-white w-100">
          <i class="bi bi-plus-lg me-2"></i><span class="small fw-medium">Nueva venta</span>
        </a>
      </div>
      <div class="col-12 col-md-9">
        <form method="GET" class="d-flex flex-wrap gap-2 justify-content-md-end">
          <input type="date" name="desde" value="{{ $desde }}" class="form-control form-control-sm w-auto" title="Desde">
          <input type="date" name="hasta" value="{{ $hasta }}" class="form-control form-control-sm w-auto" title="Hasta">
          <input type="text" name="q" value="{{ $q }}" class="form-control form-control-sm w-auto" placeholder="Código, cliente o vendedor">
          <button type="submit" class="btn btn-sm btn-outline-secondary"><i class="bi bi-search"></i></button>
          @if ($q !== '' || $desde || $hasta)
            <a href="{{ route('sales.index') }}" class="btn btn-sm btn-light" title="Limpiar"><i class="bi bi-x-lg"></i></a>
          @endif
        </form>
      </div>
    </div>
  </div>
  <div class="card-body">
    <h6 class="text-uppercase small text-muted mb-3">Total de registros encontrados: {{ $sales->total() }}</h6>
    <div class="table-responsive">
      <table class="table table-sm table-hover w-100 bg-white">
        <thead>
          <tr>
            <th class="text-center align-middle text-uppercase small">Código</th>
            <th class="text-center align-middle text-uppercase small">Fecha</th>
            <th class="text-center align-middle text-uppercase small">Cliente</th>
            <th class="text-center align-middle text-uppercase small">Vendedor</th>
            <th class="text-center align-middle text-uppercase small">Ítems</th>
            <th class="text-center align-middle text-uppercase small">IGV</th>
            <th class="text-center align-middle text-uppercase small">Total</th>
            <th class="text-center align-middle text-uppercase small">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($sales as $sale)
            <tr>
              <td class="text-center align-middle"><a href="{{ route('sales.show', $sale) }}" class="text-decoration-none fw-medium">{{ $sale->codigo }}</a></td>
              <td class="text-center align-middle">{{ $sale->date->format('d/m/Y H:i') }}</td>
              <td class="text-start align-middle">{{ $sale->customer?->name ?? 'Cliente general' }}</td>
              <td class="text-start align-middle">{{ $sale->user?->name }}</td>
              <td class="text-center align-middle">{{ $sale->details_count }}</td>
              <td class="text-end align-middle">S/ {{ number_format($sale->igv, 2) }}</td>
              <td class="text-end align-middle fw-semibold">S/ {{ number_format($sale->total, 2) }}</td>
              <td class="text-center align-middle">
                <div class="dropdown">
                  <button class="btn btn-light" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-three-dots-vertical"></i>
                  </button>
                  <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ route('sales.show', $sale) }}">Ver detalle</a></li>
                    @if (auth()->user()->isAdmin())
                      <li>
                        <form action="{{ route('sales.destroy', $sale) }}" method="POST"
                              class="js-confirm-delete" data-title="¿Anular venta?"
                              data-message="Se anulará la venta {{ $sale->codigo }} y el stock volverá al inventario.">
                          @csrf @method('DELETE')
                          <button type="submit" class="dropdown-item text-danger">Anular</button>
                        </form>
                      </li>
                    @endif
                  </ul>
                </div>
              </td>
            </tr>
          @empty
            <tr><td colspan="8" class="text-center text-secondary py-4">No se encontraron ventas.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    {{ $sales->links() }}
  </div>
</div>
@endsection
