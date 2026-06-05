# QueenShop MVC — AI Context

> Este archivo es para mantener contexto persistente entre sesiones de AI.
> No modificar manualmente — lo actualiza el asistente.

---

## Proyecto

QueenShop MVC — Sistema multi-empresa de gestión de tienda de mascotas.

- **Stack**: PHP 8.2, SQLite (PDO) con soporte MySQL via config/db.mysql.php, Bootstrap 5.3, Chart.js, Bootstrap Icons
- **Arquitectura**: MVC custom (Router, Controller, Model base), sin framework
- **Idioma**: Español (Rioplatense)
- **Tema**: Oscuro/Fondo #121212, texto blanco, amarillo primario, verde dinero positivo
- **Repo**: https://github.com/andresespinosa171-cyber/queenshop-mvc

---

## Usuarios de demo

| Usuario | Contraseña | Empresa | Rol |
|---------|-----------|---------|-----|
| `norte` | `123456` | QueenShop Norte | user |
| `sur` | `123456` | QueenShop Sur | user |
| `admin` | `123456` | QueenShop Norte | admin (ve todas las empresas) |

---

## Cambios SDD realizados

### queenshop-auth-company (Completado)

**Propósito**: Auth multi-empresa, scoping, rebrand, contabilidad.

| Fase | Estado | Descripción |
|------|--------|-------------|
| 1. DB & Config | ✅ | Schema (companies, users, company_id), timezone, COP, fortnight helpers |
| 2. Auth System | ✅ | AuthController, auth.php helpers, register/login/logout, auth views |
| 3. Routing & Scoping | ✅ | Auth routes + guard, company_id en models (Product/Sale), controllers |
| 4. UI & Rebrand | ✅ | QueenShop en navbar/footer/title, logout button, CSS legibilidad |
| 5. Admin & Accounting | ✅ | Admin role, dashboard por quincena default, contabilidad con gráficas |
| 6. Verification | ✅ | PHP syntax, login flow, auth guard, chart data, cross-company toggle |

---

## Estructura de archivos clave

```
/project/workspace/
├── index.php                     # Front controller + auth guard + todas las rutas
├── config/
│   ├── database.php              # DB con fallback MySQL → SQLite + migraciones
│   └── db.mysql.php              # Credenciales MySQL (infinityfree)
├── app/
│   ├── controllers/
│   │   ├── AuthController.php    # Login/register/logout
│   │   ├── DashboardController.php # Dashboard con quincena default
│   │   ├── AccountingController.php # Contabilidad mensual + cross-company
│   │   ├── ProductController.php
│   │   ├── SaleController.php
│   │   ├── ClientController.php  # CRUD clientes + gestión de deudas
│   │   ├── ReturnController.php  # Devoluciones con ajuste de stock
│   │   └── SwitchController.php  # Cambio entre tiendas (multi-company)
│   ├── models/
│   │   ├── Product.php           # Scoping por company_id
│   │   ├── Sale.php              # Scoping + fortnight filter + monthly stats
│   │   ├── Client.php            # Clientes + deudas
│   │   ├── Company.php           # Empresas + branding
│   │   └── ReturnModel.php       # Devoluciones
│   ├── helpers/
│   │   ├── auth.php              # require_login, current_company_id, is_admin, store helpers
│   │   └── functions.php         # COP format, fortnight helpers, session flash
│   ├── core/
│   │   ├── Router.php            # Router con parámetros URL
│   │   ├── Controller.php
│   │   └── Model.php
│   └── views/
│       ├── auth/                 # Login, register, auth layout
│       ├── dashboard/            # Dashboard con badge de quincena
│       ├── accounting/           # Contabilidad con gráfico Chart.js
│       ├── layouts/main.php      # Navbar + store switcher + logout + lightbox
│       ├── products/             # CRUD de productos
│       ├── sales/                # Ventas con carrito
│       ├── clients/              # CRUD clientes + gestión de deuda
│       ├── returns/              # Devoluciones (crear, listar, ver)
│       └── switch/               # Error al cambiar de tienda
├── database/
│   ├── schema.sql                # Schema MySQL completo + seed data
│   ├── schema.sqlite.sql         # Schema SQLite completo + seed data
│   └── petshop.db                # SQLite DB actual
├── assets/
│   ├── css/style.css             # Tema oscuro con soporte multi-store (WolfStor)
│   ├── js/app.js                 # Chart.js + carrito + lightbox
│   └── img/                      # Logos por tienda
├── _AI_CONTEXT.md                # Este archivo
```

---

## Estado actual del sitio (Junio 2026)

- ✅ **Login/Register** — Funcionando, centrado vertical, oscuro, inputs grandes
- ✅ **Dashboard** — KPIs, quincena default, chart ventas 14 días, stock alerts
- ✅ **Productos** — CRUD completo, búsqueda, restock, imágenes, lightbox
- ✅ **Ventas** — Carrito con búsqueda de productos, descuentos, finalizar venta
- ✅ **Clientes** — CRUD, deudas, pagos, ajustes
- ✅ **Contabilidad** — KPIs anuales, chart mensual (Chart.js), toggle cross-company (admin)
- ✅ **Devoluciones** — Crear desde venta, ajuste de stock, reintegrar deuda
- ✅ **Multi-tienda** — Switch entre empresas con branding distinto (QueenShop amarillo, WolfStor azul)
- ✅ **WolfStor (tienda zapatos)** — Company ID=3, tema azul #2563eb, logo wolfstor-logo.svg
- ✅ **Tema oscuro** — Fondo #121212, texto blanco, amarillo primario (#ffc107), verde dinero (#28a745)
- ✅ **DB MySQL/SQLite** — Fallback automático cuando MySQL no está disponible
- ⚠️ **WolfStor logo** — El archivo `wolfstor-logo.svg` necesita crearse en `/assets/img/`

## Decisiones de arquitectura

| Decisión | Opción elegida | Por qué |
|----------|---------------|---------|
| Auth | Session-based | Simple, sin librerías extra, usa $_SESSION existente |
| Scoping | Model-layer | company_id en cada query evita olvidos en controllers |
| Layout auth | layout.php separado | Sin navbar en páginas de login |
| Password | BCrypt cost=10 | Rápido en SQLite, amplio soporte |
| Fortnight | Default ON con ?all=1 | El dinero "se reinicia" cada 15 días |
| Admin | role column en users | Simple, sin tabla extra de roles |
| Gráficas | Chart.js (ya incluido) | Sin dependencias nuevas |
| Moneda | COP ($1.234.567) | Sin decimales, punto como separador de miles |
| Timezone | America/Bogota | Colombia |
| Tema visual | Oscuro con amarillo primario | Fondo #121212, texto blanco, amarillo (#ffc107) como color principal, verde (#28a745) para dinero positivo, rojo para pérdidas |

---

## Cómo correr localmente

```bash
php -S localhost:8080 -t /project/workspace
```

---

## Pendientes / Ideas futuras

- [ ] Agregar PHPUnit via Composer para tests automatizados
- [ ] CSRF middleware para rutas POST
- [ ] Exportar contabilidad a CSV/PDF
- [ ] Recuperación de contraseña
- [ ] Roles más granulares (admin, superadmin, etc.)

---

## Última actualización

2026-06-05 — Sesión 4: fix conexión MySQL (fallback a SQLite cuando MySQL no está disponible), verificación general del sitio.
