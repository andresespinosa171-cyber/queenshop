# Design: Multi-Store Branding — WolfStor alongside QueenShop

## Technical Approach

CSS custom properties + session-driven theming. QueenShop remains the `:root` default; WolfStor overrides via `body.theme-wolfstor`. Login loads company branding into `$_SESSION`; layouts consume it via helpers. Migration system in `config/database.php` extended with 3 new migrations. Zero new JS dependencies — lightbox is vanilla JS.

## Architecture Decisions

| Decision | Options | Tradeoff | Chosen |
|----------|---------|----------|--------|
| Theme engine | CSS vars vs SASS vs separate files | CSS vars require no build step, no new dep, runtime-switchable via class | CSS custom properties |
| Category model | New `Category` model vs add to Product | Project has no `Category` model; adding `company_id` param to `Product::getAllCategories()` is least risky but breaks abstraction | Add `company_id` param to existing Product method |
| Logo storage | DB blob vs file path | File path is simpler, matches existing `image_url()` pattern | VARCHAR column + filesystem |
| Store switcher | Session-only vs DB re-query on each request | Session-only avoids N+1 on every page load | Session rebind on switch |

## Data Flow

```
Login ──→ AuthController ──→ DB query (JOIN companies) ──→ $_SESSION[branding]
                                                               │
Layout ──→ current_store_name() ──→ navbar/title/footer ───→ <body class="theme-{theme}">
                                                               │
                                                          CSS :root + .theme-wolfstor
                                                               │
                                                          All themed elements

/switch-store/{id} ──→ SwitchController ──→ user_companies check ──→ $_SESSION rebind ──→ redirect /
```

## File Changes

| File | Action | Description |
|------|--------|-------------|
| `app/models/Company.php` | Create | Company model: findByUser(), findBySwitch() |
| `app/controllers/SwitchController.php` | Create | GET /switch-store/{id} — validates access, rebinds session |
| `assets/img/wolfstor-logo.svg` | Create | Blue shoe icon + "WolfStor" text |
| `config/database.php` | Modify | Add migrations 005, 006, 007 |
| `database/schema.sql` | Modify | Add branding cols, user_companies, category company_id |
| `database/schema.sqlite.sql` | Modify | Same schema changes for SQLite |
| `app/helpers/auth.php` | Modify | Add current_store_name(), current_company_logo(), current_company_theme_class(), current_user_companies() |
| `app/controllers/AuthController.php` | Modify | Login loads branding; register picks store_type → theme |
| `app/views/auth/login.php` | Modify | Dynamic logo + store name from session |
| `app/views/auth/register.php` | Modify | Add store_type select (pet_shop/shoe_store) |
| `app/views/auth/layout.php` | Modify | Dynamic `<title>` using store name |
| `app/views/layouts/main.php` | Modify | Dynamic logo, brand name, footer, title; store switcher dropdown; lightbox HTML |
| `assets/css/style.css` | Modify | Extract ALL hardcoded `#ffc107` to `:root` vars; add `.theme-wolfstor` block; add animation keyframes |
| `assets/js/app.js` | Modify | Add lightbox JS (click/ESC/click-outside handling) |
| `app/models/Product.php` | Modify | `getAllCategories()` accepts `?int $companyId` |

## Interfaces / Contracts

### New Session Keys (set on login, rebind on switch)

```
$_SESSION['theme']          // 'queen' | 'wolfstor'
$_SESSION['primary_color']  // '#ffc107' | '#2563eb'
$_SESSION['logo']           // 'queenshop-logo.svg' | 'wolfstor-logo.svg'
$_SESSION['store_name']     // 'QueenShop' | 'WolfStor'
$_SESSION['description']    // company description text
```

### New Helpers (`app/helpers/auth.php`)

```php
function current_store_name(): string       // $_SESSION['store_name'] ?? 'QueenShop'
function current_company_logo(): string      // $_SESSION['logo'] ?? 'logo.svg'
function current_theme_class(): string       // $_SESSION['theme'] ?? 'queen'
function current_user_companies(): array     // from user_companies pivot
```

### CSS Custom Properties (`:root` QueenShop defaults)

```
--store-primary: #ffc107       → .theme-wolfstor overrides to #2563eb
--store-secondary: #ff9800     → .theme-wolfstor: #1d4ed8
--store-bg: #121212            → (same for WolfStor)
--store-card-bg: #1e1e1e       → (same for WolfStor)
--transition-base: 0.2s ease
--transition-slow: 0.3s ease
```

### Migration 005, 006, 007

Follow existing `runMigrations()` pattern in `config/database.php`:
- **005**: `ALTER TABLE companies ADD COLUMN ...` (theme, store_name, logo, primary_color, description) + WolfStor seed
- **006**: `CREATE TABLE user_companies (user_id, company_id, UNIQUE)` + seed admin access to all companies
- **007**: `ALTER TABLE categories ADD COLUMN company_id INT DEFAULT 1` + WolfStor category seed

## Testing Strategy

No test runner available (confirmed in config.yaml). Manual checklist per spec scenario:

| Deliverable | What to Verify |
|-------------|---------------|
| Company branding | Login WolfStor shows blue theme, logo, "WolfStor" name. Login QueenShop unchanged |
| CSS refactor | No hardcoded `#ffc107` outside `:root`/`.theme-wolfstor`. All pages visually pass |
| Auth branding | Register with Shoe Store creates WolfStor-themed company. Register Pet Shop = QueenShop |
| Store switching | `/switch-store/3` updates theme. Invalid ID shows error. Sesion survives |
| Category scoping | WolfStor sees only shoe cats. QueenShop sees pet cats |
| Lightbox | Click product image → overlay. ESC/X/outside closes. No-image not clickable |
| Animations | Fade-in on page load, hover lift on cards, slide-up on modals |

## Migration / Rollout

No data migration. All additive (ALTER TABLE ADD COLUMN, CREATE TABLE). Rollback: reverse the `runMigrations()` entries in `database.php`.

## Open Questions

- None — all decisions resolved by existing patterns.
