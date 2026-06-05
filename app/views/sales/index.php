<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <h4 class="mb-0"><i class="bi bi-cart-fill"></i> Ventas</h4>
    <a href="<?= BASE_URL ?>/sales/create" class="btn btn-success">
        <i class="bi bi-plus-lg"></i> Nueva Venta
    </a>
</div>

<!-- ─── Filters ──────────────────────────────────────────────── -->
<form method="GET" class="row g-2 mb-3 bg-body-tertiary p-3 rounded-3 border">
    <div class="col-6 col-md-4">
        <label class="form-label small fw-medium">Desde</label>
        <input type="date" name="date_from" class="form-control form-control-sm"
               value="<?= htmlspecialchars($filters['date_from']) ?>">
    </div>
    <div class="col-6 col-md-4">
        <label class="form-label small fw-medium">Hasta</label>
        <input type="date" name="date_to" class="form-control form-control-sm"
               value="<?= htmlspecialchars($filters['date_to']) ?>">
    </div>
    <div class="col-12 col-md-4 d-flex align-items-end gap-2">
        <button type="submit" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-funnel"></i> Filtrar
        </button>
        <?php if (!empty($filters['date_from']) || !empty($filters['date_to'])): ?>
            <a href="<?= BASE_URL ?>/sales" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-x-circle"></i> Limpiar
            </a>
        <?php endif; ?>
    </div>
</form>

<!-- ─── Sales Table ──────────────────────────────────────────── -->
<?php if (empty($sales)): ?>
    <div class="text-center py-5 text-muted">
        <i class="bi bi-receipt fs-1 d-block mb-2"></i>
        <p>No hay ventas registradas.</p>
        <a href="<?= BASE_URL ?>/sales/create" class="btn btn-success btn-sm">
            <i class="bi bi-plus-lg"></i> Registrar primera venta
        </a>
    </div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Fecha</th>
                    <th>Cliente</th>
                    <th class="text-end">Productos</th>
                    <th class="text-end">Final</th>
                    <th class="text-end">Pago</th>
                    <th style="width:80px"></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sales as $s): ?>
                    <tr>
                        <td class="fw-medium"><?= $s['id'] ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($s['created_at'])) ?></td>
                        <td>
                            <?php if (!empty($s['client_name'])): ?>
                                <span class="badge bg-secondary-subtle text-secondary-emphasis">
                                    <?= htmlspecialchars($s['client_name']) ?>
                                </span>
                            <?php else: ?>
                                <span class="text-muted small">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end"><?= $s['item_count'] ?></td>
                        <td class="text-end fw-bold"><?= format_currency($s['final_total']) ?></td>
                        <td class="text-end">
                            <?php if (($s['payment_status'] ?? 'paid') === 'paid'): ?>
                                <span class="badge bg-success">Pagado</span>
                            <?php elseif ($s['payment_status'] === 'pending'): ?>
                                <span class="badge bg-danger">Debe</span>
                            <?php elseif ($s['payment_status'] === 'partial'): ?>
                                <span class="badge bg-warning text-dark">Parcial</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?= BASE_URL ?>/sales/<?= $s['id'] ?>" class="btn btn-sm btn-outline-secondary"
                               title="Ver detalle">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
