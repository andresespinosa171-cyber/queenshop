<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="d-flex align-items-center gap-2 mb-3">
            <a href="<?= BASE_URL ?>/products" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h4 class="mb-0"><i class="bi bi-plus-circle"></i> Nuevo Producto</h4>
        </div>

        <form method="POST" action="<?= BASE_URL ?>/products/store" enctype="multipart/form-data" class="needs-validation" novalidate>
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <!-- Name -->
                    <div class="mb-3">
                        <label class="form-label fw-medium">Nombre del producto <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required
                               placeholder="Ej: Royal Canin Perro Adulto 15kg">
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label class="form-label fw-medium">Descripción</label>
                        <textarea name="description" class="form-control" rows="2"
                                  placeholder="Descripción opcional..."></textarea>
                    </div>

                    <div class="row g-3">
                        <!-- Purchase Price -->
                        <div class="col-6 col-md-3">
                            <label class="form-label fw-medium">Precio compra</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" name="purchase_price" class="form-control"
                                       step="0.01" min="0" value="0.00" required>
                            </div>
                        </div>

                        <!-- Sale Price -->
                        <div class="col-6 col-md-3">
                            <label class="form-label fw-medium">Precio venta</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" name="sale_price" class="form-control"
                                       step="0.01" min="0" value="0.00" required>
                            </div>
                        </div>

                        <!-- Stock -->
                        <div class="col-6 col-md-3">
                            <label class="form-label fw-medium">Stock inicial</label>
                            <input type="number" name="stock" class="form-control"
                                   min="0" value="0" required>
                        </div>

                        <!-- Category -->
                        <div class="col-6 col-md-3">
                            <label class="form-label fw-medium">Categoría</label>
                            <select name="category_id" class="form-select">
                                <option value="">Sin categoría</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Image -->
                    <div class="mt-3">
                        <label class="form-label fw-medium">Imagen del producto</label>
                        <input type="file" name="image" class="form-control" accept="image/jpeg,image/png,image/gif,image/webp">
                        <div class="form-text">JPG, PNG, GIF o WebP. Máximo 2MB.</div>
                    </div>

                    <!-- Preview -->
                    <div class="mt-2" id="imagePreview" style="display:none">
                        <img src="" class="rounded border" style="max-width:120px;max-height:120px;object-fit:cover;">
                    </div>
                </div>
                <div class="card-footer bg-transparent d-flex justify-content-end gap-2">
                    <a href="/products" class="btn btn-light">Cancelar</a>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-lg"></i> Guardar Producto
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
