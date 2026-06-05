<!DOCTYPE html>
<html lang="es" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= htmlspecialchars($store['name'] ?? current_store_name()) ?> — <?= htmlspecialchars($title ?? 'QueenShop') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="icon" type="image/svg+xml" href="<?= BASE_URL ?>/assets/img/<?= htmlspecialchars($store['logo'] ?? 'logo.svg') ?>">
    <link href="<?= BASE_URL ?>/assets/css/style.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100 theme-<?= htmlspecialchars(is_array($store) ? ($store['theme'] ?? 'queenshop') : 'queenshop') ?>">

    <!-- ─── Back to Store Selection ─────────────────────────── -->
    <?php if (is_array($store)): ?>
    <div class="position-absolute top-0 start-0 z-3 p-3">
        <a href="<?= BASE_URL ?>/"
           class="btn btn-sm d-inline-flex align-items-center gap-1 fw-bold shadow-sm"
           style="background: <?= htmlspecialchars($store['primary_color'] ?? '#ffc107') ?>; color: #121212; border-radius: 50px; padding: 8px 20px; font-weight: 700; font-size: 0.9rem; border: 2px solid rgba(255,255,255,0.15);">
            <i class="bi bi-arrow-left" style="font-size: 1.1rem;"></i>
            <span>Volver al menú</span>
        </a>
    </div>
    <?php endif; ?>

    <!-- ─── Flash Messages ─────────────────────────────────── -->
    <div class="container mt-3 position-absolute top-0 start-50 translate-middle-x z-3" style="max-width: 480px;">
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

    <!-- ─── Page Content (vertically centered) ─────────────── -->
    <main class="flex-grow-1 d-flex align-items-center justify-content-center px-3 py-4">
        <?= $content ?>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
