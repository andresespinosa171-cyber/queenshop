<div class="row justify-content-center w-100" style="max-width: 420px;">
    <div class="col-12">
        <div class="card shadow-lg border-warning">
            <div class="card-body p-4 p-md-5 text-center">
                <!-- Logo -->
                <div class="mb-3">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-warning"
                          style="width: 64px; height: 64px;">
                        <i class="bi bi-shop fs-2 text-dark"></i>
                    </span>
                </div>
                <h4 class="fw-bold mb-1">QueenShop</h4>
                <p class="text-muted small mb-4">Iniciá sesión para continuar</p>

                <form method="POST" action="/login" class="text-start">
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
                    <a href="/register" class="fw-medium text-decoration-none">Crear una</a>
                </p>
            </div>
        </div>
    </div>
</div>
