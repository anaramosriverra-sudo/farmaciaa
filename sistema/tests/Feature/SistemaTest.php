<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Medicamento;
use App\Models\Role;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SistemaTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $rol = Role::firstOrCreate(['name' => 'Administrador']);

        return User::factory()->create(['role_id' => $rol->id, 'email' => 'admin@test.com']);
    }

    private function vendedor(): User
    {
        $rol = Role::firstOrCreate(['name' => 'Vendedor']);

        return User::factory()->create(['role_id' => $rol->id]);
    }

    private function medicamento(int $stock = 20, float $price = 10): Medicamento
    {
        $cat = Category::firstOrCreate(['nombre' => 'General']);

        return Medicamento::create(['category_id' => $cat->id, 'name' => 'Paracetamol', 'price' => $price, 'stock' => $stock]);
    }

    public function test_invitado_es_redirigido_al_login(): void
    {
        foreach (['/', '/reportes', '/medicamentos', '/clientes', '/ventas', '/categories', '/roles', '/usuarios'] as $url) {
            $this->get($url)->assertRedirect('/login');
        }
    }

    public function test_login_correcto_e_incorrecto(): void
    {
        $user = $this->admin();

        $this->post('/login', ['email' => $user->email, 'password' => 'mala'])->assertSessionHasErrors('email');
        $this->assertGuest();

        $this->post('/login', ['email' => $user->email, 'password' => 'password'])->assertRedirect('/');
        $this->assertAuthenticatedAs($user);

        $this->post('/logout')->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_todas_las_pestanas_cargan(): void
    {
        $this->actingAs($this->admin());
        $m = $this->medicamento();
        $c = Customer::create(['name' => 'Ana', 'dni' => '12345678']);

        $this->post('/ventas', ['customer_id' => $c->id, 'items' => [['medicine_id' => $m->id, 'amount' => 2]]]);
        $sale = Sale::first();

        foreach ([
            '/', '/reportes', '/reportes?desde=2026-01-01&hasta=2026-12-31',
            '/medicamentos', '/medicamentos/create', "/medicamentos/{$m->id}/edit",
            '/clientes', '/clientes/create', "/clientes/{$c->id}/edit",
            '/ventas', '/ventas/create', "/ventas/{$sale->id}",
            '/categories', '/categories/create', '/categories/'.$m->category_id.'/edit',
            '/roles', '/roles/create', '/usuarios', '/usuarios/create',
        ] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_crud_categorias(): void
    {
        $this->actingAs($this->admin());

        $this->post('/categories', ['nombre' => 'Vitaminas', 'descripcion' => 'x'])->assertRedirect('/categories');
        $this->post('/categories', ['nombre' => 'Vitaminas'])->assertSessionHasErrors('nombre');
        $this->post('/categories', ['nombre' => ''])->assertSessionHasErrors('nombre');

        $cat = Category::where('nombre', 'Vitaminas')->first();
        $this->put("/categories/{$cat->id}", ['nombre' => 'Vitaminas C'])->assertRedirect('/categories');
        $this->assertDatabaseHas('categories', ['nombre' => 'Vitaminas C']);

        $this->delete("/categories/{$cat->id}")->assertRedirect('/categories');
        $this->assertDatabaseMissing('categories', ['id' => $cat->id]);
    }

    public function test_no_se_elimina_categoria_con_medicamentos(): void
    {
        $this->actingAs($this->admin());
        $m = $this->medicamento();

        $this->delete("/categories/{$m->category_id}")->assertSessionHas('error');
        $this->assertDatabaseHas('categories', ['id' => $m->category_id]);
    }

    public function test_crud_medicamentos(): void
    {
        $this->actingAs($this->admin());
        $cat = Category::create(['nombre' => 'Analgésicos']);

        $this->post('/medicamentos', ['category_id' => $cat->id, 'name' => 'Ibuprofeno', 'price' => 1.5, 'stock' => 10])
            ->assertRedirect('/medicamentos');
        $this->post('/medicamentos', ['category_id' => $cat->id, 'name' => 'X', 'price' => -1, 'stock' => 1])
            ->assertSessionHasErrors('price');

        $m = Medicamento::where('name', 'Ibuprofeno')->first();
        $this->put("/medicamentos/{$m->id}", ['category_id' => $cat->id, 'name' => 'Ibuprofeno 400', 'price' => 2, 'stock' => 5])
            ->assertRedirect('/medicamentos');
        $this->assertDatabaseHas('medicamentos', ['id' => $m->id, 'name' => 'Ibuprofeno 400', 'stock' => 5]);

        $this->get('/medicamentos?q=400')->assertSee('Ibuprofeno 400');
        $this->delete("/medicamentos/{$m->id}")->assertRedirect('/medicamentos');
        $this->assertDatabaseMissing('medicamentos', ['id' => $m->id]);
    }

    public function test_crud_clientes_y_validacion_dni(): void
    {
        $this->actingAs($this->admin());

        $this->post('/clientes', ['name' => 'Ana Soto', 'dni' => '12345678', 'phone' => '999888777', 'age' => 30])
            ->assertRedirect('/clientes');
        $this->post('/clientes', ['name' => 'Otra', 'dni' => '123'])->assertSessionHasErrors('dni');
        $this->post('/clientes', ['name' => 'Otra', 'dni' => '12345678'])->assertSessionHasErrors('dni');

        $c = Customer::first();
        $this->put("/clientes/{$c->id}", ['name' => 'Ana Soto R', 'dni' => '12345678'])->assertRedirect('/clientes');
        $this->assertDatabaseHas('customers', ['id' => $c->id, 'name' => 'Ana Soto R']);

        $this->delete("/clientes/{$c->id}")->assertRedirect('/clientes');
        $this->assertDatabaseMissing('customers', ['id' => $c->id]);
    }

    public function test_crud_roles_y_protecciones(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin);

        $this->post('/roles', ['name' => 'Supervisor', 'description' => 'x'])->assertRedirect('/roles');
        $rol = Role::where('name', 'Supervisor')->first();
        $this->put("/roles/{$rol->id}", ['name' => 'Supervisor 2'])->assertRedirect('/roles');
        $this->assertDatabaseHas('roles', ['id' => $rol->id, 'name' => 'Supervisor 2']);

        // Administrador: ni se renombra ni se elimina
        $this->put("/roles/{$admin->role_id}", ['name' => 'Otro', 'description' => 'desc'])->assertRedirect('/roles');
        $this->assertDatabaseHas('roles', ['id' => $admin->role_id, 'name' => 'Administrador', 'description' => 'desc']);
        $this->delete("/roles/{$admin->role_id}")->assertSessionHas('error');

        // Rol con usuarios no se elimina; sin usuarios sí
        $this->delete("/roles/{$admin->role_id}");
        $this->delete("/roles/{$rol->id}")->assertSessionMissing('error');
        $this->assertDatabaseMissing('roles', ['id' => $rol->id]);
    }

    public function test_crud_usuarios(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin);
        $datos = ['role_id' => $admin->role_id, 'name' => 'Pedro Gil', 'dni' => '22223333', 'email' => 'pedro@test.com',
            'password' => 'clave1234', 'password_confirmation' => 'clave1234'];

        $this->post('/usuarios', $datos)->assertRedirect('/usuarios');
        $this->post('/usuarios', array_merge($datos, ['dni' => '22223334', 'email' => 'p2@test.com', 'password' => '123', 'password_confirmation' => '123']))
            ->assertSessionHasErrors('password');

        $u = User::where('email', 'pedro@test.com')->first();
        $hash = $u->password;
        $this->put("/usuarios/{$u->id}", array_merge($datos, ['name' => 'Pedro R', 'password' => '', 'password_confirmation' => '']))
            ->assertRedirect('/usuarios');
        $u->refresh();
        $this->assertSame('Pedro R', $u->name);
        $this->assertSame($hash, $u->password, 'La contraseña no debe cambiar si se deja en blanco');

        $this->delete("/usuarios/{$admin->id}")->assertSessionHas('error');
        $this->delete("/usuarios/{$u->id}")->assertRedirect('/usuarios');
        $this->assertDatabaseMissing('users', ['id' => $u->id]);
    }

    public function test_vendedor_no_accede_a_roles_ni_usuarios(): void
    {
        $this->actingAs($this->vendedor());

        $this->get('/roles')->assertForbidden();
        $this->get('/usuarios')->assertForbidden();
        $this->get('/ventas')->assertOk();
        $this->get('/medicamentos')->assertOk();
    }

    public function test_venta_descuenta_stock_calcula_igv_y_se_puede_anular(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin);
        $m = $this->medicamento(stock: 20, price: 10);
        $c = Customer::create(['name' => 'Ana', 'dni' => '12345678']);

        // 3 + 2 del mismo medicamento se unifican en una línea de 5
        $this->post('/ventas', [
            'customer_id' => $c->id,
            'items' => [['medicine_id' => $m->id, 'amount' => 3], ['medicine_id' => $m->id, 'amount' => 2]],
        ])->assertRedirect();

        $sale = Sale::with('details')->first();
        $this->assertSame(15, $m->fresh()->stock);
        $this->assertCount(1, $sale->details);
        $this->assertSame(5, $sale->details[0]->amount);
        $this->assertEquals(50.00, $sale->total);
        $this->assertEquals(7.63, $sale->igv);          // 50 - 50/1.18
        $this->assertSame($admin->id, $sale->user_id);

        // No se puede borrar lo ya vendido
        $this->delete("/medicamentos/{$m->id}")->assertSessionHas('error');
        $this->delete("/clientes/{$c->id}")->assertSessionHas('error');

        // Anular devuelve el stock
        $this->delete("/ventas/{$sale->id}")->assertRedirect('/ventas');
        $this->assertSame(20, $m->fresh()->stock);
        $this->assertDatabaseCount('sales', 0);
        $this->assertDatabaseCount('details', 0);
    }

    public function test_venta_rechaza_stock_insuficiente_y_carrito_vacio(): void
    {
        $this->actingAs($this->admin());
        $m = $this->medicamento(stock: 4);

        $this->post('/ventas', ['items' => [['medicine_id' => $m->id, 'amount' => 5]]])->assertSessionHasErrors('items');
        $this->post('/ventas', [])->assertSessionHasErrors('items');

        $this->assertSame(4, $m->fresh()->stock);
        $this->assertDatabaseCount('sales', 0);
    }

    public function test_vendedor_no_puede_anular_ventas(): void
    {
        $this->actingAs($this->vendedor());
        $m = $this->medicamento();
        $this->post('/ventas', ['items' => [['medicine_id' => $m->id, 'amount' => 1]]]);

        $this->delete('/ventas/'.Sale::first()->id)->assertForbidden();
        $this->assertDatabaseCount('sales', 1);
    }

    public function test_dashboard_muestra_alerta_de_stock_bajo(): void
    {
        $this->actingAs($this->admin());
        $this->medicamento(stock: 3);

        $this->get('/')->assertOk()->assertSee('Stock bajo');
    }
}
