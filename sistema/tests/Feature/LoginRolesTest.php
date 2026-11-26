<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Medicine;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// Casos del Paso 18 de la guía S14: login, roles, dashboard y reporte
class LoginRolesTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true; // ejecuta DatabaseSeeder (roles, 4 usuarios, ventas...)

    private function user(string $email): User
    {
        return User::where('email', $email)->firstOrFail();
    }

    public function test_login_page_loads(): void
    {
        $this->get('/login')->assertOk()->assertSee('Iniciar sesión')->assertDontSee('loginForm');
    }

    public function test_wrong_password_shows_generic_message_and_keeps_email(): void
    {
        $this->from('/login')->post('/login', ['email' => 'admin@sanjuan.com', 'password' => '12345678'])
            ->assertRedirect('/login')
            ->assertSessionHasErrors(['email' => 'El correo o la contraseña no son correctos.'])
            ->assertSessionHasInput('email', 'admin@sanjuan.com');
        $this->assertGuest();
    }

    public function test_correct_login_goes_to_dashboard_with_user_data(): void
    {
        $this->post('/login', ['email' => 'admin@sanjuan.com', 'password' => 'password'])
            ->assertRedirect(route('dashboard.index'));
        $this->assertAuthenticated();

        $this->get('/')->assertOk()
            ->assertSee('admin@sanjuan.com')
            ->assertSee('Administrador')
            ->assertSee('chartDays')
            ->assertSee('Ventas de hoy');
    }

    public function test_intended_url_after_login(): void
    {
        $this->get('/medicines')->assertRedirect('/login');
        $this->post('/login', ['email' => 'admin@sanjuan.com', 'password' => 'password'])
            ->assertRedirect('/medicines');
    }

    public function test_logout_then_login_page_shows_message(): void
    {
        $this->actingAs($this->user('admin@sanjuan.com'));
        $this->post('/logout')->assertRedirect(route('login'));
        $this->assertGuest();
        $this->get('/login')->assertSee('Cerraste sesión correctamente.');
        $this->get('/sales')->assertRedirect('/login');
    }

    public function test_logged_user_cannot_see_login(): void
    {
        $this->actingAs($this->user('admin@sanjuan.com'))->get('/login')->assertRedirect('/');
    }

    public function test_cashier_permissions(): void
    {
        $cashier = $this->user('cmendoza@sanjuan.com');
        $this->actingAs($cashier);

        $this->get('/')->assertOk()->assertDontSee('Configuraciones')->assertDontSee('Medicamentos</span>', false);
        $this->get('/customers')->assertOk();
        $this->get('/sales')->assertOk()->assertDontSee('Anular');
        $this->get('/sales/create')->assertOk();

        foreach (['/users', '/roles', '/categories', '/medicines', '/reports/sales'] as $url) {
            $this->get($url)->assertForbidden()->assertSee('Acceso denegado');
        }

        $sale = Sale::first();
        $this->delete("/sales/{$sale->id}")->assertForbidden();
    }

    public function test_chemist_permissions(): void
    {
        $this->actingAs($this->user('rquispe@sanjuan.com'));
        $this->get('/medicines')->assertOk();
        $this->get('/reports/sales')->assertOk();
        $this->get('/users')->assertForbidden();
        $this->get('/categories')->assertForbidden();
    }

    public function test_admin_can_open_everything(): void
    {
        $this->actingAs($this->user('admin@sanjuan.com'));
        foreach (['/', '/customers', '/sales', '/sales/create', '/medicines', '/reports/sales', '/users', '/roles', '/categories'] as $url) {
            $this->get($url)->assertOk();
        }
        $this->get('/sales')->assertSee('Anular');
    }

    public function test_sale_is_registered_with_logged_in_seller(): void
    {
        $chemist = $this->user('rquispe@sanjuan.com');
        $medicine = Medicine::where('stock', '>', 5)->first();
        $customer = Customer::first();

        $this->actingAs($chemist)->post('/sales', [
            'customer_id' => $customer->id,
            'user_id' => $this->user('admin@sanjuan.com')->id, // se ignora: el vendedor es quien inició sesión
            'items' => [['medicine_id' => $medicine->id, 'amount' => 2]],
        ])->assertRedirect();

        $sale = Sale::latest('id')->first();
        $this->assertSame($chemist->id, $sale->user_id);
        $this->get(route('sales.show', $sale))->assertSee('Rosa Quispe Huamán');
    }

    public function test_report_filters_and_validation(): void
    {
        $this->actingAs($this->user('admin@sanjuan.com'));

        $this->get('/reports/sales?user_id=' . $this->user('ltorres@sanjuan.com')->id . '&category_id=1')->assertOk();

        $this->from('/reports/sales')->get('/reports/sales?from=2026-11-20&to=2026-11-01')
            ->assertRedirect('/reports/sales')
            ->assertSessionHasErrors(['to' => 'El campo fecha final debe ser una fecha igual o posterior a fecha inicial.']);
    }
}
