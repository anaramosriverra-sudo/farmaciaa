<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Medicine;
use Illuminate\Http\Request;

class MedicineController extends Controller
{
    private function rules(): array
    {
        return [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:120',
            'price' => 'required|numeric|min:0|max:99999999.99',
            'stock' => 'required|integer|min:0|max:4294967295',
        ];
    }

    public function index()
    {
        $medicines = Medicine::with('category')->orderBy('name')->get();

        return view('management.medicines.index', compact('medicines'));
    }

    public function create()
    {
        $categories = Category::orderBy('nombre')->get();

        return view('management.medicines.create', compact('categories'));
    }

    public function store(Request $request)
    {
        Medicine::create($request->validate($this->rules()));

        return to_route('medicines.index')->with('ok', 'Medicamento registrado correctamente.');
    }

    public function edit(Medicine $medicine)
    {
        $categories = Category::orderBy('nombre')->get();

        return view('management.medicines.edit', compact('medicine', 'categories'));
    }

    public function update(Request $request, Medicine $medicine)
    {
        $medicine->update($request->validate($this->rules()));

        return to_route('medicines.index')->with('ok', 'Medicamento actualizado correctamente.');
    }

    public function destroy(Medicine $medicine)
    {
        if ($medicine->details()->exists()) {
            return to_route('medicines.index')
                ->with('error', 'No se puede eliminar: el medicamento ya fue vendido.');
        }

        $medicine->delete();

        return to_route('medicines.index')->with('ok', 'Medicamento eliminado correctamente.');
    }
}
