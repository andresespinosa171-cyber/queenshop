# Proposal: QueenShop Multi-Company Auth

## Intent

Single-tenant PetShop needs multi-company data isolation. Rebrand to QueenShop with session-based auth, COP localization, and fortnightly dashboard periods.

## Scope

### In Scope
- Rebrand PetShop → QueenShop (titles, navbar, footer)
- Register/login/logout with `password_hash()`/`password_verify()`
- `company_id` FK on products & sales; typed in all queries
- Dashboard: company-scoped stats + fortnight toggle (d1-15 / d16-end)
- COP currency ($1.234.567, no decimals), America/Bogota tz
- CSS legibility: higher contrast, cleaner typography

### Out of Scope
- Roles/perms inside a company
- Password reset, email verification, API tokens

## Capabilities

No existing specs — all new.

### New Capabilities
- `company-auth`: Register, login, logout, session guard, password hashing
- `company-scoping`: Data isolation by `company_id` across products, sales, dashboard
- `localization`: COP format, America/Bogota tz, fortnight helpers + toggle

### Modified Capabilities
None.

## Approach

1. **Schema**: Add `companies`, `users`; `company_id` FK on products/sales. Seed 2 demo companies.
2. **Auth routes before all others** in front controller. `require_login()` redirects to `/login`. Auth views = no-navbar layout.
3. **Session**: `user_id`, `company_id`, `company_name` on login; destroy on logout.
4. **Scoping**: Controllers pass `$_SESSION['company_id']` → models. All queries add `AND company_id = ?`.
5. **Localization**: `date_default_timezone_set('America/Bogota')`, COP format, fortnight helper.
6. **Rebrand**: s/PetShop/QueenShop in all views.

## Affected Areas

| Path | Impact |
|------|--------|
| `database/schema.sql` | Modified |
| `config/database.php` | Modified |
| `index.php` | Modified |
| `app/core/Controller.php` | Modified |
| `app/helpers/functions.php` | Modified |
| `app/helpers/auth.php` | **New** |
| `app/controllers/AuthController.php` | **New** |
| `app/controllers/*Controller.php` | Modified (3 files) |
| `app/models/*.php` | Modified (2 files) |
| `app/views/auth/login.php` | **New** |
| `app/views/auth/register.php` | **New** |
| `app/views/layouts/main.php` | Modified |
| `assets/css/style.css` | Modified |

## Risks

| Risk | Likelihood | Mitigation |
|------|------------|------------|
| Existing rows lack company_id → broken queries | High | Default 1 via migration |
| Password hash slow on SQLite | Low | BCrypt cost 10 |

## Rollback Plan

1. `git checkout -- database/ config/ index.php app/ assets/`
2. Drop `companies`, `users`; rm `company_id` cols (SQLite dump + rebuild)
3. Restore `database/petshop.db.bak`

## Dependencies

PHP 8.2, SQLite + PDO (existing).

## Success Criteria

- [ ] New company registers → sees empty isolated data
- [ ] Demo companies see only their own products
- [ ] Dashboard stats filter by company + fortnight
- [ ] COP: `$1.234.567` (no decimals)
- [ ] Logout clears session → redirect to `/login`
- [ ] Unauthenticated access redirects to `/login`
