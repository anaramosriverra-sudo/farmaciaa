<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
<<<<<<< Updated upstream
class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::all();
        return view('settings.categories.index', compact('categories'));                                  
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('settings.categories.create'); 
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
          'nombre' => 'required|max:50|unique:categories',
         'descripcion' => 'nullable|max:255',   
        ]);

        //Category::create($validated);
        $category = new Category();
        $category->nombre = $request->input('nombre');
        $category->descripcion = $request->input('descripcion');
        $category->save();
        return to_route('categories.index')->with('ok','Categoria regstrada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        return view('settings.categories.index', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
        'nombre' => [
            'required',
            'max:50',
            Rule::unique('categories','name')->ignote($category->id),
        ],
        'descripcion'=>'nullable|max:255'
        ]);

        $category->update($validated);

        return to_route('categories.index')->with('ok','Categoria actualizada correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $category->delete();
        return to_route('categories.index')->with('ok','Categoria eliminada correctamente');
    }
}
=======

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q'));

        $categories = Category::withCount('medicamentos')
            ->when($q !== '', fn ($query) => $query->where(function ($w) use ($q) {
                $w->where('nombre', 'like', "%{$q}%")->orWhere('descripcion', 'like', "%{$q}%");
            }))
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('settings.categories.index', compact('categories', 'q'));
    }

    public function create()
    {
        return view('settings.categories.create', ['category' => new Category]);
    }

    public function store(Request $request)
    {
        Category::create($this->validated($request));

        return redirect()->route('categories.index')->with('success', 'Categoría registrada correctamente.');
    }

    public function edit(Category $category)
    {
        return view('settings.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $category->update($this->validated($request, $category));

        return redirect()->route('categories.index')->with('success', 'Categoría actualizada correctamente.');
    }

    public function destroy(Category $category)
    {
        if ($category->medicamentos()->exists()) {
            return back()->with('error', 'No se puede eliminar: la categoría tiene medicamentos asociados.');
        }

        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Categoría eliminada correctamente.');
    }

    private function validated(Request $request, ?Category $category = null): array
    {
        return $request->validate([
            'nombre' => ['required', 'string', 'max:50', Rule::unique('categories', 'nombre')->ignore($category)],
            'descripcion' => ['nullable', 'string', 'max:255'],
        ]);
    }
}
>>>>>>> Stashed changes
