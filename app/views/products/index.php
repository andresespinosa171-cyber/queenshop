<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <h4 class="mb-0"><i class="bi bi-box-seam-fill"></i> Productos</h4>
    <a href="<?= BASE_URL ?>/products/create" class="btn btn-success">
        <i class="bi bi-plus-lg"></i> Nuevo Producto
    </a>
</div>

<!-- ─── Filters ──────────────────────────────────────────────── -->
<form method="GET" class="row g-2 mb-3 bg-body-tertiary p-3 rounded-3 border">
    <div class="col-12 col-md-4">
        <label class="form-label small fw-medium">Buscar</label>
        <div class="input-group input-group-sm">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input type="text" name="search" class="form-control"
                   value="<?= htmlspecialchars($filters['search']) ?>"
                   placeholder="Nombre del producto...">
        </div>
    </div>
    <div class="col-6 col-md-3">
        <label class="form-label small fw-medium">Categoría</label>
        <select name="category_id" class="form-select form-select-sm">
            <option value="">Todas</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>"
                    <?= $filters['category_id'] == $cat['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-6 col-md-2">
        <label class="form-label small fw-medium">Stock</label>
        <select name="stock" class="form-select form-select-sm">
            <option value="">Todos</option>
            <option value="out"  <?= $filters['stock'] === 'out' ? 'selected' : '' ?>>Sin stock</option>
            <option value="low"  <?= $filters['stock'] === 'low' ? 'selected' : '' ?>>Bajo (1-5)</option>
            <option value="medium" <?= $filters['stock'] === 'medium' ? 'selected' : '' ?>>Medio (6-20)</option>
            <option value="high" <?= $filters['stock'] === 'high' ? 'selected' : '' ?>>Alto (20+)</option>
        </select>
    </div>
    <div class="col-6 col-md-2">
        <label class="form-label small fw-medium">Ordenar</label>
        <select name="sort" class="form-select form-select-sm">
            <option value="name"    <?= $filters['sort'] === 'name' ? 'selected' : '' ?>>Nombre</option>
            <option value="stock"   <?= $filters['sort'] === 'stock' ? 'selected' : '' ?>>Stock</option>
            <option value="sale_price"   <?= $filters['sort'] === 'sale_price' ? 'selected' : '' ?>>Precio Venta</option>
            <option value="purchase_price" <?= $filters['sort'] === 'purchase_price' ? 'selected' : '' ?>>Precio Compra</option>
            <option value="created_at"  <?= $filters['sort'] === 'created_at' ? 'selected' : '' ?>>Fecha</option>
        </select>
    </div>
    <div class="col-3 col-md-1">
        <label class="form-label small fw-medium">Orden</label>
        <select name="order" class="form-select form-select-sm">
            <option value="ASC"  <?= $filters['order'] === 'ASC' ? 'selected' : '' ?>>Asc</option>
            <option value="DESC" <?= $filters['order'] === 'DESC' ? 'selected' : '' ?>>Desc</option>
        </select>
    </div>
    <div class="col-3 col-md-1 d-flex align-items-end">
        <button type="submit" class="btn btn-sm btn-outline-primary w-100">
            <i class="bi bi-funnel"></i>
        </button>
    </div>
    <?php if (!empty($filters['search']) || !empty($filters['category_id']) || !empty($filters['stock'])): ?>
        <div class="col-12">
            <a href="<?= BASE_URL ?>/products" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-x-circle"></i> Limpiar filtros
            </a>
        </div>
    <?php endif; ?>
</form>

<!-- ─── Products Table ───────────────────────────────────────── -->
<?php if (empty($products)): ?>
    <div class="text-center py-5 text-muted">
        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
        <p>No hay productos todavía.</p>
        <a href="<?= BASE_URL ?>/products/create" class="btn btn-success btn-sm">
            <i class="bi bi-plus-lg"></i> Crear primer producto
        </a>
    </div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width:60px">Foto</th>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th class="text-end">Compra</th>
                    <th class="text-end">Venta</th>
                    <th class="text-end">Stock</th>
                    <th class="text-end">Ganancia</th>
                    <th style="width:120px"></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $p): ?>
                    <?php
                        $profit = $p['sale_price'] - $p['purchase_price'];
                        $profitClass = $profit > 0 ? 'text-success' : ($profit < 0 ? 'text-danger' : 'text-muted');
                        $stockClass = $p['stock'] == 0 ? 'text-danger fw-bold' : ($p['stock'] <= 5 ? 'text-warning fw-bold' : '');
                    ?>
                    <tr>
                        <td>
                            <img src="<?= image_url($p['image']) ?>"
                                 alt="<?= htmlspecialchars($p['name']) ?>"
                                 class="rounded border" style="width:48px;height:48px;object-fit:cover;">
                        </td>
                        <td class="fw-medium"><?= htmlspecialchars($p['name']) ?></td>
                        <td><span class="badge bg-secondary-subtle text-secondary-emphasis"><?= htmlspecialchars($p['category_name'] ?? 'Sin categoría') ?></span></td>
                        <td class="text-end"><?= format_currency($p['purchase_price']) ?></td>
                        <td class="text-end"><?= format_currency($p['sale_price']) ?></td>
                        <td class="text-end <?= $stockClass ?>"><?= $p['stock'] ?></td>
                        <td class="text-end <?= $profitClass ?>"><?= format_currency($profit) ?></td>
                        <td>
                            <div class="d-flex gap-1 justify-content-end">
                                <a href="<?= BASE_URL ?>/products/edit/<?= $p['id'] ?>"
                                   class="btn btn-sm btn-outline-secondary"
                                   title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button class="btn btn-sm btn-outline-info"
                                        title="Agregar stock"
                                        data-bs-toggle="modal"
                                        data-bs-target="#restockModal"
                                        data-id="<?= $p['id'] ?>"
                                        data-name="<?= htmlspecialchars($p['name']) ?>">
                                    <i class="bi bi-plus-circle"></i>
                                </button>
                                <form method="POST" action="<?= BASE_URL ?>/products/delete/<?= $p['id'] ?>"
                                      onsubmit="return confirm('¿Eliminar <?= htmlspecialchars($p['name'], ENT_QUOTES) ?>?')">
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

<!-- ─── Restock Modal ────────────────────────────────────────── -->
<div class="modal fade" id="restockModal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <form method="POST" class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><i class="bi bi-plus-circle"></i> Agregar Stock</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-2 small text-muted" id="restockProductName"></p>
                <label class="form-label small fw-medium">Cantidad a agregar</label>
                <input type="number" name="quantity" class="form-control" min="1" value="1" required>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-sm w-100">
                    <i class="bi bi-check-lg"></i> Agregar
                </button>
            </div>
        </form>
    </div>
</div>
