'use strict';

/* ─── Image Preview ──────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', function () {
    const imgInput = document.querySelector('input[type="file"][name="image"]');
    const preview = document.getElementById('imagePreview');
    if (imgInput && preview) {
        imgInput.addEventListener('change', function () {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.querySelector('img').src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                preview.style.display = 'none';
            }
        });
    }
});

/* ─── Restock Modal ───────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', function () {
    const restockModal = document.getElementById('restockModal');
    if (restockModal) {
        restockModal.addEventListener('show.bs.modal', function (event) {
            const btn = event.relatedTarget;
            const id = btn.dataset.id;
            const name = btn.dataset.name;
            this.querySelector('.modal-title').textContent = 'Agregar Stock: ' + name;
            this.querySelector('#restockProductName').textContent = 'Producto: ' + name;
            this.querySelector('form').action = '/products/restock/' + id;
        });
    }
});

/* ─── Sale Cart ────────────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('productSearch');
    const resultsList = document.getElementById('resultsList');
    const searchResults = document.getElementById('searchResults');
    const cartBody = document.getElementById('cartBody');
    const emptyCart = document.getElementById('emptyCart');
    const cartCount = document.getElementById('cartCount');
    const subtotalDisplay = document.getElementById('subtotalDisplay');
    const discountPercent = document.getElementById('discountPercent');
    const discountDisplay = document.getElementById('discountDisplay');
    const finalTotalDisplay = document.getElementById('finalTotalDisplay');
    const completeBtn = document.getElementById('completeSaleBtn');
    const saleForm = document.getElementById('saleForm');

    if (!cartBody) return; // Not on sale create page

    let cart = [];
    let searchTimeout = null;
    let currentCategory = '';

    // ─── Product Search ──────────────────────────────────────────
    searchInput.addEventListener('input', function () {
        clearTimeout(searchTimeout);
        const q = this.value.trim();
        if (q.length < 1) {
            searchResults.style.display = 'none';
            return;
        }
        searchTimeout = setTimeout(function () {
            fetchProducts(q);
        }, 300);
    });

    // Category filter
    document.querySelectorAll('.cat-filter').forEach(function (el) {
        el.addEventListener('click', function (e) {
            e.preventDefault();
            currentCategory = this.dataset.cat;
            const q = searchInput.value.trim();
            if (q.length >= 1 || currentCategory) {
                fetchProducts(q, currentCategory);
            }
    });
});

/* ─── Accounting Chart ───────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', function () {
    const canvas = document.getElementById('accountingChart');
    if (!canvas || typeof accountingLabels === 'undefined') return;

    new Chart(canvas, {
        type: 'bar',
        data: {
            labels: accountingLabels,
            datasets: [{
                label: 'Ventas',
                data: accountingSales,
                backgroundColor: 'rgba(255, 193, 7, 0.7)',
                borderColor: 'rgba(255, 193, 7, 1)',
                borderWidth: 1,
                borderRadius: 4,
                order: 1
            }, {
                label: 'Ganancia',
                data: accountingProfit,
                backgroundColor: 'rgba(25, 135, 84, 0.6)',
                borderColor: 'rgba(25, 135, 84, 1)',
                borderWidth: 1,
                borderRadius: 4,
                order: 2
            }, {
                label: 'Costo',
                data: accountingCosts,
                backgroundColor: 'rgba(220, 53, 69, 0.5)',
                borderColor: 'rgba(220, 53, 69, 1)',
                borderWidth: 1,
                borderRadius: 4,
                order: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false
            },
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        boxWidth: 12,
                        padding: 12,
                        font: { size: 12 }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function (value) {
                            return '$' + value.toFixed(0);
                        }
                    },
                    grid: {
                        color: 'rgba(0,0,0,0.05)'
                    }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
});

    function fetchProducts(q, cat) {
        cat = cat || currentCategory;
        let url = '/api/products?q=' + encodeURIComponent(q);
        if (cat) url += '&category_id=' + encodeURIComponent(cat);

        fetch(url)
            .then(function (r) { return r.json(); })
            .then(function (products) {
                resultsList.innerHTML = '';
                searchResults.style.display = 'block';

                if (products.length === 0) {
                    resultsList.innerHTML = '<div class="list-group-item text-muted text-center">Sin resultados</div>';
                    return;
                }

                products.forEach(function (p) {
                    const inCart = cart.some(function (c) {
                        return c.product_id === p.id;
                    });

                    const div = document.createElement('div');
                    div.className = 'list-group-item list-group-item-action d-flex justify-content-between align-items-center' + (inCart ? ' disabled' : '');
                    div.innerHTML = '<div class="d-flex align-items-center gap-2"><img src="' + (p.image || '/assets/img/no-image.svg') + '" class="rounded border" style="width:36px;height:36px;object-fit:cover;" onerror="this.src=\'/assets/img/no-image.svg\'"><div><strong>' + p.name + '</strong><br><small class="text-muted">$' + parseFloat(p.sale_price).toFixed(2) + ' | Stock: ' + p.stock + '</small></div></div>';

                    if (!inCart) {
                        div.addEventListener('click', function () {
                            addToCart(p);
                            searchResults.style.display = 'none';
                            searchInput.value = '';
                            searchInput.focus();
                        });
                    } else {
                        div.style.cursor = 'not-allowed';
                        div.innerHTML += '<span class="badge bg-secondary">En carrito</span>';
                    }

                    resultsList.appendChild(div);
                });
            });
    }

    // ─── Add to Cart ─────────────────────────────────────────────
    function addToCart(product) {
        if (cart.some(function (c) { return c.product_id === product.id; })) return;
        if (product.stock <= 0) return;

        cart.push({
            product_id: product.id,
            product_name: product.name,
            purchase_price: parseFloat(product.purchase_price) || 0,
            unit_price: parseFloat(product.sale_price) || 0,
            quantity: 1,
            subtotal: parseFloat(product.sale_price) || 0
        });

        renderCart();
    }

    // ─── Render Cart ─────────────────────────────────────────────
    function renderCart() {
        cartBody.innerHTML = '';
        emptyCart.style.display = 'none';

        if (cart.length === 0) {
            emptyCart.style.display = '';
            updateTotals();
            return;
        }

        cart.forEach(function (item, index) {
            const template = document.getElementById('cartRowTemplate');
            const clone = template.content.cloneNode(true);
            const tr = clone.querySelector('tr');

            tr.dataset.productId = item.product_id;
            tr.querySelector('.product-name').textContent = item.product_name;
            tr.querySelector('.product-id-input').value = item.product_id;
            tr.querySelector('.purchase-price-input').value = item.purchase_price;

            const qtyInput = tr.querySelector('.qty-input');
            qtyInput.value = item.quantity;

            const priceInput = tr.querySelector('.price-input');
            priceInput.value = item.unit_price.toFixed(2);

            const subtotalCell = tr.querySelector('.subtotal-cell');
            subtotalCell.textContent = '$' + item.subtotal.toFixed(2);

            // Events
            qtyInput.addEventListener('input', function () {
                let qty = parseInt(this.value) || 1;
                if (qty < 1) qty = 1;
                this.value = qty;
                updateItem(index, qty, parseFloat(priceInput.value) || 0);
            });

            priceInput.addEventListener('input', function () {
                let price = parseFloat(this.value) || 0;
                if (price < 0) price = 0;
                updateItem(index, parseInt(qtyInput.value) || 1, price);
            });

            tr.querySelector('.remove-btn').addEventListener('click', function () {
                cart.splice(index, 1);
                renderCart();
            });

            cartBody.appendChild(clone);
        });

        updateTotals();
    }

    function updateItem(index, qty, price) {
        cart[index].quantity = qty;
        cart[index].unit_price = price;
        cart[index].subtotal = qty * price;
        renderCart();
    }

    // ─── Totals ──────────────────────────────────────────────────
    function updateTotals() {
        let subtotal = 0;
        cart.forEach(function (item) {
            subtotal += item.subtotal;
        });
        subtotal = parseFloat(subtotal.toFixed(2));

        const discPct = parseFloat(discountPercent.value) || 0;
        const discAmount = parseFloat((subtotal * (discPct / 100)).toFixed(2));
        const finalTotal = parseFloat(Math.max(0, subtotal - discAmount).toFixed(2));

        subtotalDisplay.textContent = '$' + subtotal.toFixed(2);
        discountDisplay.textContent = '-$' + discAmount.toFixed(2);
        finalTotalDisplay.textContent = '$' + finalTotal.toFixed(2);

        cartCount.textContent = cart.reduce(function (acc, item) { return acc + item.quantity; }, 0);
        completeBtn.disabled = cart.length === 0 || finalTotal <= 0;
    }

    discountPercent.addEventListener('input', updateTotals);

    // ─── Submit ──────────────────────────────────────────────────
    saleForm.addEventListener('submit', function (e) {
        if (cart.length === 0) {
            e.preventDefault();
            return;
        }

        let subtotal = 0;
        cart.forEach(function (item) {
            subtotal += item.subtotal;
        });
        subtotal = parseFloat(subtotal.toFixed(2));

        const discPct = parseFloat(discountPercent.value) || 0;
        const discAmount = parseFloat((subtotal * (discPct / 100)).toFixed(2));
        const finalTotal = parseFloat(Math.max(0, subtotal - discAmount).toFixed(2));

        document.getElementById('itemsInput').value = JSON.stringify(cart);
        document.getElementById('totalInput').value = subtotal;
        document.getElementById('discountPercentInput').value = discPct;
        document.getElementById('discountAmountInput').value = discAmount;
        document.getElementById('finalTotalInput').value = finalTotal;
    });

    // ─── Keyboard shortcut: Escape closes search ────────────────
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            searchResults.style.display = 'none';
        }
    });

    // Close search results when clicking outside
    document.addEventListener('click', function (e) {
        if (!e.target.closest('#searchResults') && !e.target.closest('#productSearch')) {
            searchResults.style.display = 'none';
        }
    });
});

/* ─── Sales Chart ──────────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', function () {
    const canvas = document.getElementById('salesChart');
    if (!canvas || typeof salesData === 'undefined') return;

    const labels = [];
    const totals = [];
    const costs = [];

    salesData.forEach(function (d) {
        const dateParts = d.day.split('-');
        labels.push(dateParts[2] + '/' + dateParts[1]); // DD/MM
        totals.push(parseFloat(d.total) || 0);
    });

    new Chart(canvas, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Ventas',
                data: totals,
                backgroundColor: 'rgba(25, 135, 84, 0.7)',
                borderColor: 'rgba(25, 135, 84, 1)',
                borderWidth: 1,
                borderRadius: 4,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function (value) {
                            return '$' + value.toFixed(0);
                        }
                    },
                    grid: {
                        color: 'rgba(0,0,0,0.05)'
                    }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
});
