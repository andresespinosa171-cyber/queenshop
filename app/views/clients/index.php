<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <h4 class="mb-0"><i class="bi bi-people-fill"></i> Clientes</h4>
    <a href="<?= BASE_URL ?>/clients/create" class="btn btn-success">
        <i class="bi bi-plus-lg"></i> Nuevo Cliente
    </a>
</div>

<!-- ─── Debt Total ──────────────────────────────────────────── -->
<div class="row g-3 mb-4">
    <div class="col-12 col-md-4">
        <div class="card card-hover-lift border-warning shadow-sm">
            <div class="card-body text-center">
                <div class="text-warning mb-1"><i class="bi bi-currency-dollar fs-3"></i></div>
                <h6 class="card-title text-muted small text-uppercase">Total que me deben</h6>
                <p class="fs-4 fw-bold mb-0 text-danger"><?= format_currency($debtTotal) ?></p>
            </div>
        </div>
    </div>
</div>

<!-- ─── Search ──────────────────────────────────────────────── -->
<form method="GET" class="row g-2 mb-3">
    <div class="col-12 col-md-6">
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input type="text" name="search" class="form-control"
                   value="<?= htmlspecialchars($filters['search']) ?>"
                   placeholder="Buscar por nombre o teléfono...">
            <button type="submit" class="btn btn-outline-primary">Buscar</button>
            <?php if (!empty($filters['search'])): ?>
                <a href="<?= BASE_URL ?>/clients" class="btn btn-outline-secondary">
                    <i class="bi bi-x-circle"></i>
                </a>
            <?php endif; ?>
        </div>
    </div>
</form>

<!-- ─── Clients Table ───────────────────────────────────────── -->
<?php if (empty($clients)): ?>
    <div class="text-center py-5 text-muted">
        <i class="bi bi-people fs-1 d-block mb-2"></i>
        <p>No hay clientes todavía.</p>
        <a href="<?= BASE_URL ?>/clients/create" class="btn btn-success btn-sm">
            <i class="bi bi-plus-lg"></i> Crear primer cliente
        </a>
    </div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Nombre</th>
                    <th>Teléfono</th>
                    <th>Email</th>
                    <th class="text-end">Debe</th>
                    <th style="width:120px"></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clients as $c): ?>
                    <tr>
                        <td class="fw-medium">
                            <a href="<?= BASE_URL ?>/clients/<?= $c['id'] ?>" class="text-decoration-none">
                                <?= htmlspecialchars($c['name']) ?>
                            </a>
                        </td>
                        <td><?= htmlspecialchars($c['phone'] ?: '—') ?></td>
                        <td><?= htmlspecialchars($c['email'] ?: '—') ?></td>
                        <td class="text-end <?= (float)$c['total_debt'] > 0 ? 'text-danger fw-bold' : 'text-muted' ?>">
                            <?= format_currency((float)$c['total_debt']) ?>
                        </td>
                        <td>
                            <div class="d-flex gap-1 justify-content-end">
                                <a href="<?= BASE_URL ?>/clients/<?= $c['id'] ?>"
                                   class="btn btn-sm btn-outline-secondary" title="Ver">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="<?= BASE_URL ?>/clients/edit/<?= $c['id'] ?>"
                                   class="btn btn-sm btn-outline-secondary" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="<?= BASE_URL ?>/clients/delete/<?= $c['id'] ?>"
                                      onsubmit="return confirm(<?= htmlspecialchars(json_encode('¿Eliminar a ' . $c['name'] . '?'), ENT_QUOTES) ?>)">
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
