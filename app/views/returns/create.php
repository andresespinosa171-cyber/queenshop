<div class="d-flex align-items-center gap-2 mb-3">
    <a href="<?= BASE_URL ?>/returns" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h4 class="mb-0"><i class="bi bi-arrow-return-left"></i> Nueva Devolución</h4>
</div>

<!-- ─── Search Sale ──────────────────────────────────────────── -->
<div class="card shadow-sm border-0 mb-3">
    <div class="card-body">
        <div class="row g-2 align-items-end">
            <div class="col-6 col-md-3">
                <label class="form-label small fw-medium">Buscar por # de venta</label>
                <form method="GET" class="input-group input-group-sm">
                    <input type="number" name="sale_id" class="form-control"
                           value="<?= $saleId ?>" min="1" placeholder="Ej: 1">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-search"></i>
                    </button>
                </form>
            </div>
            <div class="col-6 col-md-4">
                <label class="form-label small fw-medium">O buscar por cliente</label>
                <form method="GET" class="input-group input-group-sm">
                    <input type="text" name="client_search" class="form-control"
                           value="<?= htmlspecialchars($clientSearch) ?>"
                           placeholder="Nombre del cliente...">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-search"></i>
                    </button>
                </form>
            </div>
        </div>

        <?php if (!empty($searchResults)): ?>
            <div class="mt-3">
                <label class="form-label small fw-medium">Ventas encontradas:</label>
                <div class="list-group">
                    <?php foreach ($searchResults as $sr): ?>
                        <a href="<?= BASE_URL ?>/returns/create?sale_id=<?= $sr['id'] ?>"
                           class="list-group-item list-group-item-action d-flex justify-content-between">
                            <span>#<?= $sr['id'] ?> — <?= htmlspecialchars($sr['client_name'] ?? '') ?></span>
                            <span class="text-muted"><?= date('d/m/Y', strtotime($sr['created_at'])) ?> — <?= format_currency($sr['final_total']) ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php elseif ($clientSearch !== '' && empty($searchResults)): ?>
            <div class="text-muted small mt-2">Sin resultados para "<?= htmlspecialchars($clientSearch) ?>"</div>
        <?php endif; ?>
    </div>
</div>

<!-- ─── Sale found — show items to return ────────────────────── -->
<?php if ($saleData): ?>
    <form method="POST" action="<?= BASE_URL ?>/returns/store">
        <input type="hidden" name="sale_id" value="<?= $saleData['id'] ?>">

        <!-- Sale Info -->
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-header bg-transparent d-flex justify-content-between">
                <span class="fw-medium">
                    <i class="bi bi-receipt"></i> Venta #<?= $saleData['id'] ?>
                    <?php if (!empty($saleData['client_id'])): ?>
                        — Cliente: <?= htmlspecialchars($saleData['client_name'] ?? '') ?>
                    <?php endif; ?>
                </span>
                <span class="text-muted small"><?= date('d/m/Y H:i', strtotime($saleData['created_at'])) ?></span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width:60px">Cant.</th>
                                <th>Producto</th>
                                <th class="text-end">Precio U.</th>
                                <th class="text-end">Subtotal</th>
                                <th style="width:100px">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($saleData['items'] as $item): ?>
                                <tr>
                                    <td>
                                        <input type="number" name="qty[<?= $item['product_id'] ?>]"
                                               class="form-control form-control-sm" min="0"
                                               max="<?= $item['quantity'] ?>" value="0"
                                               style="width:60px">
                                        <small class="text-muted d-block">máx: <?= $item['quantity'] ?></small>
                                    </td>
                                    <td>
                                        <span class="fw-medium"><?= htmlspecialchars($item['product_name']) ?></span>
                                    </td>
                                    <td class="text-end"><?= format_currency($item['unit_price']) ?></td>
                                    <td class="text-end"><?= format_currency($item['subtotal']) ?></td>
                                    <td>
                                        <select name="action[<?= $item['product_id'] ?>]" class="form-select form-select-sm">
                                            <option value="restock">Volver a stock</option>
                                            <option value="remove">No restaurar</option>
                                        </select>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Return Details -->
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-medium">Tipo de devolución</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="return_type"
                                       id="rtRefund" value="refund" checked>
                                <label class="form-check-label" for="rtRefund">
                                    <i class="bi bi-cash"></i> Reembolso
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="return_type"
                                       id="rtExchange" value="exchange">
                                <label class="form-check-label" for="rtExchange">
                                    <i class="bi bi-arrow-left-right"></i> Cambio
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-medium">Motivo de la devolución <span class="text-danger">*</span></label>
                        <select name="reason" class="form-select" required>
                            <option value="">Seleccioná un motivo...</option>
                            <option value="talla_incorrecta">Talla incorrecta</option>
                            <option value="no_le_gusto">No le gustó</option>
                            <option value="defectuoso">Producto defectuoso</option>
                            <option value="producto_equivocado">Producto equivocado</option>
                            <option value="garantia">Garantía</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-medium">Descripción detallada</label>
                        <textarea name="reason_detail" class="form-control" rows="2"
                                  placeholder="Explicá con más detalle el motivo..."></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <a href="<?= BASE_URL ?>/returns" class="btn btn-light">Cancelar</a>
            <button type="submit" class="btn btn-warning">
                <i class="bi bi-check-lg"></i> Registrar Devolución
            </button>
        </div>
    </form>
<?php endif; ?>
