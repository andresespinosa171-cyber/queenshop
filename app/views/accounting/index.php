<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <h4 class="mb-0"><i class="bi bi-graph-up"></i> Contabilidad</h4>
    <div class="d-flex align-items-center gap-2">
        <?php if (is_admin()): ?>
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="showAllToggle"
                       <?= $showAll ? 'checked' : '' ?>
                       onchange="window.location.href='?<?= $showAll ? '' : 'all=1' ?>&year=<?= $year ?>'">
                <label class="form-check-label small" for="showAllToggle">
                    <?= $showAll ? 'Todas las empresas' : 'Mi empresa' ?>
                </label>
            </div>
        <?php endif; ?>

        <select class="form-select form-select-sm" style="width:auto" onchange="window.location.href='?year='+this.value<?= $showAll ? '+&all=1' : '' ?>">
            <?php for ($y = (int)date('Y'); $y >= 2024; $y--): ?>
                <option value="<?= $y ?>" <?= $year == $y ? 'selected' : '' ?>><?= $y ?></option>
            <?php endfor; ?>
        </select>
    </div>
</div>

<!-- KPI Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-warning shadow-sm h-100">
            <div class="card-body text-center">
                <div class="text-warning mb-2"><i class="bi bi-currency-dollar fs-2"></i></div>
                <h6 class="card-title text-muted small text-uppercase">Ventas Totales</h6>
                <p class="fs-4 fw-bold mb-0 text-success"><?= format_currency($totalSales) ?></p>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-warning shadow-sm h-100">
            <div class="card-body text-center">
                <div class="text-warning mb-2"><i class="bi bi-graph-up-arrow fs-2"></i></div>
                <h6 class="card-title text-muted small text-uppercase">Ganancia Neta</h6>
                <p class="fs-4 fw-bold mb-0 <?= $totalProfit >= 0 ? 'text-success' : 'text-danger' ?>">
                    <?= format_currency($totalProfit) ?>
                </p>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-warning shadow-sm h-100">
            <div class="card-body text-center">
                <div class="text-warning mb-2"><i class="bi bi-cart-x fs-2"></i></div>
                <h6 class="card-title text-muted small text-uppercase">Costo Total</h6>
                <p class="fs-4 fw-bold mb-0"><?= format_currency($totalCosts) ?></p>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-warning shadow-sm h-100">
            <div class="card-body text-center">
                <div class="text-warning mb-2"><i class="bi bi-receipt fs-2"></i></div>
                <h6 class="card-title text-muted small text-uppercase">Descuentos</h6>
                <p class="fs-4 fw-bold mb-0 text-danger"><?= format_currency($totalDiscounts) ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Bar Chart -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-transparent">
        <span class="fw-medium"><i class="bi bi-bar-chart-fill"></i> Ventas mensuales</span>
    </div>
    <div class="card-body">
        <canvas id="accountingChart" height="280"></canvas>
    </div>
</div>

<!-- Monthly Table -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-transparent">
        <span class="fw-medium"><i class="bi bi-table"></i> Detalle por mes</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Mes</th>
                        <th class="text-end">Ventas</th>
                        <th class="text-end">Costo</th>
                        <th class="text-end">Ganancia</th>
                        <th class="text-end">Dto.</th>
                        <th class="text-end">Ventas</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($stats as $row): ?>
                        <tr>
                            <td class="fw-medium"><?= $row['month_name'] ?></td>
                            <td class="text-end"><?= format_currency((float)$row['total_sales']) ?></td>
                            <td class="text-end"><?= format_currency((float)$row['total_cost']) ?></td>
                            <td class="text-end <?= (float)$row['gross_profit'] >= 0 ? 'text-success' : 'text-danger' ?>">
                                <?= format_currency((float)$row['gross_profit']) ?>
                            </td>
                            <td class="text-end text-danger"><?= format_currency((float)$row['total_discounts']) ?></td>
                            <td class="text-end"><?= (int)$row['sale_count'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Chart Data -->
<script>
    const accountingLabels = <?= json_encode(array_map(fn($r) => substr($r['month_name'], 0, 3), $stats)) ?>;
    const accountingSales = <?= json_encode(array_map(fn($r) => (float)$r['total_sales'], $stats)) ?>;
    const accountingProfit = <?= json_encode(array_map(fn($r) => (float)$r['gross_profit'], $stats)) ?>;
    const accountingCosts = <?= json_encode(array_map(fn($r) => (float)$r['total_cost'], $stats)) ?>;
</script>
