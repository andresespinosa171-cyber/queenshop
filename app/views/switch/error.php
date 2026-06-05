<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6 text-center">

            <div class="mb-4">
                <i class="bi bi-exclamation-triangle-fill text-warning" style="font-size: 4rem;"></i>
            </div>

            <h4 class="mb-3">No pudimos cambiar de tienda</h4>
            <p class="text-muted mb-4"><?= htmlspecialchars($error) ?></p>

            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h6 class="fw-medium mb-3">Tus tiendas disponibles</h6>
                    <div class="d-flex flex-wrap justify-content-center gap-3">
                        <?php foreach ($availableStores as $s): ?>
                            <a href="<?= BASE_URL ?>/switch-store/<?= $s['id'] ?>"
                               class="btn btn-outline-secondary d-flex flex-column align-items-center gap-2 px-4 py-3"
                               style="min-width: 140px;">
                                <?php $logo = $s['logo'] ?? 'logo.svg'; ?>
                                <img src="<?= BASE_URL ?>/assets/img/<?= htmlspecialchars($logo) ?>"
                                     alt="<?= htmlspecialchars($s['store_name'] ?? $s['name']) ?>"
                                     width="40" height="40">
                                <span class="fw-medium"><?= htmlspecialchars($s['store_name'] ?? $s['name']) ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <a href="<?= BASE_URL ?>" class="btn btn-light mt-4">
                <i class="bi bi-house"></i> Volver al inicio
            </a>

        </div>
    </div>
</div>
