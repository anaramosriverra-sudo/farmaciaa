<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\DetailSale;
use App\Models\Medicine;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaleController extends Controller
{
    public function index()
    {
        $sales = Sale::with(['customer', 'user'])->orderByDesc('date')->orderByDesc('id')->get();

        return view('management.sales.index', compact('sales'));
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        $medicines = Medicine::where('stock', '>', 0)->orderBy('name')->get();

        return view('management.sales.create', compact('customers', 'medicines'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'items' => 'required|array|min:1',
            'items.*.medicine_id' => 'required|distinct|exists:medicines,id',
            'items.*.amount' => 'required|integer|min:1',
        ]);

        $userId = Auth::id() ?? User::orderBy('id')->value('id');

        if (! $userId) {
            return back()->withInput()->with('error', 'No hay usuarios registrados para asignar la venta.');
        }

        $items = $request->input('items');

        $sale = DB::transaction(function () use ($request, $items, $userId) {
            $ids = collect($items)->pluck('medicine_id')->all();
            $medicines = Medicine::whereIn('id', $ids)->lockForUpdate()->get()->keyBy('id');

            $lines = [];
            $total = 0.0;

            foreach ($items as $index => $item) {
                $medicine = $medicines[$item['medicine_id']];
                $amount = (int) $item['amount'];

                if ($medicine->stock < $amount) {
                    throw ValidationException::withMessages([
                        "items.$index.amount" => "Stock insuficiente de {$medicine->name} (disponible: {$medicine->stock}).",
                    ]);
                }

                $price = (float) $medicine->price;
                $subtotal = round($price * $amount, 2);
                $total += $subtotal;

                $lines[] = [
                    'medicine' => $medicine,
                    'amount' => $amount,
                    'price_unit' => $price,
                    'subtotal' => $subtotal,
                ];
            }

            $total = round($total, 2);
            $igv = round($total - ($total / (1 + Sale::IGV_RATE)), 2);

            $sale = Sale::create([
                'customer_id' => $request->input('customer_id'),
                'user_id' => $userId,
                'date' => now(),
                'total' => $total,
                'igv' => $igv,
            ]);

            foreach ($lines as $line) {
                DetailSale::create([
                    'sale_id' => $sale->id,
                    'medicine_id' => $line['medicine']->id,
                    'amount' => $line['amount'],
                    'price_unit' => $line['price_unit'],
                    'subtotal' => $line['subtotal'],
                ]);

                $line['medicine']->decrement('stock', $line['amount']);
            }

            return $sale;
        });

        return to_route('sales.show', $sale)->with('ok', 'Venta registrada correctamente.');
    }

    public function show(Sale $sale)
    {
        $sale->load(['customer', 'user', 'details.medicine']);

        return view('management.sales.show', compact('sale'));
    }

    /** Anula la venta: devuelve el stock y elimina el registro. */
    public function destroy(Sale $sale)
    {
        DB::transaction(function () use ($sale) {
            foreach ($sale->details()->get() as $detail) {
                Medicine::whereKey($detail->medicine_id)->increment('stock', $detail->amount);
            }

            $sale->details()->delete();
            $sale->delete();
        });

        return to_route('sales.index')->with('ok', 'Venta anulada. El stock fue devuelto al inventario.');
    }
}
