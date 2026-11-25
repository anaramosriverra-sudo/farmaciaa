<div class="card bg-white border-0 shadow-sm border-5 border-top border-secondary">
  <div class="card-body">
    <div class="row g-3">
      <div class="col-12 col-md-6">
        <label for="name" class="form-label">Nombre completo</label>
        <input type="text" name="name" id="name" maxlength="100" class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name', $customer->name) }}" required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
      </div>
      <div class="col-12 col-md-6">
        <label for="dni" class="form-label">DNI</label>
        <input type="text" name="dni" id="dni" maxlength="8" inputmode="numeric" class="form-control @error('dni') is-invalid @enderror"
               value="{{ old('dni', $customer->dni) }}" required>
        @error('dni') <div class="invalid-feedback">{{ $message }}</div> @enderror
      </div>
      <div class="col-12 col-md-6">
        <label for="phone" class="form-label">Teléfono</label>
        <input type="text" name="phone" id="phone" maxlength="9" inputmode="numeric" class="form-control @error('phone') is-invalid @enderror"
               value="{{ old('phone', $customer->phone) }}">
        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
      </div>
      <div class="col-12 col-md-6">
        <label for="age" class="form-label">Edad</label>
        <input type="number" name="age" id="age" min="0" max="120" class="form-control @error('age') is-invalid @enderror"
               value="{{ old('age', $customer->age) }}">
        @error('age') <div class="invalid-feedback">{{ $message }}</div> @enderror
      </div>
    </div>

    <div class="mt-4">
      <button type="submit" class="btn btn-primary text-white">Guardar</button>
      <a href="{{ route('customers.index') }}" class="btn btn-secondary">Cancelar</a>
    </div>
  </div>
</div>
