# Cambios realizados (Guía S14: login, roles, dashboard y reportes)

## Cómo levantar el sistema
```bash
cd sistema
composer install
copy .env.example .env      # solo la primera vez
php artisan key:generate    # solo la primera vez
php artisan migrate:fresh --seed
php artisan storage:link
php artisan serve
```

## Usuarios de prueba (contraseña de todos: `password`)
| Correo | Rol |
|---|---|
| admin@sanjuan.com | Administrador |
| rquispe@sanjuan.com | Químico farmacéutico |
| cmendoza@sanjuan.com | Cajero |
| ltorres@sanjuan.com | Cajero |

## Por qué fallaba el inicio de sesión
- **Migraciones duplicadas**: había dos `create_roles_table`, dos `categories`, dos `customers`, dos `sales`,
  `medicamentos`/`medicines`, `details`/`detail_sales` y `role_id` agregado dos veces. `php artisan migrate`
  se detenía con «Table already exists» y nunca se creaban los usuarios: el login siempre decía que los datos
  no eran correctos.
- **Columnas mezcladas**: la tabla `roles` tenía `nombre` pero otras partes usaban `name`; el seeder no ponía el
  `dni` (obligatorio) del usuario, así que también fallaba.
- El formulario de login tenía `id="loginForm"` y cargaba `app.js` (simulación del prototipo).
- `sales/index` llamaba a `isAdmin()`, que no existía en `User` → error al abrir Ventas.
- No existía el middleware de roles ni la página 403; el botón Salir no mostraba mensaje.

## Qué se hizo
- Se unificó la base del proyecto con la versión de la sesión 13 (`sistema.zip`): una sola migración por tabla,
  columnas `name`/`description`, imagen en medicamentos y seeders separados (Role, User, Category, Medicine,
  Customer, Sale con 60 ventas).
- **Login** (`LoginController`, `auth/login.blade.php`), rutas `guest`/`auth`, cierre de sesión por POST con
  mensaje «Cerraste sesión correctamente».
- **Roles**: `User::hasRole()`, middleware `CheckRole` con alias `role`, página `errors/403.blade.php`,
  menú lateral según el rol y datos reales del usuario conectado.
- **Ventas**: el vendedor es siempre el usuario que inició sesión; solo el administrador puede anular.
- **Dashboard**: 4 indicadores y 3 gráficos con Chart.js (`public/js/chart.umd.min.js`, funciona sin internet).
- **Reporte de ventas** (`/reports/sales`): rango de fechas y filtros por cliente, vendedor y categoría.
- Campana del encabezado con alertas reales de stock bajo.
- `APP_LOCALE=es`, bloque MySQL en `.env.example`, reglas `date`/`after_or_equal` en `lang/es/validation.php`.
- Pruebas automáticas en `tests/Feature/LoginRolesTest.php` (casos del Paso 18): `php artisan test`.
