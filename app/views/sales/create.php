<div class="row justify-content-center">
    <div class="col-12 col-lg-10">
        <div class="d-flex align-items-center gap-2 mb-3">
            <a href="<?= BASE_URL ?>/sales" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h4 class="mb-0"><i class="bi bi-cart-plus"></i> Nueva Venta</h4>
        </div>

        <form method="POST" action="<?= BASE_URL ?>/sales/store" id="saleForm">
            <input type="hidden" name="items" id="itemsInput">
            <input type="hidden" name="total" id="totalInput">
            <input type="hidden" name="discount_percent" id="discountPercentInput">
            <input type="hidden" name="discount_amount" id="discountAmountInput">
            <input type="hidden" name="final_total" id="finalTotalInput">

            <div class="row g-3">
                <!-- ─── Product Search ─────────────────────────── -->
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <label class="form-label fw-medium">
                                <i class="bi bi-search"></i> Buscar productos
                            </label>
                            <div class="input-group">
                                <input type="text" id="productSearch" class="form-control"
                                       placeholder="Escribí el nombre del producto..."
                                       autocomplete="off">
                                <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-funnel"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end p-2" style="min-width:200px;">
                                    <li><h6 class="dropdown-header">Filtrar por categoría</h6></li>
                                    <li><a class="dropdown-item cat-filter" href="#" data-cat="">Todas</a></li>
                                    <?php foreach ($categories as $cat): ?>
                                        <li>
                                            <a class="dropdown-item cat-filter" href="#"
                                               data-cat="<?= $cat['id'] ?>">
                                                <?= htmlspecialchars($cat['name']) ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <div id="searchResults" class="mt-2" style="display:none;">
                                <div class="list-group" id="resultsList"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ─── Cart ───────────────────────────────────── -->
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                            <span class="fw-medium"><i class="bi bi-cart"></i> Carrito</span>
                            <span class="badge bg-primary rounded-pill" id="cartCount">0</span>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0" id="cartTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Producto</th>
                                            <th style="width:80px">Cant.</th>
                                            <th style="width:120px">Precio U.</th>
                                            <th style="width:100px">Subtotal</th>
                                            <th style="width:50px"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="cartBody">
                                        <tr id="emptyCart">
                                            <td colspan="5" class="text-center text-muted py-4">
                                                <i class="bi bi-basket fs-3 d-block mb-1"></i>
                                                Buscá y seleccioná productos para agregar al carrito
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ─── Totals ─────────────────────────────────── -->
                <div class="col-12 col-md-6 ms-auto">
                    <div class="card shadow-sm border-0 bg-body-tertiary">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <span class="fw-bold" id="subtotalDisplay">$0.00</span>
                            </div>
                            <div class="row g-2 mb-2">
                                <div class="col-8">
                                    <label class="form-label small">Descuento (%)</label>
                                </div>
                                <div class="col-4">
                                    <input type="number" id="discountPercent" class="form-control form-control-sm"
                                           min="0" max="100" value="0" step="1">
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mb-2 text-danger">
                                <span>Descuento:</span>
                                <span id="discountDisplay">-$0.00</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between fs-5">
                                <span class="fw-bold">Total:</span>
                                <span class="fw-bold text-success" id="finalTotalDisplay">$0.00</span>
                            </div>
                            <button type="submit" class="btn btn-success w-100 mt-3" id="completeSaleBtn" disabled>
                                <i class="bi bi-check2-circle"></i> Completar Venta
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- ─── Product Row Template (hidden) ─────────────────────────── -->
<template id="cartRowTemplate">
    <tr data-product-id="">
        <td>
            <span class="fw-medium product-name"></span>
            <input type="hidden" class="product-id-input">
            <input type="hidden" class="purchase-price-input">
        </td>
        <td>
            <input type="number" class="form-control form-control-sm qty-input" value="1" min="1" step="1">
        </td>
        <td>
            <div class="input-group input-group-sm">
                <span class="input-group-text">$</span>
                <input type="number" class="form-control form-control-sm price-input" min="0" step="0.01">
            </div>
        </td>
        <td class="text-end fw-medium subtotal-cell">$0.00</td>
        <td>
            <button type="button" class="btn btn-sm btn-outline-danger remove-btn">
                <i class="bi bi-x"></i>
            </button>
        </td>
    </tr>
</template>
