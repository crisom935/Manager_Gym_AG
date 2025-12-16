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
                <h5 class="modal-title fw-bold">Actualizar Stock</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="../../controllers/inventory/stock_action.php" method="POST">
                    <input type="hidden" name="id_producto" id="modalIdProducto">
                    
                    <h4 class="text-center text-danger mb-4" id="modalNombreProducto">Producto</h4>

                    <div class="mb-3">
                        <label class="form-label text-muted">Tipo de Movimiento</label>
                        <select name="tipo_movimiento" class="form-select bg-dark text-white border-secondary" required>
                            <option value="entrada">📥 Entrada (Resurtir)</option>
                            <option value="salida">📤 Salida (Venta/Merma)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted">Cantidad</label>
                        <input type="number" name="cantidad" class="form-control bg-dark text-white border-secondary" min="1" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted">Nota (Opcional)</label>
                        <input type="text" name="nota" class="form-control bg-dark text-white border-secondary" placeholder="Ej. Venta mostrador">
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary-custom">Guardar Movimiento</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once '../templates/footer.php'; ?>

<script>
document.addEventListener("DOMContentLoaded", function() {
    fetch('/proyectos/ClientManager/app/api/inventory/get_products.php')
        .then(res => res.json())
        .then(json => {
            const grid = document.getElementById('gridProductos');
            grid.innerHTML = '';
            
            json.data.forEach(p => {
                grid.innerHTML += `
                    <div class="col-md-4 col-lg-3">
                        <div class="card h-100 bg-dark border-secondary shadow-sm">
                            <div class="card-body text-center">
                                <div class="mb-3 text-danger display-4">
                                    <i class="bi ${p.nombre_producto.includes('Agua') ? 'bi-droplet-fill' : 'bi-bandaid-fill'}"></i>
                                </div>
                                <h5 class="card-title fw-bold text-white">${p.nombre_producto}</h5>
                                <h2 class="fw-bold my-3 ${p.stock_actual < 5 ? 'text-danger' : 'text-success'}">${p.stock_actual}</h2>
                                <p class="text-muted small">En existencia</p>
                                <div class="badge bg-secondary mb-3">$${p.precio_venta} MXN</div>
                                <div class="d-grid">
                                    <button class="btn btn-outline-light btn-sm" onclick="abrirModal(${p.id_producto}, '${p.nombre_producto}')">
                                        <i class="bi bi-arrow-left-right me-2"></i> Ajustar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
        });
});

function abrirModal(id, nombre) {
    document.getElementById('modalIdProducto').value = id;
    document.getElementById('modalNombreProducto').innerText = nombre;
    new bootstrap.Modal(document.getElementById('modalStock')).show();
}
</script>