<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="d-flex align-items-center gap-2 mb-3">
            <a href="<?= BASE_URL ?>/products" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h4 class="mb-0"><i class="bi bi-plus-circle"></i> Nuevo Producto</h4>
        </div>

        <form method="POST" action="<?= BASE_URL ?>/products/store" enctype="multipart/form-data" class="needs-validation" novalidate>
            <div class="card shadow-sm border-0" style="background: #1a1a1a;">
                <div class="card-body">
                    <!-- Name -->
                    <div class="mb-3">
                        <label class="form-label fw-medium text-light">Nombre del producto <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required
                               placeholder="Ej: <?= $isWolfStor ? 'Nike Air Max 90' : 'Royal Canin Perro Adulto 15kg' ?>">
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label class="form-label fw-medium text-light">Descripción</label>
                        <textarea name="description" class="form-control" rows="2"
                                  placeholder="Descripción opcional..."></textarea>
                    </div>

                    <!-- WolfStor shoe-specific fields -->
                    <?php if ($isWolfStor): ?>
                    <div class="row g-3 mb-3">
                        <div class="col-6 col-md-3">
                            <label class="form-label fw-medium text-light">Color</label>
                            <input type="text" name="color" class="form-control" placeholder="Ej: Negro, Blanco, Rojo">
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label fw-medium text-light">Marca</label>
                            <select name="brand" class="form-select">
                                <option value="">Seleccioná...</option>
                                <?php foreach ($brands as $b): ?>
                                    <option value="<?= htmlspecialchars($b) ?>"><?= htmlspecialchars($b) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label fw-medium text-light">Género</label>
                            <div class="d-flex gap-3 mt-1">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="gender" id="genM" value="Hombre">
                                    <label class="form-check-label text-light" for="genM">Hombre</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="gender" id="genF" value="Mujer">
                                    <label class="form-check-label text-light" for="genF">Mujer</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="gender" id="genU" value="Unisex">
                                    <label class="form-check-label text-light" for="genU">Unisex</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label fw-medium text-light">Tipo</label>
                            <div class="d-flex gap-3 mt-1">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="boot_type" id="btBota" value="Bota">
                                    <label class="form-check-label text-light" for="btBota">Bota</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="boot_type" id="btNormal" value="Normal" checked>
                                    <label class="form-check-label text-light" for="btNormal">Normal</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="row g-3">
                        <!-- Purchase Price -->
                        <div class="col-6 col-md-3">
                            <label class="form-label fw-medium text-light">Precio compra</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="text" name="purchase_price" class="form-control price-mask"
                                       value="0,00" required>
                            </div>
                        </div>

                        <!-- Sale Price -->
                        <div class="col-6 col-md-3">
                            <label class="form-label fw-medium text-light">Precio venta</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="text" name="sale_price" class="form-control price-mask"
                                       value="0,00" required>
                            </div>
                        </div>

                        <!-- Stock -->
                        <div class="col-6 col-md-3">
                            <label class="form-label fw-medium text-light">Stock inicial</label>
                            <input type="number" name="stock" class="form-control"
                                   min="0" value="0" required>
                        </div>

                        <!-- Category -->
                        <div class="col-6 col-md-3">
                            <label class="form-label fw-medium text-light">Categoría</label>
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
                        <label class="form-label fw-medium text-light">Imagen del producto</label>
                        <input type="file" name="image" class="form-control" accept="image/jpeg,image/png,image/gif,image/webp">
                        <div class="form-text">JPG, PNG, GIF o WebP. Máximo 2MB.</div>
                    </div>

                    <div class="mt-2" id="imagePreview" style="display:none">
                        <img src="" class="rounded border" style="max-width:120px;max-height:120px;object-fit:cover;">
                    </div>
                </div>
                <div class="card-footer bg-transparent d-flex justify-content-end gap-2" style="border-top-color: #333;">
                    <a href="<?= BASE_URL ?>/products" class="btn btn-light">Cancelar</a>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-lg"></i> Guardar Producto
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
