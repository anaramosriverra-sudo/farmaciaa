<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('role')->orderBy('name')->get();

        return view('settings.users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::orderBy('nombre')->get();

        return view('settings.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'role_id' => 'nullable|exists:roles,id',
        ]);

        User::create($data);

        return to_route('users.index')->with('ok', 'Usuario registrado correctamente.');
    }

    public function edit(User $user)
    {
        $roles = Role::orderBy('nombre')->get();

        return view('settings.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'password' => 'nullable|string|min:6',
            'role_id' => 'nullable|exists:roles,id',
        ]);

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);

        return to_route('users.index')->with('ok', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $user)
    {
        if (\App\Models\Sale::where('user_id', $user->id)->exists()) {
            return to_route('users.index')
                ->with('error', 'No se puede eliminar: el usuario tiene ventas registradas.');
        }

        $user->delete();

        return to_route('users.index')->with('ok', 'Usuario eliminado correctamente.');
    }
}
