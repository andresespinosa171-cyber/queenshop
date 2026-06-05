<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="fw-bold mb-2" style="color: #ffc107;">Bienvenido a QueenShop</h1>
        <p class="text-muted fs-5">Elegí la tienda que querés visitar</p>
    </div>

    <div class="row justify-content-center g-4">
        <?php foreach ($stores as $store): ?>
            <?php
                $isQueenshop = $store['theme'] === 'queenshop';
                $borderColor = $isQueenshop ? '#ffc107' : '#2563eb';
                $hoverBg = $isQueenshop ? 'rgba(255,193,7,0.08)' : 'rgba(37,99,235,0.08)';
                $storeIcon = $isQueenshop ? 'bi-shop' : 'bi-bag-check';
            ?>
            <div class="col-12 col-sm-6 col-lg-5">
                <a href="<?= BASE_URL ?>/login?store=<?= $store['id'] ?>"
                   class="text-decoration-none"
                   onclick="event.preventDefault(); selectStore(<?= $store['id'] ?>, '<?= htmlspecialchars($store['store_name'] ?? $store['name'], ENT_QUOTES) ?>')">
                    <div class="card border-0 shadow-lg store-card"
                         style="--store-border: <?= $borderColor ?>; --store-hover: <?= $hoverBg ?>; background: #1a1a1a; border-radius: 16px; transition: all 0.3s ease; cursor: pointer; overflow: hidden;">
                        <div class="card-body text-center p-5">
                            <!-- Store Icon -->
                            <div class="mb-4 d-inline-flex align-items-center justify-content-center rounded-circle"
                                 style="width: 96px; height: 96px; background: <?= $borderColor ?>20;">
                                <i class="bi <?= $storeIcon ?> display-3" style="color: <?= $borderColor ?>;"></i>
                            </div>

                            <!-- Store Name -->
                            <h3 class="fw-bold mb-2" style="color: <?= $borderColor ?>;">
                                <?= htmlspecialchars($store['store_name'] ?? $store['name']) ?>
                            </h3>

                            <!-- Description -->
                            <p class="text-muted mb-4">
                                <?= htmlspecialchars($store['description'] ?? ($isQueenshop ? 'Tienda de mascotas' : 'Tienda de zapatos')) ?>
                            </p>

                            <!-- Enter Button -->
                            <span class="btn btn-lg px-5 py-2 fw-bold"
                                  style="background: <?= $borderColor ?>; color: #121212; border-radius: 50px;">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Entrar
                            </span>
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<form id="storeForm" method="POST" action="<?= BASE_URL ?>/login" style="display:none;">
    <input type="hidden" name="store_id" id="selectedStoreId">
    <input type="hidden" name="store_name" id="selectedStoreName">
</form>

<script>
function selectStore(id, name) {
    document.getElementById('selectedStoreId').value = id;
    document.getElementById('selectedStoreName').value = name;
    document.getElementById('storeForm').submit();
}
</script>

<style>
.store-card:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: 0 20px 60px rgba(0,0,0,0.5) !important;
    border: 1px solid var(--store-border) !important;
}
.store-card {
    border: 1px solid transparent !important;
}
</style>
