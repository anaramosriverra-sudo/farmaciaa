# Cambios realizados

Módulos funcionando: Dashboard, Reportes, Medicamentos, Clientes, Ventas, Roles, Usuarios y Categorías.

## Cómo activarlo
```bash
cd sistema
php artisan migrate:fresh --seed   # recrea la BD (se corrigió la tabla detail_sales) y carga datos de ejemplo
php artisan serve
```
Usuario de ejemplo: admin@sanjuan.com / admin123 (aún no hay pantalla de login).

## Qué se corrigió / agregó
- Categorías: errores de tipeo en modelo y controlador (`Iluminate`, `hasMany[...]`, `ignote`, `edit` apuntando al index), `<tr>` roto en la vista.
- Migración `detail_sales`: `amount` era datetime y la columna se llamaba `price unit` (ahora `amount` entero y `price_unit`).
- Modelos con relaciones y `$fillable` (Medicine, Customer, Sale, DetailSale).
- CRUD completo: Medicamentos, Clientes, Roles, Usuarios (con protecciones al eliminar registros relacionados).
- Ventas: formulario dinámico, precio tomado de la BD, validación y descuento de stock en transacción, IGV 18 % (precios incluyen IGV), detalle imprimible y anulación con devolución de stock.
- Reportes: filtro por fechas, ventas por día / medicamento / categoría, stock bajo, exportar CSV, imprimir.
- Dashboard: KPIs, gráfico de 7 días, últimas ventas, más vendidos, stock bajo.
- Sidebar con todos los enlaces y estado activo; campana con alertas reales de stock bajo.
- Textos de validación en español, `APP_LOCALE=es`, zona horaria America/Lima.

## Login
- Pantalla de login (`/login`), cierre de sesión con confirmación y todas las rutas protegidas con `auth`.
- El menú lateral muestra el nombre, correo y rol del usuario que inició sesión.
- Usuario de ejemplo: admin@sanjuan.com / admin123 (se crea con `php artisan migrate:fresh --seed`; si ya tienes datos, solo crea o edita un usuario desde la pestaña Usuarios).
