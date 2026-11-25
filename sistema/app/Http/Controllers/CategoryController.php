<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('medicines')->orderBy('nombre')->get();

        return view('settings.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('settings.categories.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:50|unique:categories,nombre',
            'descripcion' => 'nullable|string|max:255',
        ]);

        Category::create($data);

        return to_route('categories.index')->with('ok', 'Categoría registrada correctamente.');
    }

    public function edit(Category $category)
    {
        return view('settings.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:50',
                Rule::unique('categories', 'nombre')->ignore($category->id),
            ],
            'descripcion' => 'nullable|string|max:255',
        ]);

        $category->update($data);

        return to_route('categories.index')->with('ok', 'Categoría actualizada correctamente.');
    }

    public function destroy(Category $category)
    {
        if ($category->medicines()->exists()) {
            return to_route('categories.index')
                ->with('error', 'No se puede eliminar: la categoría tiene medicamentos asociados.');
        }

        $category->delete();

        return to_route('categories.index')->with('ok', 'Categoría eliminada correctamente.');
    }
}
