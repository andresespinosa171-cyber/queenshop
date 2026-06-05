# Especificación de Categorías por Empresa (Category Scoping)

## Propósito

Cada empresa ve solo sus propias categorías. Se agrega `company_id` a la tabla `categories` para aislar el catálogo por tienda.

## Requisitos

### Requisito: company_id en categories

La tabla `categories` DEBE incluir `company_id` como clave foránea a `companies.id` con valor por defecto 1.

| # | Escenario | Dado | Cuando | Entonces |
|---|-----------|------|--------|----------|
| C1 | Migración agrega columna | Tabla categories existe | Migración ejecutada | `company_id` se agrega con FK a companies.id |
| C2 | Default company_id | Categorías existentes | Migración | Reciben `company_id = 1` (QueenShop Norte) |
| C3 | WolfStor categorías propias | WolfStor tiene categorías | Seed | WolfStor categorías tienen `company_id = 3` |

### Requisito: Filtrado por empresa

TODAS las consultas de categorías DEBEN filtrar por `company_id` de la sesión actual.

| # | Escenario | Dado | Cuando | Entonces |
|---|-----------|------|--------|----------|
| C4 | QueenShop ve QueenShop cats | Usuario empresa 1 | Lista categorías | Solo ve categorías con `company_id = 1` |
| C5 | WolfStor ve WolfStor cats | Usuario empresa 3 | Lista categorías | Solo ve categorías con `company_id = 3` |
| C6 | Productos filtran por cat correcta | Producto usa categoría | CRUD | Categoría pertenece a la misma empresa que el producto |

### Requisito: Seed de categorías por empresa

El seed DEBE crear categorías base para QueenShop (Alimentos, Juguetes, Higiene, etc.) y categorías para WolfStor (Zapatos, Botas, Sandalias, etc.).

| # | Escenario | Dado | Cuando | Entonces |
|---|-----------|------|--------|----------|
| C7 | QueenShop categorías seed | Seed ejecutado | Categorías QueenShop existen | 7 categorías con `company_id = 1` |
| C8 | WolfStor categorías seed | Seed ejecutado | Categorías WolfStor existen | Categorías de calzado con `company_id = 3` |
