# Tasks: WolfStor Multi-Store

## Review Workload Forecast

| Field | Value |
|-------|-------|
| Changed lines | 800–1100 |
| 800-line risk | High |
| Chained PRs | Yes |
| Split | PR 1 (Foundation+CSS) → PR 2 (Switch+Cats) → PR 3 (Lightbox+Polish) |
| Strategy | ask-on-risk |

Decision needed before apply: Yes
Chained PRs recommended: Yes
Chain strategy: stacked-to-main (PR 3/3)
800-line budget risk: High

## Phase 1: Foundation

- [ ] 1.1 Migration 005 in `config/database.php`: ALTER companies ADD (theme,store_name,logo,primary_color,description); seed WolfStor
- [ ] 1.2 Create `app/models/Company.php` with findByUser() and findBySwitch()
- [ ] 1.3 Add helpers to `auth.php`: current_store_name(), current_company_logo(), current_theme_class(), current_user_companies()
- [ ] 1.4 AuthController::login() — load branding (theme,logo,store_name,primary_color) into $_SESSION
- [ ] 1.5 AuthController::register() — add store_type field, auto-assign theme, create company with branding cols
- [ ] 1.6 `register.php` view — add store_type <select> (pet_shop/shoe_store)
- [ ] 1.7 `login.php` view — render logo/name from session, fallback to QueenShop
- [ ] 1.8 `auth/layout.php` — dynamic `<title>` from store_name
- [ ] 1.9 `layouts/main.php` — dynamic body class, logo, brand name, footer, title from session
- [ ] 1.10 Create `assets/img/wolfstor-logo.svg` — blue shoe icon
- [ ] 1.11 Update `schema.sql` + `schema.sqlite.sql` — branding cols + WolfStor seed

## Phase 2: CSS Architecture

- [ ] 2.1 Define :root vars in `style.css`: --store-primary, --store-secondary, --store-bg, --store-card-bg, --transition-* (QueenShop defaults)
- [ ] 2.2 Add `body.theme-wolfstor` block: --store-primary: #2563eb, --store-secondary: #1d4ed8
- [ ] 2.3 Replace ALL hardcoded `#ffc107` with `var(--store-primary)` across style.css
- [ ] 2.4 Verify zero hardcoded `#ffc107` outside :root/.theme-wolfstor
- [ ] 2.5 Add @keyframes (fadeIn, slideUp) + utility classes

## Phase 3: Store Switching

- [x] 3.1 Migration 006 in `database.php`: CREATE TABLE user_companies (user_id,company_id UNIQUE)
- [x] 3.2 Migration 006b: seed user_companies — user id=1 access to all companies
- [x] 3.3 Create `SwitchController.php`: GET /switch-store/{id} — validates user_companies access, rebinds session, redirects /
- [x] 3.4 Add route `$router->get('/switch-store/{id}', 'SwitchController@switch')` in index.php
- [x] 3.5 Navbar in main.php: store-switcher dropdown, visible only if count($allCompanies) > 1

## Phase 4: Categories & Lightbox

- [x] 4.1 Migration 007: ALTER categories ADD COLUMN company_id INT DEFAULT 1
- [x] 4.2 Seed WolfStor shoe cats + update QueenShop seed cats with company_id=1
- [x] 4.3 `Product::getAllCategories()` — accept ?int $companyId param, filter by it
- [x] 4.4 `ProductController::index()`, `create()`, `edit()` — pass current_company_id() to getAllCategories()
- [x] 4.5 Lightbox CSS in style.css: overlay, centered img, close btn, zoom toggle
- [x] 4.6 Lightbox JS in app.js: click handler, ESC/click-outside close, zoom fit↔2x
- [x] 4.7 Lightbox HTML overlay in main.php; add .product-img-clickable to product views
- [x] 4.8 Update schema.sql + schema.sqlite.sql — category company_id + seeds

## Phase 5: Animations & Polish

- [x] 5.1 Apply `.fade-in` to `<main>` in main.php — on-load fade
- [x] 5.2 `.card-hover-lift`: translateY(-4px) + shadow on card hover
- [x] 5.3 `.btn-hover-scale`: transform scale(1.03) on button hover
- [x] 5.4 `.stagger-row`: incremental animation-delay on table rows
- [x] 5.5 Verify all transitions use transform/opacity only — zero layout shift
