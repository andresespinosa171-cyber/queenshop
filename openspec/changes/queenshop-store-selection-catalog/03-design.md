# Diseño Técnico: Menú de Selección de Tienda y Catálogo de Zapatos

## Arquitectura

### Flujo de Navegación (Nuevo)

```
/ (público)
  → StoreSelectionController@index
    → Click QueenShop → /login (store_preselected=queenshop)
    → Click WolfStor → /login (store_preselected=wolfstor)

/login (público, con store_preselected)
  → Muestra branding de la tienda
  → Login valida acceso vía user_companies
  → Éxito → /dashboard

/register (público, con store_preselected)
  → Muestra branding de la tienda
  → Crea cuenta ligada a la tienda seleccionada

/switch-store/{id}
  → Destruye sesión → redirect a /

/logout
  → Destruye sesión → redirect a /
```

### Nuevos Archivos

| Archivo | Propósito |
|---------|-----------|
| `app/controllers/StoreSelectionController.php` | Landing page con tarjetas de tiendas |
| `app/views/store-selection/index.php` | Vista con dos cards estilo Netflix |

### Archivos Modificados

| Archivo | Cambio |
|---------|--------|
| `index.php` | Ruta `/` → StoreSelectionController, auth guard excluye `/` |
| `app/views/auth/layout.php` | Botón "Volver" si hay store_preselected |
| `app/views/auth/login.php` | Usar branding de store_preselected |
| `app/views/auth/register.php` | Usar branding de store_preselected |
| `app/controllers/AuthController.php` | Store preselected flow, validación user_companies |
| `app/controllers/SwitchController.php` | Logout + redirect a landing |
| `app/helpers/auth.php` | Helper `current_store_preselected()` |
| `app/views/layouts/main.php` | "Cambiar tienda" = logout |
| `config/database.php` | Nueva migración 008_shoe_attributes |
| `app/models/Product.php` | Nuevos campos en queries, getAllCategories, filtros |
| `app/controllers/ProductController.php` | Shoe fields, dynamic filters |
| `app/views/products/index.php` | Filtros dinámicos (color, marca, género, tipo) |
| `app/views/products/create.php` | Campos extra para WolfStor |
| `app/views/products/edit.php` | Campos extra para WolfStor |
| `app/views/clients/index.php` | Mejora de contraste |

### Store Pre-selection Session Keys

```php
$_SESSION['store_preselected'] = [
    'company_id' => 1,      // o 3 para WolfStor
    'name' => 'QueenShop',
    'theme' => 'queenshop',
    'primary_color' => '#ffc107',
    'logo' => 'logo.svg',
]
```

### SQL Migration (008_shoe_attributes)

```sql
ALTER TABLE products ADD COLUMN color VARCHAR(100) NOT NULL DEFAULT '';
ALTER TABLE products ADD COLUMN brand VARCHAR(100) NOT NULL DEFAULT '';
ALTER TABLE products ADD COLUMN gender VARCHAR(20) NOT NULL DEFAULT '';
ALTER TABLE products ADD COLUMN boot_type VARCHAR(20) NOT NULL DEFAULT '';
```

### Filtros Dinámicos — Algoritmo

1. En ProductController@index, detectar si es WolfStor
2. Ejecutar consultas DISTINCT:
   - `SELECT DISTINCT color FROM products WHERE company_id = X AND color != '' ORDER BY color`
   - `SELECT DISTINCT brand FROM products WHERE company_id = X AND brand != '' ORDER BY brand`
3. Pasar valores a la vista
4. Los checkboxes se renderizan con name="colors[]" (array)
5. WHERE clause en la query de productos: `AND color IN (...params...)`

### Visibilidad de Textos

- Tabla usa clases `table-dark` (fondo oscuro nativo de Bootstrap)
- Columnas de precio con `text-warning` (amarillo)  
- Profit con `text-success` / `text-danger` (verde/rojo brillante)
- Badges con `bg-warning text-dark` para alta legibilidad
- Links en celdas con `text-info` (azul claro)
