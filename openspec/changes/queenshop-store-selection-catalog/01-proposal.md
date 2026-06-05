# Propuesta: Menú de Selección de Tienda y Catálogo de Zapatos

## Intención

Transformar QueenShop en un sistema multi-tienda con selector de tienda tipo Netflix, donde el usuario elige qué tienda visitar antes de iniciar sesión, y cada cambio de tienda requiere re-login. Además, adaptar WolfStor como tienda de zapatos con campos específicos (color, marca, género, tipo de bota) y filtros dinámicos.

## Problema

1. **Sin selector de tienda**: El landing actual es el dashboard (requiere login). No hay forma de elegir entre QueenShop y WolfStor antes de autenticarse.
2. **Cambio de tienda inseguro**: El switch de tienda actual es seamless, no fuerza re-autenticación.
3. **WolfStor usa schema genérico**: Los productos de zapatos no tienen campos específicos (color, marca, género, tipo de bota).
4. **Filtros insuficientes**: La lista de productos solo tiene filtros básicos (nombre, categoría, stock). WolfStor necesita filtros por color, marca, género, tipo de bota.
5. **Textos con mala visibilidad**: Los textos en productos y clientes no se ven bien en tema oscuro.

## Alcance

### Incluye
- Landing page pública con tarjetas de QueenShop y WolfStor
- Login/Register contextuales (muestran la tienda seleccionada)
- Botón "Volver" en login/register para ir al selector
- Cambio de tienda = logout + redirect al selector
- Botón "Cambiar tienda" en navbar (cierra sesión)
- Nuevas columnas en productos: color, brand, gender, boot_type
- Migración de BD para las nuevas columnas
- Formulario de producto con campos extra para WolfStor
- Filtros dinámicos: color (multi-select), marca (multi-select), género, tipo de bota
- Mejora de contraste y visibilidad de textos en vistas de productos y clientes

### Excluye
- Testing automatizado (no hay test runner en el proyecto)
- CSRF middleware (no existe actualmente, no se agrega)
- Imágenes reales de productos (solo schema para los nuevos campos)
- Paginación de productos (se mantiene el listado actual)
- API REST para filtros (se implementa server-side con GET params)

## Enfoque Técnico

### Landing Page
- Nuevo `StoreSelectionController` con método `index()`
- Ruta pública `/` apunta a `StoreSelectionController@index`
- Dashboard se mantiene en `/dashboard` (requiere login)
- Auth guard se actualiza: `/` es pública

### Store Preselection
- Al hacer clic en una tienda, se guarda `$_SESSION['store_preselected'] = ['company_id' => X, 'name' => ..., 'theme' => ...]`
- Login valida que el usuario tenga acceso a esa tienda vía `user_companies`
- Login y Register muestran el branding de la tienda seleccionada

### Store Switch = Logout
- `/switch-store/{id}` destruye la sesión y redirige a `/` con flash message
- Navbar: "Cambiar tienda" ejecuta logout y redirige a landing

### Schema de Productos (WolfStor)
```
ALTER TABLE products ADD COLUMN color VARCHAR(100) DEFAULT '';
ALTER TABLE products ADD COLUMN brand VARCHAR(100) DEFAULT '';
ALTER TABLE products ADD COLUMN gender VARCHAR(20) DEFAULT '';
ALTER TABLE products ADD COLUMN boot_type VARCHAR(20) DEFAULT '';
```

### Filtros Dinámicos
- Los filtros de color, marca, género y tipo de bota se generan desde los valores existentes en productos (SELECT DISTINCT)
- Multi-select mediante checkboxes con name array (`colors[]`, `brands[]`)
- Se mantienen los filtros existentes (nombre, categoría, stock)

### Visibilidad de Textos
- Aumentar contraste en tablas: usar `table-dark` class, mejor color de badges
- Texto de celdas con mejor contraste sobre fondo oscuro
- Consistentes en productos y clientes

## Riesgos
- **Regresión en QueenShop**: Los cambios de schema podrían afectar consultas existentes en la tienda de mascotas. Se usan defaults vacíos para las nuevas columnas.
- **Pérdida de sesión**: Cambiar switch-store a logout podría confundir usuarios existentes. Se agrega flash message explicativo.
- **Rendimiento**: Los SELECT DISTINCT para filtros dinámicos son livianos en SQLite con pocos productos.

## Rollback
- Schema: `ALTER TABLE products DROP COLUMN` o recrear desde schema.sql
- Landing: restaurar ruta `/` a DashboardController
- Switch: restaurar SwitchController original
