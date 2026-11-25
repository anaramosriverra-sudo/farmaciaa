@extends('template.administrador')

@section('title', 'CLIENTES')
@section('name-section', 'CLIENTES')
@section('descripcion', 'Listado de clientes')
@section('module-breadcrumb', 'Gestión')
@section('section-breadcrumb', 'Clientes')
@section('item-breadcrumb', 'Listado')

@section('content')
<div class="card bg-white border-0 shadow-sm border-5 border-top border-secondary">
  <div class="card-header bg-white">
    <div class="row g-2">
      <div class="col-12 col-md-3">
        <a href="{{ route('customers.create') }}" class="btn btn-primary rounded-3 text-uppercase text-white w-100">
          <i class="bi bi-plus-lg me-2"></i><span class="small fw-medium">Nuevo registro</span>
        </a>
      </div>
      <div class="col-12 col-md-4 ms-md-auto">
        <input type="search" class="form-control" placeholder="Buscar por nombre o DNI..." data-table-filter="#tabla">
      </div>
    </div>
  </div>
  <div class="card-body">
    <h6 class="text-uppercase small text-muted mb-3">Total de registros encontrados: {{ $customers->count() }}</h6>
    <div class="table-responsive">
      <table id="tabla" class="table table-sm table-hover w-100 bg-white align-middle">
        <thead>
          <tr>
            <th class="text-center text-uppercase small">Nº</th>
            <th class="text-center text-uppercase small">Nombre</th>
            <th class="text-center text-uppercase small">DNI</th>
            <th class="text-center text-uppercase small">Teléfono</th>
            <th class="text-center text-uppercase small">Edad</th>
            <th class="text-center text-uppercase small">Compras</th>
            <th class="text-center text-uppercase small">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse($customers as $customer)
            <tr>
              <td class="text-center">{{ $loop->iteration }}</td>
              <td>{{ $customer->name }}</td>
              <td class="text-center">{{ $customer->dni }}</td>
              <td class="text-center">{{ $customer->phone ?? '—' }}</td>
              <td class="text-center">{{ $customer->age ?? '—' }}</td>
              <td class="text-center">{{ $customer->sales_count }}</td>
              <td class="text-center">
                @include('partials.row-actions', [
                  'edit' => route('customers.edit', $customer),
                  'destroy' => route('customers.destroy', $customer),
                ])
              </td>
            </tr>
          @empty
            <tr><td colspan="7" class="text-center text-secondary py-4">No hay clientes registrados.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
