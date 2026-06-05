<div class="row justify-content-center w-100" style="max-width: 420px;">
    <div class="col-12">
        <div class="card shadow-lg" style="border-color: <?= htmlspecialchars($store['primary_color'] ?? '#ffc107') ?>;">
            <div class="card-body p-4 p-md-5 text-center">
                <!-- Store Icon -->
                <div class="mb-3">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle"
                          style="width: 64px; height: 64px; background: <?= htmlspecialchars($store['primary_color'] ?? '#ffc107') ?>20;">
                        <i class="bi bi-person-plus fs-2" style="color: <?= htmlspecialchars($store['primary_color'] ?? '#ffc107') ?>;"></i>
                    </span>
                </div>
                <h4 class="fw-bold mb-1" style="color: <?= htmlspecialchars($store['primary_color'] ?? '#ffc107') ?>;">
                    <?= htmlspecialchars($store['name']) ?>
                </h4>
                <p class="text-muted small mb-4">Crear nueva cuenta</p>

                <form method="POST" action="<?= BASE_URL ?>/register" class="text-start">
                    <input type="hidden" name="store_id" value="<?= $store['company_id'] ?>">

                    <div class="mb-3">
                        <label class="form-label small fw-medium">Usuario</label>
                        <input type="text" name="username" class="form-control form-control-lg" placeholder="Ej: miempresa" required minlength="3" maxlength="50" autofocus>
                        <div class="form-text">Mínimo 3 caracteres, solo letras y números.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Contraseña</label>
                        <input type="password" name="password" class="form-control form-control-lg" placeholder="Mínimo 6 caracteres" required minlength="6">
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-medium">Repetir contraseña</label>
                        <input type="password" name="confirm_password" class="form-control form-control-lg" placeholder="Confirmar" required minlength="6">
                    </div>
                    <button type="submit" class="btn btn-lg w-100 fw-bold"
                            style="background: <?= htmlspecialchars($store['primary_color'] ?? '#ffc107') ?>; color: #121212;">
                        <i class="bi bi-person-plus"></i> Crear cuenta
                    </button>
                </form>

                <hr class="my-4">
                <p class="small text-muted mb-0">
                    ¿Ya tenés cuenta?
                    <a href="<?= BASE_URL ?>/login?store=<?= $store['company_id'] ?>"
                       class="fw-medium text-decoration-none"
                       style="color: <?= htmlspecialchars($store['primary_color'] ?? '#ffc107') ?>;">
                        Iniciar sesión
                    </a>
                </p>

                <div class="mt-3">
                    <a href="<?= BASE_URL ?>/" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> Elegir otra tienda
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
