<?php
session_start();
require_once '../../controllers/auth/auth_check.php';
include_once '../templates/header.php';
?>

<div style="height: 100px;"></div>

<div class="container my-4">
    <h2 class="text-white fw-bold mb-4"><i class="bi bi-box-seam text-danger me-2"></i>Inventario</h2>

    <?php if(isset($_SESSION['msg'])): ?>
        <div class="alert alert-<?php echo $_SESSION['msg_type']; ?> alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['msg']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['msg']); unset($_SESSION['msg_type']); ?>
    <?php endif; ?>

    <div class="row g-4" id="gridProductos">
        <div class="col-12 text-center text-white-50">Cargando inventario...</div>
    </div>
</div>

<div class="modal fade" id="modalStock" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark text-white border-secondary">
            <div class="modal-header border-secondary">
                <h5 class="modal-title fw-bold">Agregar / Vender</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">

            <form action="../../controllers/inventory/stock_action.php" method="POST">
                <input type="hidden" name="id_producto" id="modalIdProducto">
                <input type="hidden" name="precio_unitario" id="modalPrecioUnitario"> <h4 class="text-center text-danger mb-4" id="modalNombreProducto">Producto</h4>

                <div class="mb-3">
                    <label class="form-label text-muted">Tipo de Movimiento</label>
                    <select name="tipo_movimiento" id="selectTipoMovimiento" class="form-select bg-dark text-white border-secondary" required>
                        <option value="entrada">Entrada (Resurtir)</option>
                        <option value="salida" selected>Salida (Venta)</option> </select>
                </div>

                <div class="mb-3">
                    <label class="form-label text-muted">Cantidad</label>
                    <input type="number" name="cantidad" id="inputCantidad" class="form-control bg-dark text-white border-secondary" min="1" required value="1">
                </div>

                <div id="paymentSection" style="display: block;">
                    <h6 class="text-white-50 mt-4 mb-3 border-bottom border-secondary pb-2">Detalle de Pago</h6>
                    
                    <div class="mb-3">
                        <label class="form-label text-muted">Efectivo</label>
                        <input type="number" step="0.01" name="pago_efectivo" id="inputEfectivo" value="0.00" class="form-control bg-dark text-white border-secondary" min="0" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label text-muted">Tarjeta</label>
                        <input type="number" step="0.01" name="pago_tarjeta" id="inputTarjeta" value="0.00" class="form-control bg-dark text-white border-secondary" min="0" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted">Transferencia</label>
                        <input type="number" step="0.01" name="pago_transferencia" id="inputTransferencia" value="0.00" class="form-control bg-dark text-white border-secondary" min="0" required>
                    </div>
                    
                    <div class="alert alert-danger text-center fw-bold mt-4" id="alertDiferencia" style="display:none;">
                        Falta por pagar: $<span id="montoFaltante">0.00</span>
                    </div>
                    
                    <div class="alert alert-success text-center fw-bold mt-4">
                        Total Requerido: $<span id="labelMontoRequerido">0.00</span>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted">Nota (Opcional)</label>
                    <input type="text" name="nota" class="form-control bg-dark text-white border-secondary" placeholder="Ej. Venta mostrador">
                </div>

                <div class="d-grid">
                    <button type="submit" id="btnGuardarMovimiento" class="btn btn-primary-custom">Guardar Movimiento</button>
                </div>
            </form>
            </div>
        </div>
    </div>
</div>

<?php include_once '../templates/footer.php'; ?>
<script src="/proyectos/ClientManager/public/js/inventory.js"></script>