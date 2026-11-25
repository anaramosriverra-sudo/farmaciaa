<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Customer;
use App\Models\DetailSale;
use App\Models\Medicine;
use App\Models\Role;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Roles
        $admin = Role::firstOrCreate(['nombre' => 'Administrador'], ['descripcion' => 'Acceso total al sistema']);
        Role::firstOrCreate(['nombre' => 'Farmacéutico'], ['descripcion' => 'Gestión de inventario y ventas']);
        Role::firstOrCreate(['nombre' => 'Vendedor'], ['descripcion' => 'Registro de ventas']);

        // Usuario administrador (contraseña: admin123)
        $user = User::firstOrCreate(
            ['email' => 'admin@sanjuan.com'],
            ['name' => 'Administrador', 'password' => 'admin123', 'role_id' => $admin->id]
        );

        // Categorías
        $categories = [];
        foreach ([
            'Analgésicos' => 'Alivio del dolor y la fiebre',
            'Antibióticos' => 'Tratamiento de infecciones bacterianas',
            'Antiinflamatorios' => 'Reducción de inflamación',
            'Antialérgicos' => 'Tratamiento de alergias',
            'Vitaminas' => 'Suplementos y vitaminas',
        ] as $nombre => $descripcion) {
            $categories[$nombre] = Category::firstOrCreate(['nombre' => $nombre], ['descripcion' => $descripcion]);
        }

        // Medicamentos
        $medicines = [];
        foreach ([
            ['Paracetamol 500mg', 'Analgésicos', 0.50, 200],
            ['Panadol Forte', 'Analgésicos', 1.20, 120],
            ['Amoxicilina 500mg', 'Antibióticos', 1.50, 5],
            ['Azitromicina 500mg', 'Antibióticos', 6.00, 40],
            ['Ibuprofeno 400mg', 'Antiinflamatorios', 0.80, 8],
            ['Naproxeno 550mg', 'Antiinflamatorios', 1.30, 60],
            ['Loratadina 10mg', 'Antialérgicos', 0.70, 90],
            ['Cetirizina 10mg', 'Antialérgicos', 0.90, 70],
            ['Vitamina C 1g', 'Vitaminas', 1.00, 150],
            ['Complejo B', 'Vitaminas', 1.80, 9],
        ] as [$name, $category, $price, $stock]) {
            $medicines[] = Medicine::firstOrCreate(
                ['name' => $name],
                ['category_id' => $categories[$category]->id, 'price' => $price, 'stock' => $stock]
            );
        }

        // Clientes
        $customers = [];
        foreach ([
            ['Juan Pérez Quispe', '45871236', '987654321', 34],
            ['María López Rojas', '41236587', '912345678', 52],
            ['Carlos Ramos Huamán', '70123456', null, 27],
        ] as [$name, $dni, $phone, $age]) {
            $customers[] = Customer::firstOrCreate(
                ['dni' => $dni],
                ['name' => $name, 'phone' => $phone, 'age' => $age]
            );
        }

        // Ventas de ejemplo (solo si aún no hay ninguna)
        if (Sale::count() === 0) {
            $samples = [
                [0, 0, [[0, 4], [6, 2]]],
                [1, 0, [[1, 3]]],
                [2, 1, [[3, 1], [8, 2]]],
                [3, 1, [[5, 2], [7, 3]]],
                [4, 2, [[0, 10], [8, 5]]],
                [5, 2, [[1, 2]]],
                [6, 0, [[3, 2], [6, 1]]],
            ];

            foreach ($samples as [$daysAgo, $customerIndex, $lines]) {
                $total = 0;
                $rows = [];

                foreach ($lines as [$medicineIndex, $amount]) {
                    $medicine = $medicines[$medicineIndex];
                    $subtotal = round((float) $medicine->price * $amount, 2);
                    $total += $subtotal;
                    $rows[] = [$medicine, $amount, (float) $medicine->price, $subtotal];
                }

                $total = round($total, 2);

                $sale = Sale::create([
                    'customer_id' => $customers[$customerIndex]->id,
                    'user_id' => $user->id,
                    'date' => now()->subDays($daysAgo)->setTime(10 + $daysAgo, 15),
                    'total' => $total,
                    'igv' => round($total - $total / (1 + Sale::IGV_RATE), 2),
                ]);

                foreach ($rows as [$medicine, $amount, $price, $subtotal]) {
                    DetailSale::create([
                        'sale_id' => $sale->id,
                        'medicine_id' => $medicine->id,
                        'amount' => $amount,
                        'price_unit' => $price,
                        'subtotal' => $subtotal,
                    ]);
                }
            }
        }
    }
}
