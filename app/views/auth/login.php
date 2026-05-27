<div class="row justify-content-center mt-5">
    <div class="col-12 col-sm-10 col-md-6 col-lg-4">
        <div class="card shadow border-warning">
            <div class="card-body p-4 text-center">
                <i class="bi bi-shop fs-1 text-warning"></i>
                <h4 class="fw-bold mt-2">QueenShop</h4>
                <p class="text-muted small">Iniciar sesión</p>

                <form method="POST" action="/login" class="text-start">
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Usuario</label>
                        <input type="text" name="username" class="form-control" placeholder="Tu usuario" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Contraseña</label>
                        <input type="password" name="password" class="form-control" placeholder="••••••" required>
                    </div>
                    <button type="submit" class="btn btn-warning w-100 fw-bold">
                        <i class="bi bi-box-arrow-in-right"></i> Entrar
                    </button>
                </form>

                <hr class="my-3">
                <p class="small text-muted mb-0">
                    ¿No tenés cuenta?
                    <a href="/register" class="fw-medium">Crear una</a>
                </p>
            </div>
        </div>
    </div>
</div>
