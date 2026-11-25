@extends('template.administrador')

@section('title', 'CATEGORIAS')
@section('name-section', 'CATEGORIAS')
@section('descripcion', 'Listado de categorías')
@section('module-breadcrumb', 'Configuraciones')
@section('section-breadcrumb', 'Categorías')
@section('item-breadcrumb', 'Listado')

@section('content')
<div class="card bg-white border-0 shadow-sm border-5 border-top border-secondary">
  <div class="card-header bg-white">
    <div class="row g-2">
      <div class="col-12 col-md-3">
        <a href="{{ route('categories.create') }}" class="btn btn-primary rounded-3 text-uppercase text-white w-100">
          <i class="bi bi-plus-lg me-2"></i><span class="small fw-medium">Nuevo registro</span>
        </a>
      </div>
      <div class="col-12 col-md-4 ms-md-auto">
        <input type="search" class="form-control" placeholder="Buscar..." data-table-filter="#tabla">
      </div>
    </div>
  </div>
  <div class="card-body">
    <h6 class="text-uppercase small text-muted mb-3">Total de registros encontrados: {{ $categories->count() }}</h6>
    <div class="table-responsive">
      <table id="tabla" class="table table-sm table-hover w-100 bg-white align-middle">
        <thead>
          <tr>
            <th class="text-center text-uppercase small">Nº</th>
            <th class="text-center text-uppercase small">Nombre</th>
            <th class="text-center text-uppercase small">Descripción</th>
            <th class="text-center text-uppercase small">Medicamentos</th>
            <th class="text-center text-uppercase small">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse($categories as $category)
            <tr>
              <td class="text-center">{{ $loop->iteration }}</td>
              <td>{{ $category->nombre }}</td>
              <td>{{ $category->descripcion }}</td>
              <td class="text-center">{{ $category->medicines_count }}</td>
              <td class="text-center">
                @include('partials.row-actions', [
                  'edit' => route('categories.edit', $category),
                  'destroy' => route('categories.destroy', $category),
                ])
              </td>
            </tr>
          @empty
            <tr><td colspan="5" class="text-center text-secondary py-4">No hay categorías registradas.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
