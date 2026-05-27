<!DOCTYPE html>
<html lang="es" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'PetShop') ?> — PetShop MVC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
</head>
<body>

    <!-- ─── Navbar ─────────────────────────────────────────────── -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/">
                <i class="bi bi-shop"></i> PetShop
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse"
                    data-bs-target="#mainNav" aria-controls="mainNav"
                    aria-expanded="false" aria-label="Abrir menú">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link <?= str_starts_with($_SERVER['REQUEST_URI'], '/dashboard') || $_SERVER['REQUEST_URI'] === '/' ? 'active' : '' ?>"
                           href="/">
                            <i class="bi bi-grid-1x2-fill"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= str_starts_with($_SERVER['REQUEST_URI'], '/product') ? 'active' : '' ?>"
                           href="/products">
                            <i class="bi bi-box-seam-fill"></i> Productos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= str_starts_with($_SERVER['REQUEST_URI'], '/sale') ? 'active' : '' ?>"
                           href="/sales">
                            <i class="bi bi-cart-fill"></i> Ventas
                        </a>
                    </li>
                </ul>
                <span class="navbar-text small text-light-emphasis">
                    <i class="bi bi-calendar3"></i> <?= date('d/m/Y') ?>
                </span>
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
    <main class="container mt-3 mb-5">
        <?= $content ?>
    </main>

    <!-- ─── Footer ─────────────────────────────────────────────── -->
    <footer class="footer mt-auto py-3 bg-body-tertiary border-top">
        <div class="container text-center text-muted small">
            <i class="bi bi-shop"></i> PetShop MVC &mdash; <?= date('Y') ?>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    <script src="/assets/js/app.js"></script>
</body>
</html>
