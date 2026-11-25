@extends('template.administrador')

@section('title', 'REPORTES')
@section('name-section', 'REPORTES')
@section('descripcion', 'Reporte de ventas del ' . \Illuminate\Support\Carbon::parse($from)->format('d/m/Y') . ' al ' . \Illuminate\Support\Carbon::parse($to)->format('d/m/Y'))
@section('module-breadcrumb', 'Principal')
@section('section-breadcrumb', 'Reportes')

@push('styles')
<style>
  @media print {
    #sidebar, .app-header, .no-print, .sidebar-overlay { display: none !important; }
    #app-wrapper { height: auto; overflow: visible; }
    .app-content { overflow: visible !important; }
  }
</style>
@endpush

@section('content')
<div class="card border-0 shadow-sm mb-4 no-print">
  <div class="card-body">
    <form action="{{ route('reports.index') }}" method="GET" class="row g-2 align-items-end">
      <div class="col-6 col-md-3">
        <label for="from" class="form-label small mb-1">Desde</label>
        <input type="date" name="from" id="from" value="{{ old('from', $from) }}"
               class="form-control @error('from') is-invalid @enderror">
        @error('from')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="col-6 col-md-3">
        <label for="to" class="form-label small mb-1">Hasta</label>
        <input type="date" name="to" id="to" value="{{ old('to', $to) }}"
               class="form-control @error('to') is-invalid @enderror">
        @error('to')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="col-12 col-md-6 d-flex gap-2">
        <button type="submit" class="btn btn-primary text-white"><i class="bi bi-funnel me-1"></i>Filtrar</button>
        <a href="{{ route('reports.export', ['from' => $from, 'to' => $to]) }}" class="btn btn-outline-success">
          <i class="bi bi-file-earmark-spreadsheet me-1"></i>Exportar CSV
        </a>
        <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
          <i class="bi bi-printer me-1"></i>Imprimir
        </button>
      </div>
    </form>
  </div>
</div>

<div class="row g-3 mb-4">
  <div class="col-6 col-xl-3">
    <div class="card border-0 shadow-sm h-100"><div class="card-body">
      <div class="text-secondary small text-uppercase">Ventas</div>
      <div class="fs-4 fw-bold">{{ $summary['count'] }}</div>
    </div></div>
  </div>
  <div class="col-6 col-xl-3">
    <div class="card border-0 shadow-sm h-100"><div class="card-body">
      <div class="text-secondary small text-uppercase">Total vendido</div>
      <div class="fs-4 fw-bold">S/ {{ number_format($summary['total'], 2) }}</div>
    </div></div>
  </div>
  <div class="col-6 col-xl-3">
    <div class="card border-0 shadow-sm h-100"><div class="card-body">
      <div class="text-secondary small text-uppercase">IGV</div>
      <div class="fs-4 fw-bold">S/ {{ number_format($summary['igv'], 2) }}</div>
    </div></div>
  </div>
  <div class="col-6 col-xl-3">
    <div class="card border-0 shadow-sm h-100"><div class="card-body">
      <div class="text-secondary small text-uppercase">Ticket promedio</div>
      <div class="fs-4 fw-bold">S/ {{ number_format($summary['average'], 2) }}</div>
    </div></div>
  </div>
</div>

<div class="row g-3 mb-4">
  <div class="col-12 col-lg-4">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-header bg-white fw-semibold">Ventas por día</div>
      <div class="table-responsive">
        <table class="table table-sm mb-0">
          <thead><tr><th class="small text-uppercase">Día</th><th class="small text-uppercase text-center">Ventas</th><th class="small text-uppercase text-end">Total</th></tr></thead>
          <tbody>
            @forelse($perDay as $row)
              <tr>
                <td>{{ $row['day'] }}</td>
                <td class="text-center">{{ $row['count'] }}</td>
                <td class="text-end">S/ {{ number_format($row['total'], 2) }}</td>
              </tr>
            @empty
              <tr><td colspan="3" class="text-center text-secondary py-3">Sin datos en el rango.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="col-12 col-lg-4">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-header bg-white fw-semibold">Medicamentos más vendidos</div>
      <div class="table-responsive">
        <table class="table table-sm mb-0">
          <thead><tr><th class="small text-uppercase">Medicamento</th><th class="small text-uppercase text-center">Unid.</th><th class="small text-uppercase text-end">Total</th></tr></thead>
          <tbody>
            @forelse($byMedicine as $row)
              <tr>
                <td>{{ $row->name }}</td>
                <td class="text-center">{{ $row->qty }}</td>
                <td class="text-end">S/ {{ number_format($row->total, 2) }}</td>
              </tr>
            @empty
              <tr><td colspan="3" class="text-center text-secondary py-3">Sin datos en el rango.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="col-12 col-lg-4">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-header bg-white fw-semibold">Ventas por categoría</div>
      <div class="table-responsive">
        <table class="table table-sm mb-0">
          <thead><tr><th class="small text-uppercase">Categoría</th><th class="small text-uppercase text-center">Unid.</th><th class="small text-uppercase text-end">Total</th></tr></thead>
          <tbody>
            @forelse($byCategory as $row)
              <tr>
                <td>{{ $row->name }}</td>
                <td class="text-center">{{ $row->qty }}</td>
                <td class="text-end">S/ {{ number_format($row->total, 2) }}</td>
              </tr>
            @empty
              <tr><td colspan="3" class="text-center text-secondary py-3">Sin datos en el rango.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<div class="card border-0 shadow-sm mb-4">
  <div class="card-header bg-white fw-semibold">Detalle de ventas</div>
  <div class="table-responsive">
    <table class="table table-sm table-hover align-middle mb-0">
      <thead>
        <tr>
          <th class="small text-uppercase">Código</th>
          <th class="small text-uppercase">Fecha</th>
          <th class="small text-uppercase">Cliente</th>
          <th class="small text-uppercase">Vendedor</th>
          <th class="small text-uppercase text-end">IGV</th>
          <th class="small text-uppercase text-end">Total</th>
        </tr>
      </thead>
      <tbody>
        @forelse($sales as $sale)
          <tr>
            <td><a href="{{ route('sales.show', $sale) }}" class="text-decoration-none">{{ $sale->code }}</a></td>
            <td>{{ $sale->date->format('d/m/Y H:i') }}</td>
            <td>{{ $sale->customer->name ?? '—' }}</td>
            <td>{{ $sale->user->name ?? '—' }}</td>
            <td class="text-end">S/ {{ number_format($sale->igv, 2) }}</td>
            <td class="text-end">S/ {{ number_format($sale->total, 2) }}</td>
          </tr>
        @empty
          <tr><td colspan="6" class="text-center text-secondary py-3">No hay ventas en el rango seleccionado.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<div class="card border-0 shadow-sm">
  <div class="card-header bg-white fw-semibold">Medicamentos con stock bajo (≤ {{ \App\Models\Medicine::LOW_STOCK }} unidades)</div>
  <div class="table-responsive">
    <table class="table table-sm align-middle mb-0">
      <thead>
        <tr>
          <th class="small text-uppercase">Medicamento</th>
          <th class="small text-uppercase">Categoría</th>
          <th class="small text-uppercase text-center">Stock</th>
        </tr>
      </thead>
      <tbody>
        @forelse($lowStock as $medicine)
          <tr>
            <td>{{ $medicine->name }}</td>
            <td>{{ $medicine->category->nombre ?? '—' }}</td>
            <td class="text-center"><span class="badge bg-danger-subtle text-danger rounded-pill">{{ $medicine->stock }}</span></td>
          </tr>
        @empty
          <tr><td colspan="3" class="text-center text-secondary py-3">Todo el inventario tiene stock suficiente.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
