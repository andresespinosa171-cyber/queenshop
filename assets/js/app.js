'use strict';

/* ─── Format helpers (COP: punto miles, coma decimal) ─────────── */
function formatNumber(amount) {
    var parts = Number(amount).toFixed(2).split('.');
    var intPart = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    return intPart + ',' + parts[1];
}

function formatCOP(amount) {
    return '$' + formatNumber(amount);
}

function parseCOP(str) {
    if (!str || str === '') return 0;
    var clean = ('' + str).replace(/\$/g, '').replace(/\./g, '').replace(',', '.');
    return parseFloat(clean) || 0;
}

/* ─── Price Input Mask (formateo en vivo al escribir) ─────────── */
document.addEventListener('input', function (e) {
    if (e.target.matches('.price-mask')) {
        maskPriceInput(e.target);
    }
});

document.addEventListener('blur', function (e) {
    if (e.target.matches('.price-mask')) {
        maskPriceInput(e.target);
    }
}, true);

function maskPriceInput(input) {
    var val = input.value;
    var pos = input.selectionStart;
    var len = val.length;

    // Strip any non-digit, non-comma chars (except $ at the very start)
    val = val.replace(/[^\d,]/g, '');

    // Split on comma
    var parts = val.split(',');
    if (parts.length > 2) return; // multiple commas, abort

    var intPart = parts[0];
    if (intPart.length > 0) {
        // Remove leading zeros (but keep a single zero for "0.xx")
        intPart = intPart.replace(/^0+(?=\d)/, '');
        // Thousands dots
        intPart = intPart.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    var formatted = intPart;
    if (parts.length === 2) {
        formatted += ',' + parts[1].slice(0, 2);
    }

    if (formatted !== input.value) {
        input.value = formatted;
        // Try to keep cursor near where it was
        var delta = input.value.length - len;
        try { input.setSelectionRange(pos + delta, pos + delta); } catch (e) {}
    }
}

/* ─── Clean price-mask values before any form submit ──────────── */
document.addEventListener('submit', function (e) {
    e.target.querySelectorAll('.price-mask').forEach(function (input) {
        input.value = parseCOP(input.value).toFixed(2);
    });
});

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
            this.querySelector('form').action = BASE_URL + '/products/restock/' + id;
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

    function fetchProducts(q, cat) {
        cat = cat || currentCategory;
        let url = BASE_URL + '/api/products?q=' + encodeURIComponent(q);
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

                    const imgSrc = p.image
                        ? BASE_URL + '/' + p.image.replace(/^\//, '')
                        : BASE_URL + '/assets/img/no-image.svg';
                    const noImg = BASE_URL + '/assets/img/no-image.svg';

                    const div = document.createElement('div');
                    div.className = 'list-group-item list-group-item-action d-flex justify-content-between align-items-center' + (inCart ? ' disabled' : '');
                    div.innerHTML = '<div class="d-flex align-items-center gap-2"><img src="' + imgSrc + '" class="rounded border" style="width:36px;height:36px;object-fit:cover;" onerror="this.src=\'' + noImg + '\'"><div><strong>' + p.name + '</strong><br><small class="text-muted">' + formatCOP(p.sale_price) + ' | Stock: ' + p.stock + '</small></div></div>';

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
            priceInput.value = formatNumber(item.unit_price);

            const subtotalCell = tr.querySelector('.subtotal-cell');
            subtotalCell.textContent = formatCOP(item.subtotal);

            // Events
            qtyInput.addEventListener('input', function () {
                let qty = parseInt(this.value) || 1;
                if (qty < 1) qty = 1;
                this.value = qty;
                updateItem(index, qty, parseFloat(priceInput.value) || 0);
            });

            priceInput.addEventListener('input', function () {
                let price = parseCOP(this.value);
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

        const discPct = parseFloat(discountPercent.value) || 0;
        const discAmount = subtotal * (discPct / 100);
        const finalTotal = Math.max(0, subtotal - discAmount);

        subtotalDisplay.textContent = formatCOP(subtotal);
        discountDisplay.textContent = '-' + formatCOP(discAmount);
        finalTotalDisplay.textContent = formatCOP(finalTotal);

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

        const discPct = parseFloat(discountPercent.value) || 0;
        const discAmount = subtotal * (discPct / 100);
        const finalTotal = Math.max(0, subtotal - discAmount);

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
                            return formatCOP(value);
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
                            return formatCOP(value);
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
