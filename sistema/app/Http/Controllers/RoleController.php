<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q'));

        $roles = Role::withCount('users')
            ->when($q !== '', fn ($query) => $query->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")->orWhere('description', 'like', "%{$q}%");
            }))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('settings.roles.index', compact('roles', 'q'));
    }

    public function create()
    {
        return view('settings.roles.create', ['role' => new Role]);
    }

    public function store(Request $request)
    {
        Role::create($this->validated($request));

        return redirect()->route('roles.index')->with('success', 'Rol registrado correctamente.');
    }

    public function edit(Role $role)
    {
        return view('settings.roles.edit', compact('role'));
    }

    public function update(Request $request, Role $role)
    {
        $data = $this->validated($request, $role);

        // El rol Administrador no puede renombrarse: el sistema lo usa para los permisos.
        if ($role->name === 'Administrador') {
            unset($data['name']);
        }

        $role->update($data);

        return redirect()->route('roles.index')->with('success', 'Rol actualizado correctamente.');
    }

    public function destroy(Role $role)
    {
        if ($role->name === 'Administrador') {
            return back()->with('error', 'El rol Administrador no se puede eliminar.');
        }

        if ($role->users()->exists()) {
            return back()->with('error', 'No se puede eliminar: el rol tiene usuarios asignados.');
        }

        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Rol eliminado correctamente.');
    }

    private function validated(Request $request, ?Role $role = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:50', Rule::unique('roles', 'name')->ignore($role)],
            'description' => ['nullable', 'string', 'max:255'],
        ]);
    }
}
