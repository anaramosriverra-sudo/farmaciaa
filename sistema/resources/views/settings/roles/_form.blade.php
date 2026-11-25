@include('partials.form-actions')

<div class="mb-3">
  <label for="nombre" class="form-label">Nombre del rol</label>
  <input type="text" name="nombre" id="nombre" maxlength="50"
         class="form-control @error('nombre') is-invalid @enderror"
         value="{{ old('nombre', $role->nombre ?? '') }}">
  @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
  <label for="descripcion" class="form-label">Descripción</label>
  <textarea name="descripcion" id="descripcion" rows="3" maxlength="255"
            class="form-control @error('descripcion') is-invalid @enderror">{{ old('descripcion', $role->descripcion ?? '') }}</textarea>
  @error('descripcion')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<button type="submit" class="btn btn-primary text-white">Guardar</button>
<a href="{{ route('roles.index') }}" class="btn btn-secondary">Cancelar</a>
