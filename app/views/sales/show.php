<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="d-flex align-items-center gap-2 mb-3">
            <a href="/sales" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h4 class="mb-0"><i class="bi bi-receipt"></i> Venta #<?= $sale['id'] ?></h4>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                <span class="fw-medium">Detalle de venta</span>
                <span class="text-muted small">
                    <i class="bi bi-calendar3"></i> <?= date('d/m/Y H:i', strtotime($sale['created_at'])) ?>
                </span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Producto</th>
                                <th class="text-end">Cant.</th>
                                <th class="text-end">Precio U.</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($sale['items'] as $item): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <?php if (!empty($item['product_image'])): ?>
                                                <img src="<?= htmlspecialchars($item['product_image']) ?>"
                                                     class="rounded border" style="width:36px;height:36px;object-fit:cover;">
                                            <?php endif; ?>
                                            <span class="fw-medium"><?= htmlspecialchars($item['product_name']) ?></span>
                                        </div>
                                    </td>
                                    <td class="text-end"><?= $item['quantity'] ?></td>
                                    <td class="text-end"><?= format_currency($item['unit_price']) ?></td>
                                    <td class="text-end fw-medium"><?= format_currency($item['subtotal']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="3" class="text-end">Subtotal:</td>
                                <td class="text-end fw-bold"><?= format_currency($sale['total']) ?></td>
                            </tr>
                            <?php if ($sale['discount_percent'] > 0): ?>
                                <tr>
                                    <td colspan="3" class="text-end text-danger">
                                        Descuento (<?= $sale['discount_percent'] ?>%):
                                    </td>
                                    <td class="text-end text-danger">-<?= format_currency($sale['discount_amount']) ?></td>
                                </tr>
                            <?php endif; ?>
                            <tr>
                                <td colspan="3" class="text-end fw-bold fs-5">Total:</td>
                                <td class="text-end fw-bold fs-5 text-success"><?= format_currency($sale['final_total']) ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-transparent d-flex justify-content-between">
                <span class="text-muted small">
                    <i class="bi bi-box"></i> <?= $sale['item_count'] ?> producto(s)
                </span>
                <button class="btn btn-sm btn-outline-secondary" onclick="window.print()">
                    <i class="bi bi-printer"></i> Imprimir
                </button>
            </div>
        </div>
    </div>
</div>
