<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="d-flex align-items-center gap-2 mb-3">
            <a href="<?= BASE_URL ?>/products" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h4 class="mb-0"><i class="bi bi-pencil"></i> Editar Producto</h4>
        </div>

        <form method="POST" action="<?= BASE_URL ?>/products/update/<?= $product['id'] ?>" enctype="multipart/form-data">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <!-- Name -->
                    <div class="mb-3">
                        <label class="form-label fw-medium">Nombre del producto <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required
                               value="<?= htmlspecialchars($product['name']) ?>">
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label class="form-label fw-medium">Descripción</label>
                        <textarea name="description" class="form-control" rows="2"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
                    </div>

                    <div class="row g-3">
                        <!-- Purchase Price -->
                        <div class="col-6 col-md-3">
                            <label class="form-label fw-medium">Precio compra</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="text" name="purchase_price" class="form-control price-mask"
                                       value="<?= format_number((float) $product['purchase_price']) ?>" required>
                            </div>
                        </div>

                        <!-- Sale Price -->
                        <div class="col-6 col-md-3">
                            <label class="form-label fw-medium">Precio venta</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="text" name="sale_price" class="form-control price-mask"
                                       value="<?= format_number((float) $product['sale_price']) ?>" required>
                            </div>
                        </div>

                        <!-- Stock -->
                        <div class="col-6 col-md-3">
                            <label class="form-label fw-medium">Stock actual</label>
                            <input type="number" name="stock" class="form-control"
                                   min="0" value="<?= $product['stock'] ?>" required>
                        </div>

                        <!-- Category -->
                        <div class="col-6 col-md-3">
                            <label class="form-label fw-medium">Categoría</label>
                            <select name="category_id" class="form-select">
                                <option value="">Sin categoría</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"
                                        <?= $product['category_id'] == $cat['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Image -->
                    <div class="mt-3">
                        <label class="form-label fw-medium">Imagen del producto</label>
                        <?php if ($product['image']): ?>
                            <div class="mb-2">
                                <img src="<?= image_url($product['image']) ?>"
                                     class="product-img-clickable rounded border" style="max-width:100px;max-height:100px;object-fit:cover;">
                                <div class="form-text">Imagen actual. Subí una nueva para reemplazarla.</div>
                            </div>
                        <?php endif; ?>
                        <input type="file" name="image" class="form-control" accept="image/jpeg,image/png,image/gif,image/webp">
                    </div>
                </div>
                <div class="card-footer bg-transparent d-flex justify-content-end gap-2">
                    <a href="<?= BASE_URL ?>/products" class="btn btn-light">Cancelar</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg"></i> Actualizar Producto
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
