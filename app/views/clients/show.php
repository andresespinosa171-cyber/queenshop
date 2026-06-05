<div class="d-flex align-items-center gap-2 mb-3">
    <a href="<?= BASE_URL ?>/clients" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h4 class="mb-0"><i class="bi bi-person-fill"></i> <?= htmlspecialchars($client['name']) ?></h4>
    <a href="<?= BASE_URL ?>/clients/edit/<?= $client['id'] ?>" class="btn btn-sm btn-outline-secondary ms-auto">
        <i class="bi bi-pencil"></i> Editar
    </a>
</div>

<div class="row g-3 mb-4">
    <!-- Info -->
    <div class="col-12 col-md-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <h6 class="card-title text-muted small text-uppercase mb-3">Información</h6>
                <div class="mb-2">
                    <small class="text-muted d-block">Teléfono</small>
                    <span><?= htmlspecialchars($client['phone'] ?: '—') ?></span>
                </div>
                <div class="mb-2">
                    <small class="text-muted d-block">Email</small>
                    <span><?= htmlspecialchars($client['email'] ?: '—') ?></span>
                </div>
                <div class="mb-2">
                    <small class="text-muted d-block">Dirección</small>
                    <span><?= htmlspecialchars($client['address'] ?? '—') ?></span>
                </div>
                <?php if (!empty($client['notes'])): ?>
                    <div>
                        <small class="text-muted d-block">Notas</small>
                        <span><?= htmlspecialchars($client['notes']) ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Debt Card -->
    <div class="col-12 col-md-6">
        <div class="card border-warning shadow-sm h-100">
            <div class="card-body text-center d-flex flex-column justify-content-center">
                <div class="text-warning mb-2"><i class="bi bi-currency-dollar fs-2"></i></div>
                <h6 class="card-title text-muted small text-uppercase">Deuda actual</h6>
                <p class="fs-2 fw-bold mb-0 <?= (float)$client['total_debt'] > 0 ? 'text-danger' : 'text-success' ?>">
                    <?= format_currency((float)$client['total_debt']) ?>
                </p>
                <?php if ((float)$client['total_debt'] > 0): ?>
                    <small class="text-muted mt-2">Pendiente de pago</small>
                <?php else: ?>
                    <small class="text-muted mt-2">Sin deudas</small>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- ─── Quick Actions ─────────────────────────────────────────── -->
<div class="row g-3 mb-4">
    <div class="col-12 col-md-6">
        <a href="<?= BASE_URL ?>/sales/create?client_id=<?= $client['id'] ?>"
           class="btn btn-success w-100 py-3">
            <i class="bi bi-cart-plus fs-5"></i><br>
            Nueva venta a este cliente
        </a>
    </div>
    <div class="col-12 col-md-6">
        <?php if ((float)$client['total_debt'] > 0): ?>
            <button class="btn btn-warning w-100 py-3" data-bs-toggle="modal" data-bs-target="#paymentModal">
                <i class="bi bi-cash fs-5"></i><br>
                Registrar abono
            </button>
        <?php else: ?>
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center text-muted">
                    <i class="bi bi-check-circle fs-3 d-block mb-1 text-success"></i>
                    <small>Cliente sin deudas</small>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- ─── Payment History (abonos + ajustes) ─────────────────────── -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
        <span class="fw-medium"><i class="bi bi-clock-history"></i> Historial de movimientos</span>
    </div>
    <div class="card-body p-0">
        <?php if (empty($payments)): ?>
            <div class="text-center text-muted py-4">
                <i class="bi bi-inbox fs-3 d-block mb-1"></i>
                <small>Sin movimientos registrados</small>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Fecha</th>
                            <th class="text-end">Monto</th>
                            <th>Tipo</th>
                            <th>Notas</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($payments as $p): ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($p['payment_date'])) ?></td>
                                <td class="text-end fw-bold <?= (float)$p['amount'] >= 0 ? 'text-success' : 'text-danger' ?>">
                                    <?= format_currency((float)$p['amount']) ?>
                                </td>
                                <td>
                                    <?php if (($p['type'] ?? '') === 'adjustment'): ?>
                                        <span class="badge bg-secondary">Ajuste</span>
                                    <?php else: ?>
                                        <span class="badge bg-info">Abono</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-muted small"><?= htmlspecialchars($p['notes'] ?? '') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- ─── Payment Modal ─────────────────────────────────────────── -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <form method="POST" action="<?= BASE_URL ?>/clients/pay/<?= $client['id'] ?>" class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><i class="bi bi-cash"></i> Registrar abono</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="small text-muted">
                    Cliente: <strong><?= htmlspecialchars($client['name']) ?></strong><br>
                    Deuda actual: <strong class="text-danger"><?= format_currency((float)$client['total_debt']) ?></strong>
                </p>
                <div class="mb-2">
                    <label class="form-label small fw-medium">Monto del abono</label>
                    <input type="number" name="amount" class="form-control" min="1"
                           step="0.01" required
                           max="<?= (float)$client['total_debt'] ?>">
                </div>
                <div>
                    <label class="form-label small fw-medium">Notas (opcional)</label>
                    <input type="text" name="notes" class="form-control"
                           placeholder="Ej: Abono en efectivo">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success w-100">
                    <i class="bi bi-check-lg"></i> Registrar abono
                </button>
            </div>
        </form>
    </div>
</div>
