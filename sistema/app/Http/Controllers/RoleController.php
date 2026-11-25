<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::withCount('users')->orderBy('nombre')->get();

        return view('settings.roles.index', compact('roles'));
    }

    public function create()
    {
        return view('settings.roles.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:50|unique:roles,nombre',
            'descripcion' => 'nullable|string|max:255',
        ]);

        Role::create($data);

        return to_route('roles.index')->with('ok', 'Rol registrado correctamente.');
    }

    public function edit(Role $role)
    {
        return view('settings.roles.edit', compact('role'));
    }

    public function update(Request $request, Role $role)
    {
        $data = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:50',
                Rule::unique('roles', 'nombre')->ignore($role->id),
            ],
            'descripcion' => 'nullable|string|max:255',
        ]);

        $role->update($data);

        return to_route('roles.index')->with('ok', 'Rol actualizado correctamente.');
    }

    public function destroy(Role $role)
    {
        if ($role->users()->exists()) {
            return to_route('roles.index')
                ->with('error', 'No se puede eliminar: hay usuarios asignados a este rol.');
        }

        $role->delete();

        return to_route('roles.index')->with('ok', 'Rol eliminado correctamente.');
    }
}
