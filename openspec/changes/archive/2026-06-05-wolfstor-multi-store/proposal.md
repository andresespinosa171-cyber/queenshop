# Proposal: Multi-Store Branding — WolfStor alongside QueenShop

## Intent

QueenShop is multi-company at the data layer but monolithic in branding — hardcoded yellow (`#ffc107`) theme everywhere. Adding WolfStor (blue shoe store) requires per-company theming across DB, CSS, layout, and auth without duplicating the codebase.

## Scope

### In Scope
- DB: branding columns on companies + `user_companies` pivot + WolfStor seed
- CSS: hardcoded colors → custom properties; WolfStor blue theme block
- Layout: dynamic store name, logo, theme class from session
- Auth: login loads branding, registration picks store type, `/switch-store/{id}` endpoint
- Assets: WolfStor SVG logo at `assets/img/wolfstor-logo.svg`
- Feature: product image lightbox (click-to-expand modal)
- Catalog: add `company_id` to categories for per-store visibility
- Animations: fade-in, slide-up, hover effects, image zoom

### Out of Scope
- Subdomains, i18n, payments, per-store analytics

## Capabilities

> This section is the CONTRACT between proposal and specs phases.

### New Capabilities
- `company-branding`: Per-company theme (colors, logo, store name, description) loaded from DB and applied via CSS custom properties
- `store-switching`: Multi-store access via `user_companies` pivot + `/switch-store/{id}` endpoint
- `category-scoping`: `company_id` on categories for per-store catalog visibility
- `product-image-lightbox`: Click-to-expand product image viewer component

### Modified Capabilities
None — no existing specs.

## Approach

CSS custom properties approach (per exploration recommendation):
1. Add branding columns to companies + seed WolfStor (`#2563eb` blue)
2. Create `Company` model + `user_companies` pivot table
3. Refactor `style.css`: extract hardcoded colors to `:root` vars, add `body.theme-wolfstor` override block
4. Update session helpers + layout for dynamic branding
5. Update auth login, registration; add switch-store endpoint
6. Add product image lightbox + CSS animations
7. Migrate categories for company scoping

## Affected Areas

| Area | Impact | What changes |
|------|--------|-------------|
| `database/schema.sql` | Modified | Branding cols, pivot, category company_id |
| `app/models/Company.php` | New | Company model |
| `app/helpers/auth.php` | Modified | Branding session helpers |
| `app/controllers/AuthController.php` | Modified | Login/register branding, switch-store |
| `app/views/layouts/main.php` | Modified | Dynamic nav/footer/title |
| `app/views/auth/layout.php` | Modified | Dynamic auth page title |
| `assets/css/style.css` | Modified | Custom properties + WolfStor theme |
| `assets/img/wolfstor-logo.svg` | New | WolfStor shoe store logo |
| `app/controllers/ProductController.php` | Modified | Lightbox view support |
| `app/views/product/detail.php` | Modified | Image click → lightbox |

## Risks

| Risk | Likelihood | Mitigation |
|------|------------|------------|
| CSS refactor misses hardcoded color | Med | Visual QA pass on every page |
| Category migration misses shared queries | Low | Audit all category queries |
| No test runner — regressions manual | High | Structured manual checklist per flow |

## Rollback Plan

1. **DB**: Additive migration — revert by deleting WolfStor row + new columns.
2. **CSS**: QueenShop is `:root` default — removing WolfStor block restores original.
3. **Session**: Old sessions without branding keys fall back to QueenShop defaults.
4. **Full revert**: `git revert` the merge commit.

## Dependencies

- PHP 8.2 + SQLite/MySQL (no new dependencies)

## Success Criteria

- [ ] WolfStor login shows blue theme + shoe logo + "WolfStor" name
- [ ] QueenShop login shows yellow theme + original logo unchanged
- [ ] `/switch-store/{id}` changes theme without re-login
- [ ] Product image opens in lightbox on click
- [ ] WolfStor sees only its own categories
- [ ] CSS custom properties control ALL themed colors
