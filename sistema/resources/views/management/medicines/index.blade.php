@extends('template.administrador')

@section('title', 'MEDICAMENTOS')
@section('name-section', 'MEDICAMENTOS')
@section('descripcion', 'Inventario de medicamentos')
@section('module-breadcrumb', 'Gestión')
@section('section-breadcrumb', 'Medicamentos')
@section('item-breadcrumb', 'Listado')

@section('content')
<div class="card bg-white border-0 shadow-sm border-5 border-top border-secondary">
  <div class="card-header bg-white">
    <div class="row g-2">
      <div class="col-12 col-md-3">
        <a href="{{ route('medicines.create') }}" class="btn btn-primary rounded-3 text-uppercase text-white w-100">
          <i class="bi bi-plus-lg me-2"></i><span class="small fw-medium">Nuevo registro</span>
        </a>
      </div>
      <div class="col-12 col-md-4 ms-md-auto">
        <input type="search" class="form-control" placeholder="Buscar..." data-table-filter="#tabla">
      </div>
    </div>
  </div>
  <div class="card-body">
    <h6 class="text-uppercase small text-muted mb-3">Total de registros encontrados: {{ $medicines->count() }}</h6>
    <div class="table-responsive">
      <table id="tabla" class="table table-sm table-hover w-100 bg-white align-middle">
        <thead>
          <tr>
            <th class="text-center text-uppercase small">Nº</th>
            <th class="text-center text-uppercase small">Nombre</th>
            <th class="text-center text-uppercase small">Categoría</th>
            <th class="text-center text-uppercase small">Precio</th>
            <th class="text-center text-uppercase small">Stock</th>
            <th class="text-center text-uppercase small">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse($medicines as $medicine)
            <tr>
              <td class="text-center">{{ $loop->iteration }}</td>
              <td>{{ $medicine->name }}</td>
              <td class="text-center">{{ $medicine->category->nombre ?? '—' }}</td>
              <td class="text-end">S/ {{ number_format($medicine->price, 2) }}</td>
              <td class="text-center">
                @if($medicine->stock <= \App\Models\Medicine::LOW_STOCK)
                  <span class="badge bg-danger-subtle text-danger rounded-pill">{{ $medicine->stock }}</span>
                @else
                  {{ $medicine->stock }}
                @endif
              </td>
              <td class="text-center">
                @include('partials.row-actions', [
                  'edit' => route('medicines.edit', $medicine),
                  'destroy' => route('medicines.destroy', $medicine),
                ])
              </td>
            </tr>
          @empty
            <tr><td colspan="6" class="text-center text-secondary py-4">No hay medicamentos registrados.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
