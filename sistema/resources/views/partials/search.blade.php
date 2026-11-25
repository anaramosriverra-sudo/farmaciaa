{{-- Buscador GET. Uso: @include('partials.search', ['q' => $q, 'placeholder' => '...']) --}}
<form method="GET" class="d-flex gap-2">
  <input type="text" name="q" value="{{ $q ?? '' }}" class="form-control form-control-sm"
         placeholder="{{ $placeholder ?? 'Buscar...' }}">
  <button type="submit" class="btn btn-sm btn-outline-secondary"><i class="bi bi-search"></i></button>
  @if (!empty($q))
    <a href="{{ url()->current() }}" class="btn btn-sm btn-light" title="Limpiar"><i class="bi bi-x-lg"></i></a>
  @endif
</form>
