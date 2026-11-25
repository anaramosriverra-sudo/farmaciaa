@include('partials.form-actions')

<div class="row">
  <div class="col-md-6 mb-3">
    <label for="name" class="form-label">Nombre completo</label>
    <input type="text" name="name" id="name" maxlength="255"
           class="form-control @error('name') is-invalid @enderror"
           value="{{ old('name', $user->name ?? '') }}">
    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>

  <div class="col-md-6 mb-3">
    <label for="email" class="form-label">Correo electrónico</label>
    <input type="email" name="email" id="email" maxlength="255"
           class="form-control @error('email') is-invalid @enderror"
           value="{{ old('email', $user->email ?? '') }}">
    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>

  <div class="col-md-6 mb-3">
    <label for="password" class="form-label">
      Contraseña @isset($user)<span class="text-secondary small">(déjala vacía para no cambiarla)</span>@endisset
    </label>
    <input type="password" name="password" id="password" autocomplete="new-password"
           class="form-control @error('password') is-invalid @enderror">
    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>

  <div class="col-md-6 mb-3">
    <label for="role_id" class="form-label">Rol</label>
    <select name="role_id" id="role_id" class="form-select @error('role_id') is-invalid @enderror">
      <option value="">— Sin rol —</option>
      @foreach($roles as $role)
        <option value="{{ $role->id }}" @selected((string) old('role_id', $user->role_id ?? '') === (string) $role->id)>
          {{ $role->nombre }}
        </option>
      @endforeach
    </select>
    @error('role_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
</div>

<button type="submit" class="btn btn-primary text-white">Guardar</button>
<a href="{{ route('users.index') }}" class="btn btn-secondary">Cancelar</a>
