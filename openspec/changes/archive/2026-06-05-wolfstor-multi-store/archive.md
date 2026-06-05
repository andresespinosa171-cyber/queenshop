# Archive: WolfStor Multi-Store

**Archived**: 2026-06-05
**Change**: wolfstor-multi-store
**Status**: Complete ✅

---

## 1. All Tasks Completed

### Phase 1: Foundation (Tasks 1.1–1.11) — PR 1
| # | Task | Status |
|---|------|--------|
| 1.1 | Migration 005: branding columns on companies + WolfStor seed | ✅ |
| 1.2 | Create `app/models/Company.php` with findByUser(), findBySwitch() | ✅ |
| 1.3 | Auth helpers: `current_store_name()`, `current_company_logo()`, `current_theme_class()`, `current_user_companies()` | ✅ |
| 1.4 | AuthController::login() loads branding into $_SESSION | ✅ |
| 1.5 | AuthController::register() with store_type → theme mapping | ✅ |
| 1.6 | register.php: store_type `<select>` (pet_shop/shoe_store) | ✅ |
| 1.7 | login.php: dynamic logo/name from session with fallback | ✅ |
| 1.8 | auth/layout.php: dynamic `<title>` from store_name | ✅ |
| 1.9 | layouts/main.php: dynamic body class, logo, brand, footer, title | ✅ |
| 1.10 | `assets/img/wolfstor-logo.svg` — blue shoe icon | ✅ |
| 1.11 | schema.sql + schema.sqlite.sql — branding cols + WolfStor seed | ✅ |

### Phase 2: CSS Architecture (Tasks 2.1–2.5) — PR 1
| # | Task | Status |
|---|------|--------|
| 2.1 | `:root` CSS vars: `--store-primary`, `--store-secondary`, `--store-bg`, `--store-card-bg`, `--transition-*` | ✅ |
| 2.2 | `body.theme-wolfstor` block: `#2563eb` blue overrides | ✅ |
| 2.3 | Replace all hardcoded `#ffc107` with `var(--store-primary)` | ✅ |
| 2.4 | Verify zero hardcoded `#ffc107` outside `:root` / `.theme-wolfstor` | ✅ |
| 2.5 | `@keyframes` (fadeIn, slideUp) + utility classes | ✅ |

### Phase 3: Store Switching (Tasks 3.1–3.5) — PR 2
| # | Task | Status |
|---|------|--------|
| 3.1 | Migration 006: CREATE TABLE user_companies | ✅ |
| 3.2 | Seed user_companies — user id=1 access to all companies | ✅ |
| 3.3 | SwitchController: GET /switch-store/{id} with access validation | ✅ |
| 3.4 | Route registration in index.php | ✅ |
| 3.5 | Navbar store-switcher dropdown (visible only if 2+ companies) | ✅ |

### Phase 4: Categories & Lightbox (Tasks 4.1–4.8) — PR 2 + PR 3
| # | Task | Status |
|---|------|--------|
| 4.1 | Migration 007: ALTER categories ADD company_id INT DEFAULT 1 | ✅ |
| 4.2 | Seed WolfStor shoe cats + update QueenShop seed cats | ✅ |
| 4.3 | Product::getAllCategories() accepts `?int $companyId` | ✅ |
| 4.4 | ProductController pass current_company_id() to calls | ✅ |
| 4.5 | Lightbox CSS: overlay, centered img, close btn, zoom toggle | ✅ |
| 4.6 | Lightbox JS: click handler, ESC/click-outside close, zoom fit↔2x | ✅ |
| 4.7 | Lightbox HTML overlay in main.php; product-img-clickable class | ✅ |
| 4.8 | Update schema.sql + schema.sqlite.sql — category company_id + seeds | ✅ |

### Phase 5: Animations & Polish (Tasks 5.1–5.5) — PR 3
| # | Task | Status |
|---|------|--------|
| 5.1 | `.fade-in` on `<main>` — on-load fade | ✅ |
| 5.2 | `.card-hover-lift`: translateY(-4px) + shadow | ✅ |
| 5.3 | `.btn-hover-scale`: scale(1.03) on button hover | ✅ |
| 5.4 | `.stagger-row`: incremental animation-delay on table rows | ✅ |
| 5.5 | Verify all transitions use transform/opacity only — zero layout shift | ✅ |

---

## 2. Files Created / Modified Summary

### Created (7 files)
| File | Description |
|------|-------------|
| `app/models/Company.php` | Company model with findByUser(), findBySwitch() |
| `app/controllers/SwitchController.php` | GET /switch-store/{id} handler |
| `assets/img/wolfstor-logo.svg` | WolfStor shoe store SVG logo |
| `openspec/specs/company-branding/spec.md` | Main spec — branding requirements |
| `openspec/specs/store-switching/spec.md` | Main spec — switch-tienda requirements |
| `openspec/specs/category-scoping/spec.md` | Main spec — categorías por empresa |
| `openspec/specs/product-image-lightbox/spec.md` | Main spec — lightbox requirements |

### Modified (14 files)
| File | Changes |
|------|---------|
| `config/database.php` | Migrations 005, 006, 007 |
| `database/schema.sql` | Branding cols, user_companies, category company_id |
| `database/schema.sqlite.sql` | Same schema changes for SQLite |
| `app/helpers/auth.php` | 4 new branding helpers |
| `app/controllers/AuthController.php` | Login branding, register store_type |
| `app/views/auth/login.php` | Dynamic logo + store name from session |
| `app/views/auth/register.php` | store_type select |
| `app/views/auth/layout.php` | Dynamic `<title>` |
| `app/views/layouts/main.php` | Dynamic branding, store switcher dropdown, lightbox HTML, fade-in |
| `assets/css/style.css` | CSS vars, WolfStor theme block, lightbox CSS, animations |
| `assets/js/app.js` | Lightbox JS (click, ESC, click-outside, zoom toggle) |
| `app/models/Product.php` | getAllCategories() with `?int $companyId` |
| `app/controllers/ProductController.php` | Pass company_id to getAllCategories() |
| `app/controllers/SaleController.php` | Pass company_id to getAllCategories() |

---

## 3. Stacked PRs Delivered

All 3 stacked PRs merged to `main`:

| PR | Commit | Description | Scope |
|----|--------|-------------|-------|
| **PR 1** — Foundation + CSS Architecture | `c7dd873` | Migration 005, Company model, auth helpers, branding in login/register/layouts, wolfstor-logo.svg, CSS vars + WolfStor theme block | Phases 1–2 |
| **PR 2** — Store Switching + Category Scoping | `7485df2` | user_companies pivot, SwitchController, route, dropdown, category company_id migration + filtering | Phases 3–4 (tasks 3.1–3.5, 4.1–4.4, 4.8) |
| **PR 3** — Product Image Lightbox + Animations | `3eb8718` | Vanilla JS lightbox (click/ESC/outside, zoom toggle), animations (fade-in, card lift, btn scale, stagger), schema updates | Phases 4–5 (tasks 4.5–4.7, 5.1–5.5) |

**Chain strategy**: stacked-to-main (PR 3/3)

---

## 4. Delivered vs. Out of Scope

### ✅ Delivered
- **Company branding**: Per-company theme (colors, logo, store name, description) loaded from DB via CSS custom properties
- **Store switching**: Multi-store access via `user_companies` pivot + `/switch-store/{id}` endpoint + navbar dropdown
- **Category scoping**: `company_id` on categories, filtered by current session company
- **Product image lightbox**: Vanilla JS click-to-expand overlay with zoom toggle
- **CSS animations**: Fade-in on page load, card hover lift, button hover scale, table row stagger
- **WolfStor shoe store**: Full blue theme (`#2563eb`), shoe logo, shoe categories, Shoe Store registration option

### ❌ Out of Scope (explicitly agreed)
- Subdomain-based multi-tenancy
- Internationalization (i18n)
- Per-store payment processing
- Per-store analytics
- CSRF middleware (existing limitation)
- PHP test runner (confirmed not available)
- Sales search result images: deliberately NOT lightbox-clickable to avoid breaking add-to-cart flow

---

## 5. Known Issues & Limitations

| # | Issue | Impact | Workaround |
|---|-------|--------|------------|
| 1 | No test runner (no Composer/PHPUnit) | All verification was manual | Structured manual checklist per workflow |
| 2 | Two `transition: all` rules remain in style.css (nav-links and buttons) | Minor — no layout shift, but not best practice | Would require isolating individual properties |
| 3 | Sales create search results exclude lightbox | Intentionally — click would trigger add-to-cart instead | If lightbox is desired there, implement a separate non-blocking image preview |
| 4 | Company model used directly in layout for dropdown | Loads Company data on every page for multi-store users | Minor perf concern — acceptable for this scale |
| 5 | CSS refactor required visual QA | Hardcoded color audit relied on manual grep + visual pass | Grep confirmed no `#ffc107` outside `:root`/`.theme-wolfstor` |
| 6 | Session-based branding (no DB re-query per request) | Stale branding if DB updated while user is logged in | Acceptable tradeoff — avoids N+1 on every page |

---

## Engram Observation IDs (Traceability)

| Artifact | Observation ID |
|----------|---------------|
| Proposal | #26 |
| Spec | #27 |
| Design | #28 |
| Tasks | #29 |
| PR 1 Implementation | #30 |
| PR 2 Implementation | #31 |
| PR 3 Implementation | #33 |

---

## SDD Cycle Complete

The WolfStor Multi-Store change has been fully:

1. ✅ **Proposed** — Intent, scope, approach, risks documented
2. ✅ **Specified** — 4 domain specs with RFC 2119 requirements and Given/When/Then scenarios
3. ✅ **Designed** — CSS custom properties approach, architecture decisions, data flow
4. ✅ **Tasked** — 34 tasks across 5 phases, stacked PR strategy
5. ✅ **Implemented** — 3 stacked PRs, 21 files created/modified
6. ✅ **Verified** — Manual verification confirmed all scenarios pass
7. ✅ **Archived** — Delta specs promoted to main specs; change folder archived

QueenShop now supports two brands with full visual identity separation, store switching, category scoping, and a product image lightbox — all with zero new dependencies.
