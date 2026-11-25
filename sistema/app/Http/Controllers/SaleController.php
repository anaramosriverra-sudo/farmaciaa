<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Medicamento;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaleController extends Controller
{
    public const IGV = 0.18;

    public function index(Request $request)
    {
        $q = trim((string) $request->query('q'));
        $desde = $request->query('desde');
        $hasta = $request->query('hasta');

        $sales = Sale::with(['user', 'customer'])
            ->withCount('details')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->whereHas('customer', fn ($c) => $c->where('name', 'like', "%{$q}%")->orWhere('dni', 'like', "%{$q}%"))
                        ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$q}%"));
                    if (ctype_digit(ltrim($q, 'Vv-0')) && ltrim($q, 'Vv-0') !== '') {
                        $w->orWhere('id', (int) ltrim($q, 'Vv-0'));
                    }
                });
            })
            ->when($desde, fn ($query) => $query->whereDate('date', '>=', $desde))
            ->when($hasta, fn ($query) => $query->whereDate('date', '<=', $hasta))
            ->orderByDesc('date')->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('sales.index', compact('sales', 'q', 'desde', 'hasta'));
    }

    public function create()
    {
        $medicamentos = Medicamento::with('category')
            ->where('stock', '>', 0)
            ->orderBy('name')
            ->get()
            ->map(fn ($m) => [
                'id' => $m->id,
                'name' => $m->name,
                'category' => $m->category?->nombre,
                'price' => (float) $m->price,
                'stock' => $m->stock,
            ]);

        return view('sales.create', [
            'medicamentos' => $medicamentos,
            'customers' => Customer::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => ['nullable', 'exists:customers,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.medicine_id' => ['required', 'exists:medicamentos,id'],
            'items.*.amount' => ['required', 'integer', 'min:1', 'max:10000'],
        ], [
            'items.required' => 'Debes agregar al menos un medicamento a la venta.',
            'items.min' => 'Debes agregar al menos un medicamento a la venta.',
        ]);

        // Unificar medicamentos repetidos
        $items = collect($request->input('items'))
            ->groupBy('medicine_id')
            ->map(fn ($rows) => $rows->sum('amount'));

        $sale = DB::transaction(function () use ($request, $items) {
            $medicamentos = Medicamento::whereIn('id', $items->keys())->lockForUpdate()->get()->keyBy('id');

            $total = 0;
            $lines = [];

            foreach ($items as $id => $amount) {
                $med = $medicamentos[$id];

                if ($med->stock < $amount) {
                    throw ValidationException::withMessages([
                        'items' => "Stock insuficiente de {$med->name}: disponible {$med->stock}, solicitado {$amount}.",
                    ]);
                }

                $subtotal = round($med->price * $amount, 2);
                $total += $subtotal;
                $lines[] = [
                    'medicine_id' => $id,
                    'amount' => $amount,
                    'unit_price' => $med->price,
                    'subtotal' => $subtotal,
                ];
                $med->decrement('stock', $amount);
            }

            $total = round($total, 2);
            $igv = round($total - ($total / (1 + self::IGV)), 2);

            $sale = Sale::create([
                'user_id' => $request->user()->id,
                'customer_id' => $request->input('customer_id'),
                'date' => now(),
                'total' => $total,
                'igv' => $igv,
            ]);

            $sale->details()->createMany($lines);

            return $sale;
        });

        return redirect()->route('sales.show', $sale)->with('success', 'Venta registrada correctamente.');
    }

    public function show(Sale $sale)
    {
        $sale->load(['user', 'customer', 'details.medicamento']);

        return view('sales.show', compact('sale'));
    }

    /** Anular venta: devuelve el stock y elimina el registro. */
    public function destroy(Request $request, Sale $sale)
    {
        abort_unless($request->user()->isAdmin(), 403, 'Solo el Administrador puede anular ventas.');

        DB::transaction(function () use ($sale) {
            foreach ($sale->details as $detail) {
                Medicamento::whereKey($detail->medicine_id)->increment('stock', $detail->amount);
            }
            $sale->delete(); // los detalles se eliminan en cascada
        });

        return redirect()->route('sales.index')->with('success', 'Venta anulada. El stock fue devuelto al inventario.');
    }
}
