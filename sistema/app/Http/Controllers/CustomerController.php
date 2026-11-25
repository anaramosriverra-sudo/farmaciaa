<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::withCount('sales')->orderBy('name')->get();

        return view('management.customers.index', compact('customers'));
    }

    public function create()
    {
        return view('management.customers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:50',
            'dni' => 'required|digits:8|unique:customers,dni',
            'phone' => 'nullable|digits:9',
            'age' => 'nullable|integer|min:0|max:120',
        ]);

        Customer::create($data);

        return to_route('customers.index')->with('ok', 'Cliente registrado correctamente.');
    }

    public function edit(Customer $customer)
    {
        return view('management.customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $data = $request->validate([
            'name' => 'required|string|max:50',
            'dni' => [
                'required',
                'digits:8',
                Rule::unique('customers', 'dni')->ignore($customer->id),
            ],
            'phone' => 'nullable|digits:9',
            'age' => 'nullable|integer|min:0|max:120',
        ]);

        $customer->update($data);

        return to_route('customers.index')->with('ok', 'Cliente actualizado correctamente.');
    }

    public function destroy(Customer $customer)
    {
        if ($customer->sales()->exists()) {
            return to_route('customers.index')
                ->with('error', 'No se puede eliminar: el cliente tiene ventas registradas.');
        }

        $customer->delete();

        return to_route('customers.index')->with('ok', 'Cliente eliminado correctamente.');
    }
}
