<?php
$companyModel = new Company();
$allCompanies = $companyModel->getAll();
?>
<!DOCTYPE html>
<html lang="es" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars(current_store_name()) ?> — <?= htmlspecialchars($title ?? 'QueenShop') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="icon" type="image/svg+xml" href="<?= BASE_URL ?>/assets/img/logo.svg">
    <link href="<?= BASE_URL ?>/assets/css/style.css" rel="stylesheet">
</head>
<body class="theme-<?= htmlspecialchars(current_theme_class()) ?>">

    <!-- ─── Navbar ─────────────────────────────────────────────── -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="<?= BASE_URL ?>/">
                <?php $logoPath = current_company_logo(); ?>
                <?php if (str_starts_with($logoPath, 'http')): ?>
                    <img src="<?= htmlspecialchars($logoPath) ?>" alt="<?= htmlspecialchars(current_store_name()) ?>" width="32" height="32" class="d-inline-block align-text-bottom me-1">
                <?php else: ?>
                    <img src="<?= BASE_URL ?>/assets/img/<?= htmlspecialchars($logoPath) ?>" alt="<?= htmlspecialchars(current_store_name()) ?>" width="32" height="32" class="d-inline-block align-text-bottom me-1">
                <?php endif; ?>
                <?= htmlspecialchars(current_store_name()) ?>
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse"
                    data-bs-target="#mainNav" aria-controls="mainNav"
                    aria-expanded="false" aria-label="Abrir menú">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link <?= str_starts_with($_SERVER['REQUEST_URI'], BASE_URL . '/dashboard') || $_SERVER['REQUEST_URI'] === BASE_URL . '/' || $_SERVER['REQUEST_URI'] === BASE_URL ? 'active' : '' ?>"
                           href="<?= BASE_URL ?>/">
                            <i class="bi bi-grid-1x2-fill"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= str_starts_with($_SERVER['REQUEST_URI'], BASE_URL . '/product') ? 'active' : '' ?>"
                           href="<?= BASE_URL ?>/products">
                            <i class="bi bi-box-seam-fill"></i> Productos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= str_starts_with($_SERVER['REQUEST_URI'], BASE_URL . '/client') ? 'active' : '' ?>"
                           href="<?= BASE_URL ?>/clients">
                            <i class="bi bi-people-fill"></i> Clientes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= str_starts_with($_SERVER['REQUEST_URI'], BASE_URL . '/sale') ? 'active' : '' ?>"
                           href="<?= BASE_URL ?>/sales">
                            <i class="bi bi-cart-fill"></i> Ventas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= str_starts_with($_SERVER['REQUEST_URI'], BASE_URL . '/return') ? 'active' : '' ?>"
                           href="<?= BASE_URL ?>/returns">
                            <i class="bi bi-arrow-return-left"></i> Devoluciones
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= str_starts_with($_SERVER['REQUEST_URI'], BASE_URL . '/accounting') ? 'active' : '' ?>"
                           href="<?= BASE_URL ?>/accounting">
                            <i class="bi bi-graph-up"></i> Contabilidad
                        </a>
                    </li>
                </ul>

                <?php if (!empty($_SESSION['user_id']) && count($allCompanies) > 1): ?>
                    <span class="navbar-text ms-auto">
                        <div class="dropdown d-inline-block">
                            <a class="dropdown-toggle text-decoration-none text-light-emphasis" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-shop"></i> <?= htmlspecialchars(current_store_name()) ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <?php foreach ($allCompanies as $c): ?>
                                    <li>
                                        <a class="dropdown-item <?= (int)$c['id'] === current_company_id() ? 'active' : '' ?>"
                                           href="<?= BASE_URL ?>/switch-store/<?= $c['id'] ?>">
                                            <?= htmlspecialchars($c['store_name'] ?? $c['name']) ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </span>
                <?php endif; ?>

                <span class="navbar-text small text-light-emphasis">
                    <i class="bi bi-calendar3"></i> <?= date('d/m/Y') ?>
                </span>
                <?php if (!empty($_SESSION['user_id'])): ?>
                    <div class="d-flex align-items-center gap-2 ms-2">
                        <span class="navbar-text small text-light-emphasis">
                            <i class="bi bi-person-circle"></i> <?= htmlspecialchars($_SESSION['company_name'] ?? '') ?>
                        </span>
                        <a href="<?= BASE_URL ?>/logout" class="btn btn-sm btn-outline-warning">
                            <i class="bi bi-box-arrow-right"></i> Salir
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- ─── Flash Messages ─────────────────────────────────────── -->
    <div class="container mt-3">
        <?php if (session_has('success')): ?>
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 border-0 shadow-sm">
                <i class="bi bi-check-circle-fill fs-5"></i>
                <?= htmlspecialchars(session_get('success')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if (session_has('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 border-0 shadow-sm">
                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                <?= htmlspecialchars(session_get('error')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
    </div>

    <!-- ─── Page Content ───────────────────────────────────────── -->
    <main class="container fade-in mt-3 mb-5">
        <?= $content ?>
    </main>

    <!-- ─── Footer ─────────────────────────────────────────────── -->
    <footer class="footer mt-auto py-3 bg-body-tertiary border-top">
        <div class="container text-center text-muted small">
            <i class="bi bi-shop"></i> <?= htmlspecialchars(current_store_name()) ?> &mdash; <?= date('Y') ?>
        </div>
    </footer>

    <!-- ─── Product Image Lightbox ─────────────────────────── -->
    <div id="productLightbox" class="lightbox-overlay" role="dialog" aria-label="Vista ampliada de la imagen">
        <button id="lightboxClose" class="lightbox-close" aria-label="Cerrar">&times;</button>
        <img id="lightboxImage" src="" alt="Vista ampliada">
        <button id="lightboxZoom" class="lightbox-zoom">&#x1F50D; 2&times;</button>
    </div>

    <script>const BASE_URL = '<?= BASE_URL ?>';</script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    <script src="<?= BASE_URL ?>/assets/js/app.js"></script>
</body>
</html>
