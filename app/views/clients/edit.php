<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="d-flex align-items-center gap-2 mb-3">
            <a href="<?= BASE_URL ?>/clients" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h4 class="mb-0"><i class="bi bi-pencil"></i> Editar Cliente</h4>
        </div>

        <form method="POST" action="<?= BASE_URL ?>/clients/update/<?= $client['id'] ?>">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-medium">Nombre <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required
                                   value="<?= htmlspecialchars($client['name']) ?>">
                        </div>

                        <div class="col-6 col-md-4">
                            <label class="form-label fw-medium">Teléfono</label>
                            <input type="text" name="phone" class="form-control"
                                   value="<?= htmlspecialchars($client['phone']) ?>">
                        </div>

                        <div class="col-6 col-md-4">
                            <label class="form-label fw-medium">Email</label>
                            <input type="email" name="email" class="form-control"
                                   value="<?= htmlspecialchars($client['email']) ?>">
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="form-label fw-medium">Dirección</label>
                            <input type="text" name="address" class="form-control"
                                   value="<?= htmlspecialchars($client['address'] ?? '') ?>">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-medium">Notas</label>
                            <textarea name="notes" class="form-control" rows="2"><?= htmlspecialchars($client['notes'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent d-flex justify-content-end gap-2">
                    <a href="<?= BASE_URL ?>/clients" class="btn btn-light">Cancelar</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg"></i> Actualizar Cliente
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
