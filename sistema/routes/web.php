<?php

<<<<<<< Updated upstream
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('template/administrador');
// });
Route::get('/',[DashboardController::class, 'index'])->name('dashboard.index');

Route::resource('categories', CategoryController::class)->except('show');
=======
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MedicamentoController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Autenticación
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:10,1')->name('login.store');
});
Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

// Sistema (requiere sesión iniciada)
Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/reportes', [ReportController::class, 'index'])->name('reports.index');

    // Gestión
    Route::resource('medicamentos', MedicamentoController::class)->except('show');
    Route::resource('clientes', CustomerController::class)
        ->except('show')->names('customers')->parameters(['clientes' => 'customer']);
    Route::resource('ventas', SaleController::class)
        ->only(['index', 'create', 'store', 'show', 'destroy'])
        ->names('sales')->parameters(['ventas' => 'sale']);

    // Configuraciones
    Route::resource('categories', CategoryController::class)->except('show');

    Route::middleware('admin')->group(function () {
        Route::resource('roles', RoleController::class)->except('show');
        Route::resource('usuarios', UserController::class)
            ->except('show')->names('users')->parameters(['usuarios' => 'user']);
    });
});
>>>>>>> Stashed changes
