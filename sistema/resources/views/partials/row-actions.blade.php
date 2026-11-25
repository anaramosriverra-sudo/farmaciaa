<div class="d-inline-flex gap-1">
  @isset($show)
    <a href="{{ $show }}" class="btn btn-sm btn-outline-secondary" title="Ver detalle"><i class="bi bi-eye"></i></a>
  @endisset
  @isset($edit)
    <a href="{{ $edit }}" class="btn btn-sm btn-outline-primary" title="Editar"><i class="bi bi-pencil"></i></a>
  @endisset
  <form action="{{ $destroy }}" method="POST" class="d-inline"
        onsubmit="return confirm('{{ $confirm ?? '¿Estás seguro de eliminar este registro?' }}');">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ $label ?? 'Eliminar' }}"><i class="bi bi-trash"></i></button>
  </form>
</div>
