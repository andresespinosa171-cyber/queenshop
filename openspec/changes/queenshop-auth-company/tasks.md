# Tasks: QueenShop Multi-Company Auth

## Review Workload Forecast

| Field | Value |
|-------|-------|
| Estimated changed lines | ~580–650 |
| 400-line budget risk | Medium |
| Chained PRs recommended | Yes |
| Suggested split | PR 1 → PR 2 → PR 3 → PR 4 |
| Delivery strategy | ask-on-risk |
| Chain strategy | pending |

Decision needed before apply: Yes
Chained PRs recommended: Yes
Chain strategy: pending
400-line budget risk: Medium

### Suggested Work Units

| Unit | Goal | Likely PR | Notes |
|------|------|-----------|-------|
| 1 | Database schema, config, helpers | PR 1 | Foundation: tables, timezone, COP format, fortnight helpers |
| 2 | Auth system + routes | PR 2 | auth.php, AuthController, auth views, index.php auth routes |
| 3 | Data scoping (models + controllers) | PR 3 | company_id in all queries and controller calls |
| 4 | UI rebrand + verification | PR 4 | QueenShop text, CSS legibility, logout button, manual tests |

---

## Phase 1: Database & Config

- [x] 1.1 Update `database/schema.sql` — add `companies` + `users` tables; add `company_id` on `products` and `sales`; seed 2 demo companies with isolated products and sales
- [x] 1.2 Add `date_default_timezone_set('America/Bogota')` to `config/database.php` at the top of the file
- [x] 1.3 Reformat `format_currency()` in `app/helpers/functions.php` to COP (no decimals, `.` as thousands sep, `$` prefix)
- [x] 1.4 Add `is_first_fortnight()`, `is_second_fortnight()`, `current_fortnight_range()`, `in_current_fortnight()` to `app/helpers/functions.php`

## Phase 2: Auth System

- [x] 2.1 Create `app/helpers/auth.php` — `require_login()`, `current_company_id()`, `current_company_name()`
- [x] 2.2 Add optional `$layout` param to `Controller::view()` in `app/core/Controller.php` — allow passing `'auth/layout'` to skip main layout
- [x] 2.3 Create `app/controllers/AuthController.php` — `loginForm`, `login` (verify with `password_verify()`), `registerForm`, `register` (BCRYPT cost=10, transaction for company+user), `logout` (destroy session)
- [x] 2.4 Create `app/views/auth/layout.php` — minimal HTML shell with Bootstrap CDN, centered card container, no navbar
- [x] 2.5 Create `app/views/auth/login.php` — form with username + password, POST to `/login`, link to `/register`
- [x] 2.6 Create `app/views/auth/register.php` — form with username + password + company name, POST to `/register`

## Phase 3: Routing & Data Scoping

- [ ] 3.1 Update `index.php` — add `require_once auth.php` before autoloader; register auth routes before dashboard/products/sales; add `require_login()` call before `$router->dispatch()` for protected routes
- [ ] 3.2 Update `app/models/Product.php` — add `?int $companyId = null` param to `getAll()`, `getLowStock()`, `getOutOfStock()`, `getStockValue()`, `apiSearch()`; append `AND p.company_id = ?` when non-null
- [ ] 3.3 Update `app/models/Sale.php` — add `?int $companyId = null` to `getAll()`, `createWithItems()`, `getTodaySales()`, `getTotalSales()`, `getTotalCost()`, `getTodayProfit()`, `getTotalDiscounts()`, `getSaleCount()`, `getRecentSales()`, `getSalesByDay()`; append `AND s.company_id = ?`
- [ ] 3.4 Update `app/controllers/DashboardController.php` — pass `current_company_id()` to all model calls; add `$fortnightFilter` from GET param; pass `$showFortnight` to view
- [ ] 3.5 Update `app/controllers/ProductController.php` — pass `current_company_id()` to `getAll()` in `index()`, to `create()` in `store()`, to `apiSearch()` in `apiList()`
- [ ] 3.6 Update `app/controllers/SaleController.php` — pass `current_company_id()` to `getAll()` in `index()`, to `createWithItems()` in `store()`, to stat methods via `DashboardController`

## Phase 4: UI & Rebrand

- [ ] 4.1 Update `app/views/layouts/main.php` — replace PetShop→QueenShop in title, brand, footer; add logout button in navbar when session active (show company name + Cerrar sesión)
- [ ] 4.2 Update `app/views/dashboard/index.php` — add fortnight toggle button (link with `?fortnight=1`); show active state label
- [ ] 4.3 Rebrand PetShop→QueenShop in all view text: `products/index.php`, `products/create.php`, `products/edit.php`, `sales/index.php`, `sales/create.php`, `sales/show.php`
- [ ] 4.4 Update `assets/css/style.css` — `.text-muted` → `#495057` (`!important`); body bg → `#fff`; card shadow → `0 2px 8px rgba(0,0,0,0.08)`; dark mode `.text-muted` → `#adb5bd`; update `PetShop MVC` header comment
- [ ] 4.5 Rebrand `PetShop MVC` → `QueenShop MVC` in `index.php` header comment

## Phase 5: Verification

- [ ] 5.1 Manual: register new company → redirect to login → login → see empty dashboard
- [ ] 5.2 Manual: login as demo user (company 1) → products list shows only company 1 products
- [ ] 5.3 Manual: toggle fortnight on dashboard → stats filter to current fortnight range
- [ ] 5.4 Manual: logout → session destroyed → redirected to `/login`
- [ ] 5.5 Manual: visit `/` without session → redirected to `/login` with flash message
