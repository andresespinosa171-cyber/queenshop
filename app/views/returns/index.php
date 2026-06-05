<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <h4 class="mb-0"><i class="bi bi-arrow-return-left"></i> Devoluciones</h4>
    <a href="<?= BASE_URL ?>/returns/create" class="btn btn-warning">
        <i class="bi bi-plus-lg"></i> Nueva Devolución
    </a>
</div>

<?php if (empty($returns)): ?>
    <div class="text-center py-5 text-muted">
        <i class="bi bi-arrow-return-left fs-1 d-block mb-2"></i>
        <p>No hay devoluciones registradas.</p>
        <a href="<?= BASE_URL ?>/returns/create" class="btn btn-warning btn-sm">
            <i class="bi bi-plus-lg"></i> Registrar devolución
        </a>
    </div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Fecha</th>
                    <th>Venta #</th>
                    <th>Cliente</th>
                    <th>Tipo</th>
                    <th>Motivo</th>
                    <th class="text-end">Monto</th>
                    <th style="width:80px"></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($returns as $r): ?>
                    <tr>
                        <td class="fw-medium"><?= $r['id'] ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($r['created_at'])) ?></td>
                        <td>
                            <a href="<?= BASE_URL ?>/sales/<?= $r['sale_id'] ?>">#<?= $r['sale_id'] ?></a>
                        </td>
                        <td><?= htmlspecialchars($r['client_name'] ?? '—') ?></td>
                        <td>
                            <?php if ($r['return_type'] === 'refund'): ?>
                                <span class="badge bg-danger">Reembolso</span>
                            <?php else: ?>
                                <span class="badge bg-info">Cambio</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-muted small"><?= htmlspecialchars(truncate($r['reason'], 40)) ?></td>
                        <td class="text-end text-danger"><?= format_currency((float)$r['total_amount']) ?></td>
                        <td>
                            <a href="<?= BASE_URL ?>/returns/<?= $r['id'] ?>" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
