@extends('template.administrador')

@section('title', 'Roles')
@section('name-section', 'ROLES')
@section('descripcion', 'Listado de roles')
@section('module-breadcrumb', 'Configuraciones')
@section('section-breadcrumb', 'Roles')

@section('content')
<div class="card bg-white border-0 shadow-sm border-5 border-top border-secondary">
  <div class="card-header bg-white">
    <div class="row g-2 justify-content-between">
      <div class="col-12 col-md-3">
        <a href="{{ route('roles.create') }}" class="btn btn-primary rounded-3 text-uppercase text-white w-100">
          <i class="bi bi-plus-lg me-2"></i><span class="small fw-medium">Nuevo registro</span>
        </a>
      </div>
      <div class="col-12 col-md-5">@include('partials.search', ['q' => $q, 'placeholder' => 'Buscar rol...'])</div>
    </div>
  </div>
  <div class="card-body">
    <h6 class="text-uppercase small text-muted mb-3">Total de registros encontrados: {{ $roles->total() }}</h6>
    <div class="table-responsive">
      <table class="table table-sm table-hover w-100 bg-white">
        <thead>
          <tr>
            <th class="text-center align-middle text-uppercase small">Nº</th>
            <th class="text-center align-middle text-uppercase small">Nombre</th>
            <th class="text-center align-middle text-uppercase small">Descripción</th>
            <th class="text-center align-middle text-uppercase small">Usuarios</th>
            <th class="text-center align-middle text-uppercase small">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($roles as $role)
            <tr>
              <td class="text-center align-middle">{{ $roles->firstItem() + $loop->index }}</td>
              <td class="text-start align-middle">{{ $role->name }}</td>
              <td class="text-start align-middle">{{ $role->description ?? '—' }}</td>
              <td class="text-center align-middle">{{ $role->users_count }}</td>
              <td class="text-center align-middle">
                <div class="dropdown">
                  <button class="btn btn-light" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-three-dots-vertical"></i>
                  </button>
                  <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ route('roles.edit', $role) }}">Editar</a></li>
                    @if ($role->name !== 'Administrador')
                      <li>
                        <form action="{{ route('roles.destroy', $role) }}" method="POST"
                              class="js-confirm-delete" data-message="¿Eliminar el rol «{{ $role->name }}»?">
                          @csrf @method('DELETE')
                          <button type="submit" class="dropdown-item text-danger">Eliminar</button>
                        </form>
                      </li>
                    @endif
                  </ul>
                </div>
              </td>
            </tr>
          @empty
            <tr><td colspan="5" class="text-center text-secondary py-4">No se encontraron roles.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    {{ $roles->links() }}
  </div>
</div>
@endsection
