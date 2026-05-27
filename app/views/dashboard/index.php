<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <h4 class="mb-0"><i class="bi bi-grid-1x2-fill"></i> Dashboard</h4>
    <div class="d-flex align-items-center gap-2">
        <?php if ($fortnightOnly): ?>
            <span class="badge bg-warning text-dark px-3 py-2">
                <i class="bi bi-calendar-range"></i> Quincena actual
                <small class="ms-1">(<?= date('d/m', strtotime($currentFortnight['start'])) ?> - <?= date('d/m', strtotime($currentFortnight['end'])) ?>)</small>
            </span>
            <a href="/dashboard?all=1" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-clock-history"></i> Ver todo
            </a>
        <?php else: ?>
            <span class="badge bg-dark text-warning px-3 py-2">
                <i class="bi bi-globe"></i> Todo el tiempo
            </span>
            <a href="/dashboard" class="btn btn-sm btn-outline-warning">
                <i class="bi bi-calendar-range"></i> Solo quincena
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- ─── KPI Cards ────────────────────────────────────────────── -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-warning shadow-sm h-100">
            <div class="card-body text-center">
                <div class="text-warning mb-2"><i class="bi bi-currency-dollar fs-2"></i></div>
                <h6 class="card-title text-muted small text-uppercase">Ventas Totales</h6>
                <p class="fs-4 fw-bold mb-0 text-warning"><?= format_currency($totalSales) ?></p>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-warning shadow-sm h-100">
            <div class="card-body text-center">
                <div class="text-warning mb-2"><i class="bi bi-graph-up-arrow fs-2"></i></div>
                <h6 class="card-title text-muted small text-uppercase">Ganancia Bruta</h6>
                <p class="fs-4 fw-bold mb-0 <?= $grossProfit >= 0 ? 'text-warning' : 'text-danger' ?>">
                    <?= format_currency($grossProfit) ?>
                </p>
                <small class="text-muted">Margen: <?= $profitMargin ?>%</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-warning shadow-sm h-100">
            <div class="card-body text-center">
                <div class="text-warning mb-2"><i class="bi bi-cart-x fs-2"></i></div>
                <h6 class="card-title text-muted small text-uppercase">Costo en Stock</h6>
                <p class="fs-4 fw-bold mb-0"><?= format_currency($stockValue) ?></p>
                <small class="text-muted">Valor actual en inventario</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-warning shadow-sm h-100">
            <div class="card-body text-center">
                <div class="text-warning mb-2"><i class="bi bi-receipt fs-2"></i></div>
                <h6 class="card-title text-muted small text-uppercase">Ventas Hoy</h6>
                <p class="fs-4 fw-bold mb-0 text-warning"><?= format_currency($todaySales) ?></p>
                <small class="text-muted">Ganancia: <?= format_currency($todayProfit) ?></small>
            </div>
        </div>
    </div>
</div>

<!-- ─── Second Row: Summary ──────────────────────────────────── -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-3">
                <div class="text-muted small">Ventas realizadas</div>
                <span class="fs-5 fw-bold"><?= $saleCount ?></span>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-3">
                <div class="text-muted small">Costo total (mercad.)</div>
                <span class="fs-5 fw-bold"><?= format_currency($totalCost) ?></span>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-3">
                <div class="text-muted small">Descuentos otorgados</div>
                <span class="fs-5 fw-bold text-danger"><?= format_currency($totalDiscount) ?></span>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-3">
                <div class="text-muted small">Productos en stock</div>
                <span class="fs-5 fw-bold"><?= count($lowStock) + count($outOfStock) ?> bajo</span>
            </div>
        </div>
    </div>
</div>

<!-- ─── Chart + Low Stock ────────────────────────────────────── -->
<div class="row g-3 mb-4">
    <!-- Sales Chart -->
    <div class="col-12 col-md-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent">
                <span class="fw-medium"><i class="bi bi-bar-chart-line"></i> Ventas (últimos 14 días)</span>
            </div>
            <div class="card-body">
                <canvas id="salesChart" height="200"></canvas>
            </div>
        </div>
    </div>

    <!-- Low Stock -->
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                <span class="fw-medium"><i class="bi bi-exclamation-triangle"></i> Alertas de Stock</span>
                <a href="/products?stock=low" class="btn btn-sm btn-outline-warning">Ver todo</a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($lowStock) && empty($outOfStock)): ?>
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-check-circle fs-3 d-block mb-1 text-success"></i>
                        <small>Todo en orden, stock suficiente</small>
                    </div>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($outOfStock as $p): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-3">
                                <div>
                                    <small class="fw-medium"><?= htmlspecialchars($p['name']) ?></small>
                                    <br><small class="text-danger">Sin stock</small>
                                </div>
                                <a href="/products/edit/<?= $p['id'] ?>"
                                   class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-plus-circle"></i>
                                </a>
                            </li>
                        <?php endforeach; ?>
                        <?php foreach ($lowStock as $p): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-3">
                                <div>
                                    <small class="fw-medium"><?= htmlspecialchars($p['name']) ?></small>
                                    <br><small class="text-warning">Stock: <?= $p['stock'] ?></small>
                                </div>
                                <a href="/products/edit/<?= $p['id'] ?>"
                                   class="btn btn-sm btn-outline-warning">
                                    <i class="bi bi-plus-circle"></i>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- ─── Recent Sales ─────────────────────────────────────────── -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
        <span class="fw-medium"><i class="bi bi-clock-history"></i> Ventas Recientes</span>
        <a href="/sales" class="btn btn-sm btn-outline-secondary">Ver todas</a>
    </div>
    <div class="card-body p-0">
        <?php if (empty($recentSales)): ?>
            <div class="text-center text-muted py-4">
                <i class="bi bi-inbox fs-3 d-block mb-1"></i>
                <small>No hay ventas aún</small>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Fecha</th>
                            <th class="text-end">Total</th>
                            <th class="text-end">Dto.</th>
                            <th class="text-end">Final</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentSales as $s): ?>
                            <tr>
                                <td><?= $s['id'] ?></td>
                                <td><?= date('d/m H:i', strtotime($s['created_at'])) ?></td>
                                <td class="text-end"><?= format_currency($s['total']) ?></td>
                                <td class="text-end text-danger"><?= $s['discount_percent'] ?>%</td>
                                <td class="text-end fw-bold"><?= format_currency($s['final_total']) ?></td>
                                <td><a href="/sales/<?= $s['id'] ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- ─── Chart Data ───────────────────────────────────────────── -->
<script>
    const salesData = <?= json_encode($salesByDay) ?>;
</script>
