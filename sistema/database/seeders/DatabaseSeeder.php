<?php

namespace Database\Seeders;

<<<<<<< Updated upstream
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
=======
use App\Models\Category;
use App\Models\Customer;
use App\Models\Medicamento;
use App\Models\Role;
use App\Models\Sale;
use App\Models\User;
>>>>>>> Stashed changes
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
<<<<<<< Updated upstream
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
=======
    public function run(): void
    {
        // Roles
        $admin = Role::firstOrCreate(['name' => 'Administrador'], ['description' => 'Acceso total al sistema']);
        $vendedor = Role::firstOrCreate(['name' => 'Vendedor'], ['description' => 'Registra ventas y consulta inventario']);

        // Usuarios (contraseña de ambos: password)
        User::updateOrCreate(['email' => 'admin@sanjuan.com'], [
            'role_id' => $admin->id,
            'name' => 'Administrador',
            'dni' => '70000001',
            'phone' => '987654321',
            'password' => 'password',
        ]);
        $user = User::updateOrCreate(['email' => 'vendedor@sanjuan.com'], [
            'role_id' => $vendedor->id,
            'name' => 'Vendedor Demo',
            'dni' => '70000002',
            'phone' => '987654322',
            'password' => 'password',
        ]);

        // Categorías
        $cats = collect([
            'Analgésicos' => 'Alivio del dolor y la fiebre',
            'Antibióticos' => 'Tratamiento de infecciones bacterianas',
            'Antiinflamatorios' => 'Reducen la inflamación',
            'Vitaminas' => 'Suplementos vitamínicos y minerales',
            'Antialérgicos' => 'Tratamiento de alergias',
        ])->map(fn ($d, $n) => Category::firstOrCreate(['nombre' => $n], ['descripcion' => $d]));

        // Medicamentos
        $meds = [
            ['Paracetamol 500 mg', 'Analgésicos', 0.50, 200],
            ['Ibuprofeno 400 mg', 'Antiinflamatorios', 1.20, 8],
            ['Amoxicilina 500 mg', 'Antibióticos', 1.80, 5],
            ['Naproxeno 550 mg', 'Antiinflamatorios', 1.50, 60],
            ['Vitamina C 1 g', 'Vitaminas', 2.00, 120],
            ['Loratadina 10 mg', 'Antialérgicos', 0.90, 90],
            ['Azitromicina 500 mg', 'Antibióticos', 6.50, 40],
            ['Diclofenaco 50 mg', 'Antiinflamatorios', 0.80, 150],
        ];
        foreach ($meds as [$name, $cat, $price, $stock]) {
            Medicamento::firstOrCreate(['name' => $name], [
                'category_id' => $cats[$cat]->id,
                'price' => $price,
                'stock' => $stock,
            ]);
        }

        // Clientes
        foreach ([
            ['Juan Pérez Quispe', '45678912', '987111222', 34],
            ['María Torres Huamán', '41236598', '987333444', 28],
            ['Luis Ramos Condori', '47891234', null, 52],
        ] as [$name, $dni, $phone, $age]) {
            Customer::firstOrCreate(['dni' => $dni], compact('name', 'phone', 'age'));
        }

        // Ventas de ejemplo (solo si no hay ninguna)
        if (Sale::count() === 0) {
            $clientes = Customer::all();
            $catalogo = Medicamento::where('stock', '>', 20)->get();

            foreach (range(0, 5) as $i) {
                $items = $catalogo->random(2)->map(fn ($m) => ['m' => $m, 'q' => rand(1, 4)]);
                $total = round($items->sum(fn ($r) => $r['m']->price * $r['q']), 2);

                $sale = Sale::create([
                    'user_id' => $user->id,
                    'customer_id' => $clientes->random()->id,
                    'date' => now()->subDays($i)->setTime(rand(9, 18), rand(0, 59)),
                    'total' => $total,
                    'igv' => round($total - $total / 1.18, 2),
                ]);

                foreach ($items as $r) {
                    $sale->details()->create([
                        'medicine_id' => $r['m']->id,
                        'amount' => $r['q'],
                        'unit_price' => $r['m']->price,
                        'subtotal' => round($r['m']->price * $r['q'], 2),
                    ]);
                    $r['m']->decrement('stock', $r['q']);
                }
            }
        }
>>>>>>> Stashed changes
    }
}
