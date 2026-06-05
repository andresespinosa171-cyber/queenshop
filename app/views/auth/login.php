<div class="row justify-content-center w-100" style="max-width: 420px;">
    <div class="col-12">
        <div class="card shadow-lg border-warning">
            <div class="card-body p-4 p-md-5 text-center">
                <!-- Logo -->
                <div class="mb-3">
                    <?php $logoPath = current_company_logo(); ?>
                    <?php if (str_starts_with($logoPath, 'http')): ?>
                        <img src="<?= htmlspecialchars($logoPath) ?>" alt="<?= htmlspecialchars(current_store_name()) ?>"
                             width="80" height="80">
                    <?php else: ?>
                        <img src="<?= BASE_URL ?>/assets/img/<?= htmlspecialchars($logoPath) ?>"
                             alt="<?= htmlspecialchars(current_store_name()) ?>"
                             width="80" height="80">
                    <?php endif; ?>
                </div>
                <h4 class="fw-bold mb-1"><?= htmlspecialchars(current_store_name()) ?></h4>
                <p class="text-muted small mb-4">Iniciá sesión para continuar</p>

                <form method="POST" action="<?= BASE_URL ?>/login" class="text-start">
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Usuario</label>
                        <input type="text" name="username" class="form-control form-control-lg" placeholder="Tu usuario" required autofocus>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-medium">Contraseña</label>
                        <input type="password" name="password" class="form-control form-control-lg" placeholder="••••••" required>
                    </div>
                    <button type="submit" class="btn btn-warning btn-lg w-100 fw-bold">
                        <i class="bi bi-box-arrow-in-right"></i> Entrar
                    </button>
                </form>

                <hr class="my-4">
                <p class="small text-muted mb-0">
                    ¿No tenés cuenta?
                    <a href="<?= BASE_URL ?>/register" class="fw-medium text-decoration-none">Crear una</a>
                </p>
            </div>
        </div>
    </div>
</div>
