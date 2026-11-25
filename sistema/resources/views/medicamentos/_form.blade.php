<div class="card bg-white border-0 shadow-sm border-5 border-top border-secondary">
  <div class="card-body">
    <div class="row g-3">
      <div class="col-12 col-md-8">
        <label for="name" class="form-label">Nombre del medicamento</label>
        <input type="text" name="name" id="name" maxlength="120" class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name', $medicamento->name) }}" required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
      </div>
      <div class="col-12 col-md-4">
        <label for="category_id" class="form-label">Categoría</label>
        <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
          <option value="">Seleccione una categoría</option>
          @foreach ($categories as $category)
            <option value="{{ $category->id }}" @selected(old('category_id', $medicamento->category_id) == $category->id)>{{ $category->nombre }}</option>
          @endforeach
        </select>
        @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
        @if ($categories->isEmpty())
          <div class="form-text text-danger">Primero registra una <a href="{{ route('categories.create') }}">categoría</a>.</div>
        @endif
      </div>
      <div class="col-12">
        <label for="description" class="form-label">Descripción</label>
        <input type="text" name="description" id="description" maxlength="255" class="form-control @error('description') is-invalid @enderror"
               value="{{ old('description', $medicamento->description) }}">
        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
      </div>
      <div class="col-12 col-md-6">
        <label for="price" class="form-label">Precio (S/)</label>
        <input type="number" name="price" id="price" step="0.01" min="0" class="form-control @error('price') is-invalid @enderror"
               value="{{ old('price', $medicamento->price) }}" required>
        @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
      </div>
      <div class="col-12 col-md-6">
        <label for="stock" class="form-label">Stock</label>
        <input type="number" name="stock" id="stock" min="0" class="form-control @error('stock') is-invalid @enderror"
               value="{{ old('stock', $medicamento->stock ?? 0) }}" required>
        @error('stock') <div class="invalid-feedback">{{ $message }}</div> @enderror
      </div>
    </div>

    <div class="mt-4">
      <button type="submit" class="btn btn-primary text-white">Guardar</button>
      <a href="{{ route('medicamentos.index') }}" class="btn btn-secondary">Cancelar</a>
    </div>
  </div>
</div>
