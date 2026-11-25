@extends('template.administrador')

@section('title', 'Usuarios')
@section('name-section', 'USUARIOS')
@section('descripcion', 'Listado de usuarios del sistema')
@section('module-breadcrumb', 'Configuraciones')
@section('section-breadcrumb', 'Usuarios')

@section('content')
<div class="card bg-white border-0 shadow-sm border-5 border-top border-secondary">
  <div class="card-header bg-white">
    <div class="row g-2 justify-content-between">
      <div class="col-12 col-md-3">
        <a href="{{ route('users.create') }}" class="btn btn-primary rounded-3 text-uppercase text-white w-100">
          <i class="bi bi-plus-lg me-2"></i><span class="small fw-medium">Nuevo registro</span>
        </a>
      </div>
      <div class="col-12 col-md-5">@include('partials.search', ['q' => $q, 'placeholder' => 'Buscar por nombre, correo o DNI...'])</div>
    </div>
  </div>
  <div class="card-body">
    <h6 class="text-uppercase small text-muted mb-3">Total de registros encontrados: {{ $users->total() }}</h6>
    <div class="table-responsive">
      <table class="table table-sm table-hover w-100 bg-white">
        <thead>
          <tr>
            <th class="text-center align-middle text-uppercase small">Nº</th>
            <th class="text-center align-middle text-uppercase small">Nombre</th>
            <th class="text-center align-middle text-uppercase small">DNI</th>
            <th class="text-center align-middle text-uppercase small">Correo</th>
            <th class="text-center align-middle text-uppercase small">Teléfono</th>
            <th class="text-center align-middle text-uppercase small">Rol</th>
            <th class="text-center align-middle text-uppercase small">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($users as $user)
            <tr>
              <td class="text-center align-middle">{{ $users->firstItem() + $loop->index }}</td>
              <td class="text-start align-middle">{{ $user->name }}</td>
              <td class="text-center align-middle">{{ $user->dni }}</td>
              <td class="text-start align-middle">{{ $user->email }}</td>
              <td class="text-center align-middle">{{ $user->phone ?? '—' }}</td>
              <td class="text-center align-middle"><span class="badge bg-primary-subtle text-primary">{{ $user->role?->name }}</span></td>
              <td class="text-center align-middle">
                <div class="dropdown">
                  <button class="btn btn-light" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-three-dots-vertical"></i>
                  </button>
                  <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ route('users.edit', $user) }}">Editar</a></li>
                    @if (! $user->is(auth()->user()))
                      <li>
                        <form action="{{ route('users.destroy', $user) }}" method="POST"
                              class="js-confirm-delete" data-message="¿Eliminar al usuario «{{ $user->name }}»?">
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
            <tr><td colspan="7" class="text-center text-secondary py-4">No se encontraron usuarios.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    {{ $users->links() }}
  </div>
</div>
@endsection
