@include('partials.form-actions')

<div class="row">
  <div class="col-md-6 mb-3">
    <label for="name" class="form-label">Nombre del medicamento</label>
    <input type="text" name="name" id="name" maxlength="120"
           class="form-control @error('name') is-invalid @enderror"
           value="{{ old('name', $medicine->name ?? '') }}">
    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>

  <div class="col-md-6 mb-3">
    <label for="category_id" class="form-label">Categoría</label>
    <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror">
      <option value="">— Seleccione —</option>
      @foreach($categories as $category)
        <option value="{{ $category->id }}" @selected((string) old('category_id', $medicine->category_id ?? '') === (string) $category->id)>
          {{ $category->nombre }}
        </option>
      @endforeach
    </select>
    @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>

  <div class="col-md-6 mb-3">
    <label for="price" class="form-label">Precio (S/, incluye IGV)</label>
    <input type="number" name="price" id="price" step="0.01" min="0"
           class="form-control @error('price') is-invalid @enderror"
           value="{{ old('price', isset($medicine) ? number_format($medicine->price, 2, '.', '') : '') }}">
    @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>

  <div class="col-md-6 mb-3">
    <label for="stock" class="form-label">Stock (unidades)</label>
    <input type="number" name="stock" id="stock" step="1" min="0"
           class="form-control @error('stock') is-invalid @enderror"
           value="{{ old('stock', $medicine->stock ?? 0) }}">
    @error('stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
</div>

<button type="submit" class="btn btn-primary text-white">Guardar</button>
<a href="{{ route('medicines.index') }}" class="btn btn-secondary">Cancelar</a>
