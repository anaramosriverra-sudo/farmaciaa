@extends('template.administrador')

@section('title', 'Reportes')
@section('name-section', 'REPORTES')
@section('descripcion', 'Reporte de ventas del '.$desde->format('d/m/Y').' al '.$hasta->format('d/m/Y'))
@section('module-breadcrumb', 'Principal')
@section('section-breadcrumb', 'Reportes')

@section('content')
<div class="card border-0 shadow-sm mb-3 d-print-none">
  <div class="card-body">
    <form method="GET" class="row g-2 align-items-end">
      <div class="col-6 col-md-3">
        <label for="desde" class="form-label small mb-1">Desde</label>
        <input type="date" name="desde" id="desde" value="{{ $desde->toDateString() }}" class="form-control form-control-sm @error('desde') is-invalid @enderror">
        @error('desde') <div class="invalid-feedback">{{ $message }}</div> @enderror
      </div>
      <div class="col-6 col-md-3">
        <label for="hasta" class="form-label small mb-1">Hasta</label>
        <input type="date" name="hasta" id="hasta" value="{{ $hasta->toDateString() }}" class="form-control form-control-sm @error('hasta') is-invalid @enderror">
        @error('hasta') <div class="invalid-feedback">{{ $message }}</div> @enderror
      </div>
      <div class="col-12 col-md-6 d-flex gap-2">
        <button type="submit" class="btn btn-sm btn-primary text-white"><i class="bi bi-funnel me-1"></i>Filtrar</button>
        <a href="{{ route('reports.index') }}" class="btn btn-sm btn-light">Este mes</a>
        <button type="button" class="btn btn-sm btn-outline-secondary ms-auto" onclick="window.print()"><i class="bi bi-printer me-1"></i>Imprimir</button>
      </div>
    </form>
  </div>
</div>

<div class="row g-3 mb-3">
  @foreach ([
    ['Ventas realizadas', $resumen['cantidad'], ''],
    ['Total vendido', 'S/ '.number_format($resumen['total'], 2), ''],
    ['IGV (18%)', 'S/ '.number_format($resumen['igv'], 2), ''],
    ['Venta neta', 'S/ '.number_format($resumen['neto'], 2), ''],
    ['Ticket promedio', 'S/ '.number_format($resumen['promedio'], 2), ''],
  ] as [$t, $v])
    <div class="col-6 col-lg">
      <div class="card border-0 shadow-sm h-100"><div class="card-body py-3">
        <div class="text-secondary small text-uppercase">{{ $t }}</div>
        <div class="fs-5 fw-bold">{{ $v }}</div>
      </div></div>
    </div>
  @endforeach
</div>

<div class="row g-3 mb-3">
  <div class="col-12 col-xl-6">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-header bg-white fw-semibold small text-uppercase">Ventas por día</div>
      <div class="table-responsive">
        <table class="table table-sm mb-0 align-middle">
          <thead><tr class="small text-uppercase"><th>Fecha</th><th class="text-center">Ventas</th><th class="text-end">Total</th></tr></thead>
          <tbody>
            @forelse ($porDia as $d)
              <tr><td>{{ $d['dia']->format('d/m/Y') }}</td><td class="text-center">{{ $d['cantidad'] }}</td><td class="text-end">S/ {{ number_format($d['total'], 2) }}</td></tr>
            @empty
              <tr><td colspan="3" class="text-center text-secondary py-3 small">Sin ventas en el rango seleccionado.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="col-12 col-xl-6">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-header bg-white fw-semibold small text-uppercase">Medicamentos más vendidos</div>
      <div class="table-responsive">
        <table class="table table-sm mb-0 align-middle">
          <thead><tr class="small text-uppercase"><th>Medicamento</th><th class="text-center">Unidades</th><th class="text-end">Monto</th></tr></thead>
          <tbody>
            @forelse ($topMedicamentos as $m)
              <tr><td>{{ $m->name }}</td><td class="text-center">{{ $m->cantidad }}</td><td class="text-end">S/ {{ number_format($m->monto, 2) }}</td></tr>
            @empty
              <tr><td colspan="3" class="text-center text-secondary py-3 small">Sin datos.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<div class="row g-3 mb-3">
  <div class="col-12 col-xl-6">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-header bg-white fw-semibold small text-uppercase">Ventas por categoría</div>
      <div class="table-responsive">
        <table class="table table-sm mb-0 align-middle">
          <thead><tr class="small text-uppercase"><th>Categoría</th><th class="text-center">Unidades</th><th class="text-end">Monto</th></tr></thead>
          <tbody>
            @forelse ($porCategoria as $c)
              <tr><td>{{ $c->nombre }}</td><td class="text-center">{{ $c->cantidad }}</td><td class="text-end">S/ {{ number_format($c->monto, 2) }}</td></tr>
            @empty
              <tr><td colspan="3" class="text-center text-secondary py-3 small">Sin datos.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="col-12 col-xl-6">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-header bg-white fw-semibold small text-uppercase">Ventas por vendedor</div>
      <div class="table-responsive">
        <table class="table table-sm mb-0 align-middle">
          <thead><tr class="small text-uppercase"><th>Vendedor</th><th class="text-center">Ventas</th><th class="text-end">Monto</th></tr></thead>
          <tbody>
            @forelse ($porVendedor as $v)
              <tr><td>{{ $v->name }}</td><td class="text-center">{{ $v->cantidad }}</td><td class="text-end">S/ {{ number_format($v->monto, 2) }}</td></tr>
            @empty
              <tr><td colspan="3" class="text-center text-secondary py-3 small">Sin datos.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<div class="card border-0 shadow-sm">
  <div class="card-header bg-white fw-semibold small text-uppercase">Estado del inventario (actual)</div>
  <div class="card-body">
    <div class="row g-3 mb-3">
      <div class="col-4"><div class="text-secondary small">Productos</div><div class="fw-bold">{{ $inventario['productos'] }}</div></div>
      <div class="col-4"><div class="text-secondary small">Unidades en stock</div><div class="fw-bold">{{ number_format($inventario['unidades']) }}</div></div>
      <div class="col-4"><div class="text-secondary small">Valor del inventario</div><div class="fw-bold">S/ {{ number_format($inventario['valor'], 2) }}</div></div>
    </div>
    <div class="table-responsive">
      <table class="table table-sm mb-0 align-middle">
        <thead><tr class="small text-uppercase"><th>Stock bajo (≤ {{ \App\Models\Medicamento::STOCK_BAJO }})</th><th>Categoría</th><th class="text-center">Stock</th></tr></thead>
        <tbody>
          @forelse ($inventario['stock_bajo'] as $m)
            <tr><td>{{ $m->name }}</td><td>{{ $m->category?->nombre }}</td><td class="text-center"><span class="badge {{ $m->stock === 0 ? 'bg-danger' : 'bg-warning text-dark' }}">{{ $m->stock }}</span></td></tr>
          @empty
            <tr><td colspan="3" class="text-center text-secondary py-3 small">No hay productos con stock bajo.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
