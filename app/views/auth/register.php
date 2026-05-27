<div class="row justify-content-center mt-5">
    <div class="col-12 col-sm-10 col-md-6 col-lg-4">
        <div class="card shadow border-warning">
            <div class="card-body p-4 text-center">
                <i class="bi bi-shop fs-1 text-warning"></i>
                <h4 class="fw-bold mt-2">QueenShop</h4>
                <p class="text-muted small">Crear nueva cuenta</p>

                <form method="POST" action="/register" class="text-start">
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Usuario</label>
                        <input type="text" name="username" class="form-control" placeholder="Ej: miempresa" required minlength="3" maxlength="50" autofocus>
                        <div class="form-text">Mínimo 3 caracteres, solo letras y números.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Contraseña</label>
                        <input type="password" name="password" class="form-control" placeholder="Mínimo 6 caracteres" required minlength="6">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Repetir contraseña</label>
                        <input type="password" name="confirm_password" class="form-control" placeholder="Confirmar" required minlength="6">
                    </div>
                    <button type="submit" class="btn btn-warning w-100 fw-bold">
                        <i class="bi bi-person-plus"></i> Crear cuenta
                    </button>
                </form>

                <hr class="my-3">
                <p class="small text-muted mb-0">
                    ¿Ya tenés cuenta?
                    <a href="/login" class="fw-medium">Iniciar sesión</a>
                </p>
            </div>
        </div>
    </div>
</div>
