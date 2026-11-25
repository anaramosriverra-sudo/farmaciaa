@extends('template.administrador')

@section('title', 'DASHBOARD')
@section('name-section', 'DASHBOARD')
@section('descripcion', 'Gráficos y KPIs')
@section('module-breadcrumb', 'Principal')
@section('section-breadcrumb', 'Dashboard')

@section('content')
<div class="row g-3 mb-4">
  <div class="col-12 col-sm-6 col-xl-3">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-body">
        <div class="text-secondary small text-uppercase">Ventas de hoy</div>
        <div class="fs-3 fw-bold">S/ {{ number_format($kpis['today_total'], 2) }}</div>
        <div class="text-secondary small">{{ $kpis['today_count'] }} {{ $kpis['today_count'] === 1 ? 'venta' : 'ventas' }}</div>
      </div>
    </div>
  </div>
  <div class="col-12 col-sm-6 col-xl-3">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-body">
        <div class="text-secondary small text-uppercase">Ventas del mes</div>
        <div class="fs-3 fw-bold">S/ {{ number_format($kpis['month_total'], 2) }}</div>
        <div class="text-secondary small">{{ $kpis['month_count'] }} {{ $kpis['month_count'] === 1 ? 'venta' : 'ventas' }}</div>
      </div>
    </div>
  </div>
  <div class="col-12 col-sm-6 col-xl-3">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-body">
        <div class="text-secondary small text-uppercase">Medicamentos</div>
        <div class="fs-3 fw-bold">{{ $kpis['medicines'] }}</div>
        <div class="small {{ $kpis['low_stock'] > 0 ? 'text-danger' : 'text-secondary' }}">
          {{ $kpis['low_stock'] }} con stock bajo
        </div>
      </div>
    </div>
  </div>
  <div class="col-12 col-sm-6 col-xl-3">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-body">
        <div class="text-secondary small text-uppercase">Clientes</div>
        <div class="fs-3 fw-bold">{{ $kpis['customers'] }}</div>
        <a href="{{ route('sales.create') }}" class="small text-decoration-none">+ Nueva venta</a>
      </div>
    </div>
  </div>
</div>

<div class="row g-3 mb-4">
  <div class="col-12 col-lg-8">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-header bg-white fw-semibold">Ventas de los últimos 7 días</div>
      <div class="card-body">
        <div class="d-flex gap-2">
          @foreach($chart as $bar)
            <div class="flex-fill text-center">
              <div class="text-secondary" style="font-size:.7rem;min-height:1rem">
                {{ $bar['total'] > 0 ? number_format($bar['total'], 0) : '' }}
              </div>
              <div class="d-flex align-items-end" style="height:140px">
                <div class="bg-primary rounded-top w-100"
                     style="height: {{ max(2, round($bar['total'] / $chartMax * 100)) }}%"
                     title="S/ {{ number_format($bar['total'], 2) }}"></div>
              </div>
              <div class="small text-secondary mt-1">{{ $bar['label'] }}</div>
            </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>

  <div class="col-12 col-lg-4">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-header bg-white fw-semibold">Más vendidos del mes</div>
      <ul class="list-group list-group-flush">
        @forelse($topMedicines as $item)
          <li class="list-group-item d-flex justify-content-between align-items-center">
            <span class="small">{{ $item->name }}</span>
            <span class="badge bg-primary-subtle text-primary rounded-pill">{{ $item->qty }} u.</span>
          </li>
        @empty
          <li class="list-group-item text-secondary small">Aún no hay ventas este mes.</li>
        @endforelse
      </ul>
    </div>
  </div>
</div>

<div class="row g-3">
  <div class="col-12 col-lg-7">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <span class="fw-semibold">Últimas ventas</span>
        <a href="{{ route('sales.index') }}" class="small text-decoration-none">Ver todas</a>
      </div>
      <div class="table-responsive">
        <table class="table table-sm align-middle mb-0">
          <thead>
            <tr>
              <th class="small text-uppercase">Código</th>
              <th class="small text-uppercase">Cliente</th>
              <th class="small text-uppercase">Fecha</th>
              <th class="small text-uppercase text-end">Total</th>
            </tr>
          </thead>
          <tbody>
            @forelse($latestSales as $sale)
              <tr>
                <td><a href="{{ route('sales.show', $sale) }}" class="text-decoration-none">{{ $sale->code }}</a></td>
                <td>{{ $sale->customer->name ?? '—' }}</td>
                <td>{{ $sale->date->format('d/m/Y H:i') }}</td>
                <td class="text-end">S/ {{ number_format($sale->total, 2) }}</td>
              </tr>
            @empty
              <tr><td colspan="4" class="text-center text-secondary py-3">Sin ventas registradas.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="col-12 col-lg-5">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <span class="fw-semibold">Stock bajo</span>
        <a href="{{ route('medicines.index') }}" class="small text-decoration-none">Ver inventario</a>
      </div>
      <ul class="list-group list-group-flush">
        @forelse($lowStock as $medicine)
          <li class="list-group-item d-flex justify-content-between align-items-center">
            <span class="small">{{ $medicine->name }}</span>
            <span class="badge bg-danger-subtle text-danger rounded-pill">{{ $medicine->stock }}</span>
          </li>
        @empty
          <li class="list-group-item text-secondary small">Todo el inventario tiene stock suficiente.</li>
        @endforelse
      </ul>
    </div>
  </div>
</div>
@endsection
