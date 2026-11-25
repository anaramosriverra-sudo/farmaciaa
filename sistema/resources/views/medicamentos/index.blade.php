@extends('template.administrador')

@section('title', 'Medicamentos')
@section('name-section', 'MEDICAMENTOS')
@section('descripcion', 'Inventario de medicamentos')
@section('module-breadcrumb', 'Gestión')
@section('section-breadcrumb', 'Medicamentos')

@section('content')
<div class="card bg-white border-0 shadow-sm border-5 border-top border-secondary">
  <div class="card-header bg-white">
    <div class="row g-2 justify-content-between">
      <div class="col-12 col-md-3">
        <a href="{{ route('medicamentos.create') }}" class="btn btn-primary rounded-3 text-uppercase text-white w-100">
          <i class="bi bi-plus-lg me-2"></i><span class="small fw-medium">Nuevo registro</span>
        </a>
      </div>
      <div class="col-12 col-md-7">
        <form method="GET" class="d-flex gap-2">
          <select name="category_id" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
            <option value="">Todas las categorías</option>
            @foreach ($categories as $c)
              <option value="{{ $c->id }}" @selected($categoria == $c->id)>{{ $c->nombre }}</option>
            @endforeach
          </select>
          <input type="text" name="q" value="{{ $q }}" class="form-control form-control-sm" placeholder="Buscar medicamento...">
          <button type="submit" class="btn btn-sm btn-outline-secondary"><i class="bi bi-search"></i></button>
          @if ($q !== '' || $categoria)
            <a href="{{ route('medicamentos.index') }}" class="btn btn-sm btn-light" title="Limpiar"><i class="bi bi-x-lg"></i></a>
          @endif
        </form>
      </div>
    </div>
  </div>
  <div class="card-body">
    <h6 class="text-uppercase small text-muted mb-3">Total de registros encontrados: {{ $medicamentos->total() }}</h6>
    <div class="table-responsive">
      <table class="table table-sm table-hover w-100 bg-white">
        <thead>
          <tr>
            <th class="text-center align-middle text-uppercase small">Nº</th>
            <th class="text-center align-middle text-uppercase small">Nombre</th>
            <th class="text-center align-middle text-uppercase small">Categoría</th>
            <th class="text-center align-middle text-uppercase small">Precio</th>
            <th class="text-center align-middle text-uppercase small">Stock</th>
            <th class="text-center align-middle text-uppercase small">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($medicamentos as $m)
            <tr>
              <td class="text-center align-middle">{{ $medicamentos->firstItem() + $loop->index }}</td>
              <td class="text-start align-middle">{{ $m->name }}
                @if ($m->description)<div class="text-secondary" style="font-size:.72rem">{{ $m->description }}</div>@endif
              </td>
              <td class="text-center align-middle">{{ $m->category?->nombre }}</td>
              <td class="text-end align-middle">S/ {{ number_format($m->price, 2) }}</td>
              <td class="text-center align-middle">
                <span class="badge {{ $m->stock === 0 ? 'bg-danger' : ($m->stock <= \App\Models\Medicamento::STOCK_BAJO ? 'bg-warning text-dark' : 'bg-success-subtle text-success') }}">{{ $m->stock }}</span>
              </td>
              <td class="text-center align-middle">
                <div class="dropdown">
                  <button class="btn btn-light" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-three-dots-vertical"></i>
                  </button>
                  <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ route('medicamentos.edit', $m) }}">Editar</a></li>
                    <li>
                      <form action="{{ route('medicamentos.destroy', $m) }}" method="POST"
                            class="js-confirm-delete" data-message="¿Eliminar el medicamento «{{ $m->name }}»?">
                        @csrf @method('DELETE')
                        <button type="submit" class="dropdown-item text-danger">Eliminar</button>
                      </form>
                    </li>
                  </ul>
                </div>
              </td>
            </tr>
          @empty
            <tr><td colspan="6" class="text-center text-secondary py-4">No se encontraron medicamentos.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    {{ $medicamentos->links() }}
  </div>
</div>
@endsection
