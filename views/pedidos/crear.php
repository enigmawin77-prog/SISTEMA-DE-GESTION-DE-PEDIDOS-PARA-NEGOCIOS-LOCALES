<?php
$pageTitle = "Nuevo Pedido";
require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../layouts/sidebar.php';

use App\Helpers\FormatHelper;
use App\Helpers\FlashHelper;
use App\Helpers\CsrfHelper;
?>

<main class="app-main">
    <?php require __DIR__ . '/../layouts/navbar.php'; ?>

    <div class="app-content">
        <?= FlashHelper::render() ?>

        <!-- Encabezado -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
            <div>
                <a href="index.php?route=pedidos" class="text-secondary small fw-bold text-decoration-none">
                    <i class="bi bi-arrow-left"></i> Volver a Pedidos
                </a>
                <h2 class="h4 fw-bold text-dark mb-0 mt-1">Registrar Nuevo Pedido</h2>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-primary fs-6 px-3 py-2 rounded-pill shadow-sm">
                    <i class="bi bi-hash"></i> <?= htmlspecialchars($proximoNumero) ?>
                </span>
            </div>
        </div>

        <form id="orderForm" action="index.php?route=pedidos_store" method="POST">
            <?= CsrfHelper::field() ?>
            <input type="hidden" name="items_json" id="itemsJsonInput" value="[]">

            <div class="row g-4">
                <!-- ============================================================
                     COLUMNA IZQUIERDA: CATÁLOGO DE PRODUCTOS (POS)
                ============================================================ -->
                <div class="col-12 col-xl-7">
                    <div class="card-enterprise h-100 mb-0">
                        <div class="card-header-clean flex-wrap gap-2">
                            <div class="card-title-clean">
                                <i class="bi bi-grid-3x3-gap-fill text-primary"></i> Catálogo de Productos
                            </div>
                            <!-- Buscador en tiempo real de productos -->
                            <div style="min-width: 220px;">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-search"></i></span>
                                    <input type="text" id="productSearchFilter" class="form-control border-start-0 ps-0" placeholder="Buscar en catálogo...">
                                </div>
                            </div>
                        </div>

                        <!-- Filtros por categoría -->
                        <div class="px-3 pt-3 pb-2 border-bottom bg-light">
                            <div class="d-flex gap-1.5 overflow-auto pb-1" id="categoryTabs">
                                <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 py-1 cat-filter-btn active" data-cat="all">
                                    Todos
                                </button>
                                <?php foreach ($categorias as $cat): ?>
                                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 cat-filter-btn" data-cat="<?= $cat['id'] ?>">
                                        <?= htmlspecialchars($cat['nombre']) ?>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Grid de productos -->
                        <div class="card-body-clean" style="max-height: 640px; overflow-y: auto;">
                            <div class="row g-3" id="productCatalogGrid">
                                <?php foreach ($productos as $prod): ?>
                                    <?php 
                                        $pImg = 'public/uploads/productos/' . ($prod['imagen'] ?: 'burger.jpg');
                                        if (!file_exists($pImg)) $pImg = 'public/uploads/productos/burger.jpg';
                                        $isAvailable = $prod['stock'] > 0;
                                    ?>
                                    <div class="col-12 col-sm-6 col-md-4 product-card-item"
                                         data-id="<?= $prod['id'] ?>"
                                         data-cat="<?= $prod['categoria_id'] ?>"
                                         data-name="<?= strtolower(htmlspecialchars($prod['nombre'])) ?>"
                                         data-price="<?= $prod['precio_venta'] ?>"
                                         data-stock="<?= $prod['stock'] ?>">
                                        <div class="card h-100 border rounded-3 p-2.5 shadow-none transition-hover">
                                            <div class="position-relative mb-2">
                                                <img src="<?= $pImg ?>" class="w-100 rounded-3 object-fit-cover" style="height: 120px;" alt="<?= htmlspecialchars($prod['nombre']) ?>" onerror="this.src='public/uploads/productos/burger.jpg'">
                                                <span class="position-absolute top-0 end-0 m-2 badge <?= $isAvailable ? ($prod['stock'] <= $prod['stock_minimo'] ? 'bg-warning text-dark' : 'bg-dark text-white') : 'bg-danger text-white' ?> rounded-pill" style="font-size: 11px;">
                                                    Stock: <?= $prod['stock'] ?>
                                                </span>
                                            </div>
                                            <div class="d-flex flex-column justify-content-between flex-grow-1">
                                                <div>
                                                    <span class="badge bg-light text-muted border mb-1" style="font-size: 10px;"><?= htmlspecialchars($prod['categoria_nombre']) ?></span>
                                                    <h6 class="fw-bold text-dark mb-1" style="font-size: 13.5px;"><?= htmlspecialchars($prod['nombre']) ?></h6>
                                                </div>
                                                <div class="d-flex align-items-center justify-content-between mt-2 pt-1 border-top">
                                                    <div class="fw-bold text-primary fs-6"><?= FormatHelper::money($prod['precio_venta']) ?></div>
                                                    <button type="button"
                                                            class="btn btn-sm btn-primary rounded-pill px-2.5 py-1 btn-add-to-cart"
                                                            data-id="<?= $prod['id'] ?>"
                                                            data-name="<?= htmlspecialchars($prod['nombre']) ?>"
                                                            data-price="<?= $prod['precio_venta'] ?>"
                                                            data-stock="<?= $prod['stock'] ?>"
                                                            data-image="<?= $pImg ?>"
                                                            <?= !$isAvailable ? 'disabled' : '' ?>>
                                                        <i class="bi bi-plus-lg"></i> Agregar
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ============================================================
                     COLUMNA DERECHA: RESUMEN DE LA ORDEN Y CHECKOUT
                ============================================================ -->
                <div class="col-12 col-xl-5">
                    <div class="card-enterprise">
                        <div class="card-header-clean bg-primary text-white">
                            <div class="card-title-clean text-white">
                                <i class="bi bi-cart-check-fill me-1"></i> Resumen de la Orden
                            </div>
                            <span class="badge bg-white text-primary rounded-pill fw-bold" id="cartItemCountBadge">0 items</span>
                        </div>

                        <div class="card-body-clean">
                            <!-- 1. Selección de Cliente -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-label small fw-bold text-dark mb-0">Cliente <span class="text-danger">*</span></label>
                                    <button type="button" class="btn btn-link btn-sm text-primary p-0 text-decoration-none fw-bold" data-bs-toggle="modal" data-bs-target="#modalNuevoCliente">
                                        <i class="bi bi-person-plus"></i> + Nuevo Cliente
                                    </button>
                                </div>
                                <select name="cliente_id" id="clientSelect" class="form-select form-select-sm" required>
                                    <option value="">Seleccione un cliente...</option>
                                    <?php foreach ($clientes as $cli): ?>
                                        <option value="<?= $cli['id'] ?>" data-direccion="<?= htmlspecialchars($cli['direccion'] ?? '') ?>" data-telefono="<?= htmlspecialchars($cli['telefono'] ?? '') ?>">
                                            <?= htmlspecialchars($cli['nombre']) ?> <?= $cli['numero_documento'] ? "({$cli['numero_documento']})" : '' ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- 2. Modalidad de Entrega -->
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-dark mb-1">Modalidad de Atención</label>
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" name="modalidad" id="modDomicilio" value="domicilio" checked autocomplete="off">
                                    <label class="btn btn-outline-primary btn-sm py-2" for="modDomicilio">
                                        <i class="bi bi-bicycle me-1"></i> Domicilio (+S/ 5.00)
                                    </label>

                                    <input type="radio" class="btn-check" name="modalidad" id="modRecojo" value="recojo" autocomplete="off">
                                    <label class="btn btn-outline-secondary btn-sm py-2" for="modRecojo">
                                        <i class="bi bi-shop me-1"></i> Recojo en tienda
                                    </label>
                                </div>
                            </div>

                            <!-- Campo de dirección para Domicilio -->
                            <div id="deliveryAddressSection" class="mb-3">
                                <label class="form-label small fw-bold text-dark mb-1">Dirección de Entrega</label>
                                <input type="text" name="direccion_entrega" id="deliveryAddressInput" class="form-control form-control-sm mb-1.5" placeholder="Ej: Calle Los Fresnos 240, Dpto 302, San Isidro">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <input type="text" name="entrega_referencia" class="form-control form-control-sm" placeholder="Referencia de llegada">
                                    </div>
                                    <div class="col-6">
                                        <input type="text" name="repartidor_nombre" class="form-control form-control-sm" placeholder="Repartidor (opcional)">
                                    </div>
                                </div>
                            </div>

                            <!-- 3. Lista de Productos en la Canasta -->
                            <div class="border rounded-3 p-2 mb-3 bg-light" style="min-height: 180px; max-height: 260px; overflow-y: auto;">
                                <div id="cartEmptyPlaceholder" class="text-center py-4 text-muted">
                                    <i class="bi bi-bag-plus fs-2 d-block mb-1 text-secondary opacity-50"></i>
                                    <span class="small">La orden está vacía.<br>Agregue productos del catálogo de la izquierda.</span>
                                </div>
                                <div id="cartItemsList" class="d-flex flex-column gap-2" style="display: none !important;">
                                    <!-- Items generados dinámicamente con JavaScript -->
                                </div>
                            </div>

                            <!-- 4. Cálculos: Subtotal, Descuento, Envío, Total -->
                            <div class="bg-white border rounded-3 p-3 mb-3">
                                <div class="d-flex justify-content-between mb-1.5 text-secondary small">
                                    <span>Subtotal productos:</span>
                                    <span class="fw-bold text-dark" id="txtSubtotal">S/ 0.00</span>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-1.5 small text-secondary">
                                    <span>Descuento autorizado:</span>
                                    <div class="input-group input-group-sm" style="width: 110px;">
                                        <span class="input-group-text py-0 px-1.5 bg-light">S/</span>
                                        <input type="number" step="0.50" min="0" name="descuento" id="inputDescuento" class="form-control form-control-sm text-end py-0" value="0.00">
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-2 small text-secondary">
                                    <span>Costo de envío:</span>
                                    <div class="input-group input-group-sm" style="width: 110px;">
                                        <span class="input-group-text py-0 px-1.5 bg-light">S/</span>
                                        <input type="number" step="0.50" min="0" name="costo_envio" id="inputCostoEnvio" class="form-control form-control-sm text-end py-0" value="5.00">
                                    </div>
                                </div>

                                <hr class="my-2">

                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-bold fs-6 text-dark">TOTAL A COBRAR:</span>
                                    <span class="fw-bold fs-5 text-primary" id="txtTotal">S/ 0.00</span>
                                </div>
                            </div>

                            <!-- 5. Pago Inicial Opcional -->
                            <div class="mb-3">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="chkPagoInicial" checked>
                                    <label class="form-check-label small fw-bold text-dark" for="chkPagoInicial">Registrar pago inmediato</label>
                                </div>
                                <div id="pagoInicialSection" class="p-2.5 border rounded-3 bg-light">
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <label class="form-label small fw-bold text-secondary mb-1">Método de Pago</label>
                                            <select name="pago_metodo" class="form-select form-select-sm">
                                                <option value="efectivo">Efectivo</option>
                                                <option value="yape" selected>Yape</option>
                                                <option value="plin">Plin</option>
                                                <option value="transferencia">Transferencia Bancaria</option>
                                                <option value="tarjeta">Tarjeta Débito/Crédito</option>
                                            </select>
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label small fw-bold text-secondary mb-1">Monto Pagado</label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text bg-white">S/</span>
                                                <input type="number" step="0.50" min="0" name="pago_monto" id="inputPagoMonto" class="form-control form-control-sm text-end" placeholder="0.00">
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <input type="text" name="pago_referencia" class="form-control form-control-sm" placeholder="N° de operación o referencia (opcional)">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 6. Notas del Pedido -->
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-dark mb-1">Notas de la comanda / cocina</label>
                                <textarea name="notas" class="form-control form-control-sm" rows="2" placeholder="Ej: Sin mayonesa, enviar ají en pote extra..."></textarea>
                            </div>

                            <!-- Botón de Envío -->
                            <button type="submit" id="btnSubmitOrder" class="btn btn-primary w-100 py-2.5 fw-bold rounded-pill shadow-sm">
                                <i class="bi bi-check2-circle me-1"></i> Confirmar y Crear Pedido
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

    </div>
</main>

<!-- Modal: Registro Rápido de Nuevo Cliente -->
<div class="modal fade" id="modalNuevoCliente" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-light">
                <h6 class="modal-title fw-bold text-dark"><i class="bi bi-person-plus-fill text-primary me-2"></i>Registrar Nuevo Cliente</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formQuickClient">
                <?= CsrfHelper::field() ?>
                <input type="hidden" name="is_ajax" value="1">
                <div class="modal-body">
                    <div class="mb-2.5">
                        <label class="form-label small fw-bold">Nombre Completo <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" class="form-control form-control-sm" placeholder="Ej: Juan Pérez" required>
                    </div>
                    <div class="row g-2 mb-2.5">
                        <div class="col-6">
                            <label class="form-label small fw-bold">DNI / Documento</label>
                            <input type="text" name="numero_documento" class="form-control form-control-sm" placeholder="8 dígitos">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">Teléfono / WhatsApp</label>
                            <input type="text" name="telefono" class="form-control form-control-sm" placeholder="987654321">
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold">Dirección Habitual</label>
                        <input type="text" name="direccion" class="form-control form-control-sm" placeholder="Calle, Av, Nro, Dpto">
                    </div>
                </div>
                <div class="modal-footer p-2.5 bg-light">
                    <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold">Guardar y Seleccionar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Lógica JavaScript del Carrito y POS -->
<script>
let cart = [];

document.addEventListener('DOMContentLoaded', function () {
    const catalogGrid = document.getElementById('productCatalogGrid');
    const searchFilter = document.getElementById('productSearchFilter');
    const catButtons = document.querySelectorAll('.cat-filter-btn');
    const cartItemsList = document.getElementById('cartItemsList');
    const cartEmptyPlaceholder = document.getElementById('cartEmptyPlaceholder');
    const cartItemCountBadge = document.getElementById('cartItemCountBadge');
    const txtSubtotal = document.getElementById('txtSubtotal');
    const txtTotal = document.getElementById('txtTotal');
    const inputDescuento = document.getElementById('inputDescuento');
    const inputCostoEnvio = document.getElementById('inputCostoEnvio');
    const inputPagoMonto = document.getElementById('inputPagoMonto');
    const itemsJsonInput = document.getElementById('itemsJsonInput');
    const clientSelect = document.getElementById('clientSelect');
    const deliveryAddressInput = document.getElementById('deliveryAddressInput');
    const modDomicilio = document.getElementById('modDomicilio');
    const modRecojo = document.getElementById('modRecojo');
    const deliveryAddressSection = document.getElementById('deliveryAddressSection');
    const chkPagoInicial = document.getElementById('chkPagoInicial');
    const pagoInicialSection = document.getElementById('pagoInicialSection');
    const formQuickClient = document.getElementById('formQuickClient');

    // 1. Filtrado de productos por texto
    searchFilter.addEventListener('input', filterProducts);

    // 2. Filtrado de productos por categoría
    catButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            catButtons.forEach(b => b.classList.remove('active', 'btn-primary'));
            catButtons.forEach(b => b.classList.add('btn-outline-secondary'));
            this.classList.add('active', 'btn-primary');
            this.classList.remove('btn-outline-secondary');
            filterProducts();
        });
    });

    function filterProducts() {
        const text = searchFilter.value.toLowerCase().trim();
        const activeCatBtn = document.querySelector('.cat-filter-btn.active');
        const selectedCat = activeCatBtn ? activeCatBtn.getAttribute('data-cat') : 'all';

        const items = document.querySelectorAll('.product-card-item');
        items.forEach(it => {
            const name = it.getAttribute('data-name');
            const cat = it.getAttribute('data-cat');
            const matchText = name.includes(text);
            const matchCat = (selectedCat === 'all' || cat === selectedCat);

            if (matchText && matchCat) {
                it.style.display = 'block';
            } else {
                it.style.display = 'none';
            }
        });
    }

    // 3. Agregar producto a la canasta
    document.querySelectorAll('.btn-add-to-cart').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = parseInt(this.getAttribute('data-id'));
            const name = this.getAttribute('data-name');
            const price = parseFloat(this.getAttribute('data-price'));
            const stock = parseInt(this.getAttribute('data-stock'));

            addToCart(id, name, price, stock);
        });
    });

    function addToCart(id, name, price, stock) {
        const existing = cart.find(i => i.id === id);
        if (existing) {
            if (existing.qty + 1 > stock) {
                alert(`No hay más stock disponible para ${name} (Máximo ${stock} unidades).`);
                return;
            }
            existing.qty += 1;
        } else {
            if (stock <= 0) {
                alert(`El producto ${name} está agotado.`);
                return;
            }
            cart.push({ id, name, price, stock, qty: 1, notes: '' });
        }
        renderCart();
    }

    // 4. Renderizar la canasta
    function renderCart() {
        if (cart.length === 0) {
            cartEmptyPlaceholder.style.display = 'block';
            cartItemsList.style.display = 'none';
            cartItemCountBadge.textContent = '0 items';
            txtSubtotal.textContent = 'S/ 0.00';
            txtTotal.textContent = 'S/ 0.00';
            itemsJsonInput.value = '[]';
            inputPagoMonto.value = '0.00';
            return;
        }

        cartEmptyPlaceholder.style.display = 'none';
        cartItemsList.style.display = 'flex';
        cartItemsList.innerHTML = '';

        let subtotal = 0;
        let totalItems = 0;

        cart.forEach((item, index) => {
            const itemSubtotal = item.price * item.qty;
            subtotal += itemSubtotal;
            totalItems += item.qty;

            const div = document.createElement('div');
            div.className = 'bg-white p-2 border rounded-2 shadow-2xs';
            div.innerHTML = `
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <div class="fw-bold text-dark text-truncate" style="max-width: 170px; font-size: 13px;">${item.name}</div>
                    <div class="fw-bold text-primary" style="font-size: 13px;">S/ ${itemSubtotal.toFixed(2)}</div>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-1.5">
                        <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-1.5" onclick="changeQty(${index}, -1)">-</button>
                        <span class="fw-bold px-1.5" style="font-size: 13px;">${item.qty}</span>
                        <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-1.5" onclick="changeQty(${index}, 1)">+</button>
                        <span class="text-muted ms-1" style="font-size: 11px;">(S/ ${item.price.toFixed(2)} c/u)</span>
                    </div>
                    <button type="button" class="btn btn-link text-danger p-0 text-decoration-none" onclick="removeItem(${index})">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            `;
            cartItemsList.appendChild(div);
        });

        cartItemCountBadge.textContent = `${totalItems} items`;
        txtSubtotal.textContent = 'S/ ' + subtotal.toFixed(2);

        // Actualizar totales generales
        calculateTotals(subtotal);

        // Serializar a JSON para el envío por POST
        const payload = cart.map(i => ({
            producto_id: i.id,
            cantidad: i.qty,
            precio_unitario: i.price,
            notas: i.notes
        }));
        itemsJsonInput.value = JSON.stringify(payload);
    }

    window.changeQty = function (index, delta) {
        const item = cart[index];
        const newQty = item.qty + delta;
        if (newQty <= 0) {
            cart.splice(index, 1);
        } else if (newQty > item.stock) {
            alert(`Stock máximo alcanzado para ${item.name} (${item.stock} unidades disponibles).`);
            return;
        } else {
            item.qty = newQty;
        }
        renderCart();
    };

    window.removeItem = function (index) {
        cart.splice(index, 1);
        renderCart();
    };

    // 5. Cálculos de total con descuento y costo de envío
    function calculateTotals(subtotal) {
        const desc = Math.max(0, parseFloat(inputDescuento.value) || 0);
        const envio = modDomicilio.checked ? (Math.max(0, parseFloat(inputCostoEnvio.value) || 0)) : 0;
        const total = Math.max(0, (subtotal - desc) + envio);

        txtTotal.textContent = 'S/ ' + total.toFixed(2);
        if (chkPagoInicial.checked) {
            inputPagoMonto.value = total.toFixed(2);
        }
    }

    inputDescuento.addEventListener('input', () => {
        let sub = cart.reduce((acc, i) => acc + (i.price * i.qty), 0);
        calculateTotals(sub);
    });

    inputCostoEnvio.addEventListener('input', () => {
        let sub = cart.reduce((acc, i) => acc + (i.price * i.qty), 0);
        calculateTotals(sub);
    });

    // 6. Cambio de modalidad de entrega
    modDomicilio.addEventListener('change', function () {
        if (this.checked) {
            deliveryAddressSection.style.display = 'block';
            inputCostoEnvio.value = '5.00';
            inputCostoEnvio.disabled = false;
        }
        let sub = cart.reduce((acc, i) => acc + (i.price * i.qty), 0);
        calculateTotals(sub);
    });

    modRecojo.addEventListener('change', function () {
        if (this.checked) {
            deliveryAddressSection.style.display = 'none';
            inputCostoEnvio.value = '0.00';
            inputCostoEnvio.disabled = true;
        }
        let sub = cart.reduce((acc, i) => acc + (i.price * i.qty), 0);
        calculateTotals(sub);
    });

    // 7. Auto-completar dirección al seleccionar cliente
    clientSelect.addEventListener('change', function () {
        const opt = this.options[this.selectedIndex];
        const dir = opt.getAttribute('data-direccion');
        if (dir && deliveryAddressInput) {
            deliveryAddressInput.value = dir;
        }
    });

    // 8. Alternar pago inicial
    chkPagoInicial.addEventListener('change', function () {
        pagoInicialSection.style.display = this.checked ? 'block' : 'none';
        if (!this.checked) {
            inputPagoMonto.value = '0.00';
        } else {
            let sub = cart.reduce((acc, i) => acc + (i.price * i.qty), 0);
            calculateTotals(sub);
        }
    });

    // 9. Registro rápido de cliente por AJAX
    if (formQuickClient) {
        formQuickClient.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('index.php?route=clientes_store', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success && data.cliente) {
                    const c = data.cliente;
                    const opt = document.createElement('option');
                    opt.value = c.id;
                    opt.textContent = `${c.nombre} (${c.numero_documento || 'Sin doc'})`;
                    opt.setAttribute('data-direccion', c.direccion || '');
                    opt.setAttribute('data-telefono', c.telefono || '');
                    opt.selected = true;

                    clientSelect.appendChild(opt);
                    if (c.direccion) deliveryAddressInput.value = c.direccion;

                    // Cerrar modal
                    const modalEl = document.getElementById('modalNuevoCliente');
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();
                    formQuickClient.reset();

                    alert(`Cliente ${c.nombre} registrado y seleccionado exitosamente.`);
                } else {
                    alert('Error: ' + (data.message || 'No se pudo registrar el cliente.'));
                }
            })
            .catch(err => {
                alert('Error de conexión al registrar cliente.');
            });
        });
    }

    // 10. Validación antes del envío
    document.getElementById('orderForm').addEventListener('submit', function (e) {
        if (cart.length === 0) {
            e.preventDefault();
            alert('Debe agregar al menos un producto a la orden.');
            return false;
        }
        if (!clientSelect.value) {
            e.preventDefault();
            alert('Por favor seleccione un cliente.');
            clientSelect.focus();
            return false;
        }
    });
});
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
