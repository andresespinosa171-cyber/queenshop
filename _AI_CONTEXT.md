# QueenShop MVC — AI Context

> Este archivo es para mantener contexto persistente entre sesiones de AI.
> No modificar manualmente — lo actualiza el asistente.

---

## Proyecto

QueenShop MVC — Sistema multi-empresa de gestión de tienda de mascotas.

- **Stack**: PHP 8.2, SQLite (PDO), Bootstrap 5.3, Chart.js, Bootstrap Icons
- **Arquitectura**: MVC custom (Router, Controller, Model base), sin framework
- **Idioma**: Español (Rioplatense)
- **Tema**: Amarillo/Negro/Blanco
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
├── index.php                     # Front controller + auth guard
├── app/
│   ├── controllers/
│   │   ├── AuthController.php    # Login/register/logout
│   │   ├── DashboardController.php # Dashboard con quincena default
│   │   ├── AccountingController.php # Contabilidad mensual + cross-company
│   │   ├── ProductController.php
│   │   └── SaleController.php
│   ├── models/
│   │   ├── Product.php           # Scoping por company_id
│   │   └── Sale.php              # Scoping + fortnight filter + monthly stats
│   ├── helpers/
│   │   ├── auth.php              # require_login, current_company_id, is_admin
│   │   └── functions.php         # COP format, fortnight helpers
│   ├── core/
│   │   ├── Router.php
│   │   ├── Controller.php
│   │   └── Model.php
│   └── views/
│       ├── auth/                 # Login, register, auth layout
│       ├── dashboard/            # Dashboard con badge de quincena
│       ├── accounting/           # Contabilidad con gráfico Chart.js
│       ├── layouts/main.php      # Navbar + logout + navigation
│       ├── products/             # CRUD de productos
│       └── sales/                # Ventas con carrito
├── database/
│   ├── schema.sql                # Schema completo + seed data
│   └── petshop.db                # SQLite DB (se recrea al borrar)
├── assets/
│   ├── css/style.css             # Tema amarillo/negro/blanco
│   └── js/app.js                 # Chart.js initialization
├── openspec/
│   └── changes/queenshop-auth-company/  # SDD artifacts
└── _AI_CONTEXT.md                # Este archivo
```

---

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

2026-05-27 — Sesión completa: auth, scoping, rebrand, contabilidad, admin, charts.
