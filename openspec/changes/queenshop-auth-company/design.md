# Design: QueenShop Multi-Company Auth

## Technical Approach

Extend the existing custom MVC with session-based auth, company-scoped queries, and COP localization. Add `companies` + `users` tables, `company_id` FK on products/sales, an `AuthController`, a `require_login()` guard, and fortnight dashboard toggling. Rebrand PetShop→QueenShop across all visible text.

---

## Architecture Decisions

| Option | Tradeoffs | Decision |
|--------|-----------|----------|
| Session-based vs. JWT auth | Sessions: simpler, no extra libs, natural PHP fit. JWT: stateless, token refresh overhead, no existing middleware. | **Sessions** — matches existing `$_SESSION` flash pattern, zero new deps |
| Model-layer scoping vs. controller-layer | Model: every query method adds `AND company_id = ?` — one place, no risk of forgetting. Controller: must remember to filter in every action. | **Model-layer scoping** — `Product::getAll($filters)` and `Sale::getAll($filters)` accept `company_id`, append to WHERE |
| Separate auth layout vs. conditional in main | Auth layout: no navbar, centered card, cleaner. Conditional in main: adds complexity to a shared template. | **New `views/auth/layout.php`** — keeps auth pages lean, no nav/logout confusion |
| Transaction for register | Company + user must be atomic on SQLite. Single-user reg means no contention risk. | **BEGIN/COMMIT around INSERT company → INSERT user** — no orphan companies |
| Fortnight logic as helpers vs. model methods | Helpers: pure functions (`in_current_fortnight($date)`) usable anywhere. Model methods: only in queries. | **Pure helpers in `functions.php`** — dashboard toggle can filter in PHP or SQL |
| BCrypt vs. Argon2 | BCrypt cost=10 ~100ms on SQLite, Argon2 ~200ms. Both supported on PHP 8.2. | **`password_hash(PASSWORD_BCRYPT, ['cost' => 10])`** — faster on SQLite, wide compat |

---

## Data Flow

```
Registration:
  Browser → POST /register → AuthController@register
    → validate username (unique, 3-50 alnum), password (min 6)
    → BEGIN TX → INSERT companies(name) → INSERT users(company_id, username, hash)
    → COMMIT → redirect /login (flash "Registrado correctamente")

Login:
  Browser → POST /login → AuthController@login
    → find user by username → password_verify()
    → $_SESSION = {user_id, company_id, company_name, username}
    → redirect /

Auth Guard (every protected request):
  index.php before dispatch: if !$_SESSION['user_id'] && route not in {/login,/register} → redirect /login

Scoped Query:
  Controller: $companyId = current_company_id();
  Model: Product::getAll(array_merge($filters, ['company_id' => $companyId]))
  SQL: SELECT ... FROM products WHERE 1=1 AND company_id = ? ...
```

---

## File Changes

| File | Action | Description |
|------|--------|-------------|
| `database/schema.sql` | Modify | Add `companies`, `users` tables; `company_id` cols on products + sales; seed data |
| `config/database.php` | Modify | Add `date_default_timezone_set('America/Bogota')` on DB init |
| `index.php` | Modify | Auth routes before dashboard/products/sales; add `require_once helpers/auth.php`; call `require_login()` before dispatch for protected routes |
| `app/core/Controller.php` | Modify | Add `$this->view('auth/...')` awareness — skip main layout for auth views? (Or use separate layout) |
| `app/controllers/AuthController.php` | Create | loginForm, login, registerForm, register, logout — 5 actions |
| `app/controllers/DashboardController.php` | Modify | Pass `company_id` to all model calls; add `$fortnightFilter` toggle; pass `$showFortnight` to view |
| `app/controllers/ProductController.php` | Modify | Pass `current_company_id()` to `getAll()`, `apiSearch()`, `create()` |
| `app/controllers/SaleController.php` | Modify | Pass `company_id` to `getAll()`, `createWithItems()`, dashboard stats |
| `app/models/Product.php` | Modify | Add `company_id` param to `getAll()`, `getLowStock()`, `getOutOfStock()`, `getStockValue()`, `apiSearch()`, `increaseStock()` — append `AND p.company_id = ?` |
| `app/models/Sale.php` | Modify | Add `company_id` to `getAll()`, `createWithItems()`, all dashboard stat methods — append `AND s.company_id = ?`, `AND si.company_id = ?` where needed |
| `app/helpers/auth.php` | Create | `require_login(): void`, `current_company_id(): int`, `current_company_name(): string` |
| `app/helpers/functions.php` | Modify | `format_currency()` → COP (no decimals, `.` as thousands sep); add `is_first_fortnight()`, `is_second_fortnight()`, `current_fortnight_range()`, `in_current_fortnight($date)` |
| `app/views/auth/layout.php` | Create | Minimal layout (no navbar): Bootstrap CDN, centered card container |
| `app/views/auth/login.php` | Create | Login form, POST to `/login`, link to `/register` |
| `app/views/auth/register.php` | Create | Register form (username, password, company name), POST to `/register` |
| `app/views/layouts/main.php` | Modify | Rebrand PetShop→QueenShop; add logout button in navbar when session active |
| `app/views/dashboard/index.php` | Modify | Add fortnight toggle button; pass `$currentFortnight` for active state |
| `app/views/products/index.php` | Modify | Text rebrand (no structural change) |
| `app/views/sales/index.php` | Modify | Text rebrand (no structural change) |
| `assets/css/style.css` | Modify | `.text-muted` → `#495057` (`!important`); body bg → `#fff`; card shadow → `0 2px 8px rgba(0,0,0,0.08)`; active nav link contrast; dark mode `.text-muted` → `#adb5bd` |

---

## Interfaces

### `app/helpers/auth.php`
```php
function require_login(): void {
    if (empty($_SESSION['user_id'])) {
        $_SESSION['_flash']['error'] = 'Debés iniciar sesión primero.';
        header('Location: /login');
        exit;
    }
}

function current_company_id(): int {
    return (int) ($_SESSION['company_id'] ?? 0);
}

function current_company_name(): string {
    return $_SESSION['company_name'] ?? '';
}
```

### Model scoping contract
Every model method that queries products or sales accepts `?int $companyId = null` and appends `AND table.company_id = ?` when non-null. Controllers always pass `current_company_id()`.

### Session contract (on login)
```php
$_SESSION['user_id']       = $user['id'];
$_SESSION['company_id']    = $user['company_id'];
$_SESSION['company_name']  = $company['name'];
$_SESSION['username']      = $user['username'];
```

---

## Routes

| Method | Path | Handler | Auth? |
|--------|------|---------|-------|
| GET | `/login` | `AuthController@loginForm` | No |
| POST | `/login` | `AuthController@login` | No |
| GET | `/register` | `AuthController@registerForm` | No |
| POST | `/register` | `AuthController@register` | No |
| GET | `/logout` | `AuthController@logout` | Yes (destroy session) |
| GET | `/` | `DashboardController@index` | Yes |
| GET | `/dashboard` | `DashboardController@index` | Yes |
| GET | `/products*` | `ProductController@*` | Yes |
| POST | `/products*` | `ProductController@*` | Yes |
| GET | `/sales*` | `SaleController@*` | Yes |
| POST | `/sales*` | `SaleController@*` | Yes |
| GET | `/api/products` | `ProductController@apiList` | Yes |

Auth routes registered BEFORE all others in `index.php`. `require_login()` called after route matching but before controller dispatch — skips guard for `/login` and `/register` (both GET+POST).

---

## Fortnight Toggle

Dashboard passes `$showFortnight` (bool) from a GET param `?fortnight=1`. When active:
- Dashboard stat methods filter by `in_current_fortnight(s.created_at)` in SQL
- Toggle button in view switches between "Todo" and "Quincena actual"
- Helpers in `functions.php` compute range from day-of-month (1-15 = first, 16-end = second)

---

## Testing Strategy

| Layer | What to Test | Approach |
|-------|-------------|----------|
| Unit | Fortnight helpers, COP format | Manual verification (`php -r` or ad-hoc) |
| Integration | Register → login → scoped data | Manual flow: register company A, create product, verify company B doesn't see it |
| E2E | Full auth flow, logout redirect | Browser testing — register, login, verify session, logout, verify redirect |

*Note: No PHP test runner is configured. Testing is manual until PHPUnit is added.*

## Migration / Rollout

1. **Schema**: Add `companies` + `users` tables; `ALTER TABLE products ADD company_id`; `ALTER TABLE sales ADD company_id`
2. **Seed**: INSERT QueenShop Legacy (id=1); UPDATE products SET company_id=1; UPDATE sales SET company_id=1
3. **Guard**: Deploy auth guard last — existing session-less users will be prompted to log in immediately
4. Rollback: `git checkout -- database/ config/ index.php app/ assets/` + restore `petshop.db.bak`

## Open Questions

- [ ] Should existing session data be preserved or invalidated on deploy?
- [ ] Default company name for registration — use the username's company name or prompt the user?
