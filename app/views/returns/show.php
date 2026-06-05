<div class="d-flex align-items-center gap-2 mb-3">
    <a href="<?= BASE_URL ?>/returns" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h4 class="mb-0"><i class="bi bi-receipt"></i> Devolución #<?= $return['id'] ?></h4>
</div>

<div class="row g-3 mb-4">
    <div class="col-12 col-md-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <h6 class="card-title text-muted small text-uppercase mb-3">Información</h6>
                <div class="mb-2">
                    <small class="text-muted d-block">Fecha</small>
                    <span><?= date('d/m/Y H:i', strtotime($return['created_at'])) ?></span>
                </div>
                <div class="mb-2">
                    <small class="text-muted d-block">Venta relacionada</small>
                    <a href="<?= BASE_URL ?>/sales/<?= $return['sale_id'] ?>">#<?= $return['sale_id'] ?></a>
                </div>
                <div class="mb-2">
                    <small class="text-muted d-block">Cliente</small>
                    <span><?= htmlspecialchars($return['client_name'] ?? '—') ?></span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card border-warning shadow-sm h-100">
            <div class="card-body text-center d-flex flex-column justify-content-center">
                <h6 class="card-title text-muted small text-uppercase">Tipo</h6>
                <?php if ($return['return_type'] === 'refund'): ?>
                    <p class="fs-5 fw-bold mb-0 text-danger">
                        <i class="bi bi-cash"></i> Reembolso
                    </p>
                <?php else: ?>
                    <p class="fs-5 fw-bold mb-0 text-info">
                        <i class="bi bi-arrow-left-right"></i> Cambio
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body text-center d-flex flex-column justify-content-center">
                <h6 class="card-title text-muted small text-uppercase">Monto devuelto</h6>
                <p class="fs-4 fw-bold mb-0 text-danger"><?= format_currency((float)$return['total_amount']) ?></p>
            </div>
        </div>
    </div>
</div>

<!-- ─── Reason ────────────────────────────────────────────────── -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <h6 class="card-title text-muted small text-uppercase mb-2">Motivo</h6>
        <p class="mb-0"><?= htmlspecialchars($return['reason']) ?></p>
    </div>
</div>

<!-- ─── Items ─────────────────────────────────────────────────── -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-transparent">
        <span class="fw-medium"><i class="bi bi-box-seam"></i> Productos devueltos</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Producto</th>
                        <th class="text-end">Cantidad</th>
                        <th class="text-end">Precio U.</th>
                        <th class="text-end">Subtotal</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($return['items'] as $item): ?>
                        <tr>
                            <td class="fw-medium"><?= htmlspecialchars($item['product_name']) ?></td>
                            <td class="text-end"><?= $item['quantity'] ?></td>
                            <td class="text-end"><?= format_currency($item['unit_price']) ?></td>
                            <td class="text-end"><?= format_currency($item['subtotal']) ?></td>
                            <td>
                                <?php if ($item['action'] === 'restock'): ?>
                                    <span class="badge bg-success">Restockeado</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">No restockeado</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
