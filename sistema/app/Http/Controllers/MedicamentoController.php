<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Medicamento;
use Illuminate\Http\Request;

class MedicamentoController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q'));
        $categoria = $request->query('category_id');

        $medicamentos = Medicamento::with('category')
            ->when($q !== '', fn ($query) => $query->where('name', 'like', "%{$q}%"))
            ->when($categoria, fn ($query) => $query->where('category_id', $categoria))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $categories = Category::orderBy('nombre')->get();

        return view('medicamentos.index', compact('medicamentos', 'categories', 'q', 'categoria'));
    }

    public function create()
    {
        return view('medicamentos.create', [
            'medicamento' => new Medicamento,
            'categories' => Category::orderBy('nombre')->get(),
        ]);
    }

    public function store(Request $request)
    {
        Medicamento::create($this->validated($request));

        return redirect()->route('medicamentos.index')->with('success', 'Medicamento registrado correctamente.');
    }

    public function edit(Medicamento $medicamento)
    {
        return view('medicamentos.edit', [
            'medicamento' => $medicamento,
            'categories' => Category::orderBy('nombre')->get(),
        ]);
    }

    public function update(Request $request, Medicamento $medicamento)
    {
        $medicamento->update($this->validated($request));

        return redirect()->route('medicamentos.index')->with('success', 'Medicamento actualizado correctamente.');
    }

    public function destroy(Medicamento $medicamento)
    {
        if ($medicamento->details()->exists()) {
            return back()->with('error', 'No se puede eliminar: el medicamento ya fue vendido.');
        }

        $medicamento->delete();

        return redirect()->route('medicamentos.index')->with('success', 'Medicamento eliminado correctamente.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'stock' => ['required', 'integer', 'min:0', 'max:4294967295'],
        ]);
    }
}
