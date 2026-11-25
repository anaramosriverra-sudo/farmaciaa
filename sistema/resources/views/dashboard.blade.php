@extends('template.administrador')

@section('title', 'Dashboard')
@section('name-section', 'Dashboard')
@section('descripcion', 'Resumen general de la farmacia')
@section('module-breadcrumb', 'Principal')
@section('section-breadcrumb', 'Dashboard')

@section('content')
<div class="row g-3 mb-4">
  @php
    $kpis = [
      ['Ventas de hoy', 'S/ '.number_format($stats['ventas_hoy_total'], 2), $stats['ventas_hoy_cant'].' venta(s)', 'bi-cash-coin', 'primary'],
      ['Ventas del mes', 'S/ '.number_format($stats['ventas_mes_total'], 2), $stats['ventas_mes_cant'].' venta(s)', 'bi-graph-up-arrow', 'success'],
      ['Medicamentos', $stats['medicamentos'], $stats['stock_bajo'].' con stock bajo', 'bi-box-seam', 'warning'],
      ['Clientes', $stats['clientes'], 'registrados', 'bi-people', 'info'],
    ];
  @endphp
  @foreach ($kpis as [$titulo, $valor, $nota, $icono, $color])
    <div class="col-12 col-sm-6 col-xl-3">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body d-flex align-items-center gap-3">
          <div class="rounded-3 bg-{{ $color }}-subtle text-{{ $color }} d-flex align-items-center justify-content-center flex-shrink-0" style="width:52px;height:52px">
            <i class="bi {{ $icono }} fs-4"></i>
          </div>
          <div class="overflow-hidden">
            <div class="text-secondary small text-uppercase">{{ $titulo }}</div>
            <div class="fs-4 fw-bold lh-sm">{{ $valor }}</div>
            <div class="text-secondary" style="font-size:.75rem">{{ $nota }}</div>
          </div>
        </div>
      </div>
    </div>
  @endforeach
</div>

<div class="row g-3 mb-4">
  <div class="col-12 col-xl-7">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-header bg-white fw-semibold small text-uppercase">Ventas de los últimos 7 días</div>
      <div class="card-body">
        <div class="d-flex align-items-end justify-content-between gap-2" style="height:200px">
          @foreach ($semana as $d)
            <div class="flex-fill d-flex flex-column align-items-center justify-content-end h-100">
              <div class="small text-secondary mb-1" style="font-size:.7rem">{{ $d['total'] > 0 ? number_format($d['total'], 0) : '' }}</div>
              <div class="w-100 bg-primary rounded-top" style="height:{{ $d['total'] > 0 ? max(4, round($d['total'] / $maxSemana * 150)) : 2 }}px;{{ $d['total'] > 0 ? '' : 'opacity:.25' }}"></div>
              <div class="text-secondary mt-2 text-center" style="font-size:.7rem">{{ $d['label'] }}</div>
            </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>

  <div class="col-12 col-xl-5">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-header bg-white fw-semibold small text-uppercase">Más vendidos del mes</div>
      <ul class="list-group list-group-flush">
        @forelse ($topMedicamentos as $i => $m)
          <li class="list-group-item d-flex justify-content-between align-items-center">
            <span class="small"><span class="text-secondary me-2">{{ $i + 1 }}.</span>{{ $m->name }}</span>
            <span class="badge bg-primary-subtle text-primary rounded-pill">{{ $m->cantidad }} und.</span>
          </li>
        @empty
          <li class="list-group-item text-secondary small">Aún no hay ventas este mes.</li>
        @endforelse
      </ul>
    </div>
  </div>
</div>

<div class="row g-3">
  <div class="col-12 col-xl-7">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <span class="fw-semibold small text-uppercase">Últimas ventas</span>
        <a href="{{ route('sales.index') }}" class="small text-decoration-none">Ver todas</a>
      </div>
      <div class="table-responsive">
        <table class="table table-sm table-hover align-middle mb-0">
          <thead><tr class="small text-uppercase text-center"><th>Código</th><th>Fecha</th><th>Cliente</th><th>Total</th></tr></thead>
          <tbody>
            @forelse ($ultimasVentas as $v)
              <tr>
                <td class="text-center"><a href="{{ route('sales.show', $v) }}" class="text-decoration-none">{{ $v->codigo }}</a></td>
                <td class="text-center small">{{ $v->date->format('d/m/Y H:i') }}</td>
                <td class="small">{{ $v->customer?->name ?? 'Cliente general' }}</td>
                <td class="text-end pe-3">S/ {{ number_format($v->total, 2) }}</td>
              </tr>
            @empty
              <tr><td colspan="4" class="text-center text-secondary small py-3">No hay ventas registradas.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="col-12 col-xl-5">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <span class="fw-semibold small text-uppercase">Stock bajo (≤ {{ \App\Models\Medicamento::STOCK_BAJO }})</span>
        <a href="{{ route('medicamentos.index') }}" class="small text-decoration-none">Ver todos</a>
      </div>
      <ul class="list-group list-group-flush">
        @forelse ($stockBajo as $m)
          <li class="list-group-item d-flex justify-content-between align-items-center">
            <span class="small">{{ $m->name }}<br><span class="text-secondary" style="font-size:.72rem">{{ $m->category?->nombre }}</span></span>
            <span class="badge {{ $m->stock === 0 ? 'bg-danger' : 'bg-warning text-dark' }} rounded-pill">{{ $m->stock }}</span>
          </li>
        @empty
          <li class="list-group-item text-secondary small"><i class="bi bi-check-circle text-success me-1"></i>Todo el inventario está sobre el mínimo.</li>
        @endforelse
      </ul>
    </div>
  </div>
</div>
@endsection
