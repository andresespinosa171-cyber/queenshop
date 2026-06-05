# Especificación de Cambio de Tienda (Store Switching)

## Propósito

Usuarios con acceso a múltiples empresas pueden cambiar entre tiendas sin cerrar sesión mediante un selector en la navbar y un endpoint `/switch-store/{id}`.

## Requisitos

### Requisito: Tabla pivote user_companies

DEBE existir la tabla `user_companies` con `user_id` (FK → users), `company_id` (FK → companies), `created_at`. Un usuario puede tener múltiples filas.

| # | Escenario | Dado | Cuando | Entonces |
|---|-----------|------|--------|----------|
| S1 | Admin vinculado a todas | Usuario admin existe | Se ejecuta seed | Admin tiene filas en user_companies para todas las empresas |
| S2 | Usuario normal una empresa | Usuario normal existe | Seed | Tiene solo su empresa asignada |
| S3 | Sin duplicados | Mismo par user+company | Insert | UNIQUE(user_id, company_id) lo rechaza |

### Requisito: Endpoint /switch-store/{id}

El sistema DEBE exponer `GET /switch-store/{id}` que cambia `company_id`, `company_name` y datos de branding en `$_SESSION`, y redirige a `/`.

| # | Escenario | Dado | Cuando | Entonces |
|---|-----------|------|--------|----------|
| S4 | Cambio exitoso | Usuario con acceso a 2 empresas | Navega a `/switch-store/2` | Sesión actualiza `company_id`, `company_name`, `theme`, `primary_color`; redirige a `/` |
| S5 | Sin acceso | Usuario sin permiso para empresa 3 | Navega a `/switch-store/3` | Sistema muestra error "No tenés acceso a esa tienda" y redirige a `/` |
| S6 | ID inexistente | Empresa 99 no existe | Navega a `/switch-store/99` | Sistema muestra error y redirige a `/` |

### Requisito: Selector en navbar

Usuarios con acceso a múltiples empresas DEBEN ver un dropdown en la navbar para cambiar de tienda.

| # | Escenario | Dado | Cuando | Entonces |
|---|-----------|------|--------|----------|
| S7 | Dropdown visible | Usuario tiene 2+ empresas en user_companies | Navbar renderiza | Muestra un botón/dropdown con lista de empresas disponibles |
| S8 | Dropdown oculto | Usuario tiene 1 empresa | Navbar renderiza | No muestra el selector de tiendas |
| S9 | Empresa activa marcada | Usuario en WolfStor | Dropdown abierto | WolfStor aparece como activa (check o resaltado) |

### Requisito: Conservar sesión

El cambio de tienda NO DEBE destruir la sesión ni pedir credenciales nuevamente.

| # | Escenario | Dado | Cuando | Entonces |
|---|-----------|------|--------|----------|
| S10 | Sesión intacta | Usuario autenticado | Cambia de tienda | `user_id`, `username`, `role` permanecen igual |
