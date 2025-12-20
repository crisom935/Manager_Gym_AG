<?php
session_start();
require_once '../../controllers/auth/auth_check.php';
check_auth_and_role('administrador'); // Solo admin
include_once '../templates/header.php';
?>

<div style="height: 100px;"></div>

<div class="container-fluid px-4 my-4">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-white fw-bold"><i class="bi bi-journal-bookmark-fill text-info me-2"></i>Directorio General</h2>
            <p class="text-muted mb-0">Gestión completa de la base de datos de clientes.</p>
        </div>
    </div>

    <?php if(isset($_SESSION['msg'])): ?>
        <div class="alert alert-<?php echo $_SESSION['msg_type']; ?> alert-dismissible fade show">
            <i class="bi bi-info-circle-fill me-2"></i><?php echo $_SESSION['msg']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['msg']); unset($_SESSION['msg_type']); ?>
    <?php endif; ?>

    <div class="card shadow-sm bg-dark border-secondary">
        <div class="card-body">
            <div class="table-responsive">
                <table id="tablaDirectorio" class="table table-dark table-hover w-100 align-middle" style="font-size: 0.9rem;">
                    <thead class="table-secondary text-dark">
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Contacto</th>
                            <th>Plan y Estado</th>
                            <th>Detalle Financiero</th>
                            <th>Fechas Registro</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditarCliente" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark text-white border-secondary">
            <div class="modal-header border-secondary">
                <h5 class="modal-title text-warning"><i class="bi bi-pencil-square me-2"></i>Editar Cliente</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="../../controllers/crud/update_action.php" method="POST">
                    <input type="hidden" name="id_cliente" id="editId">
                    
                    <div class="mb-3">
                        <label class="text-muted small text-uppercase">Nombre Completo</label>
                        <input type="text" name="nombre_cliente" id="editNombre" class="form-control bg-dark text-white border-secondary" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="text-muted small text-uppercase">Teléfono</label>
                            <input type="text" name="telefono" id="editTel" class="form-control bg-dark text-white border-secondary">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="text-muted small text-uppercase">Correo</label>
                            <input type="email" name="correo" id="editEmail" class="form-control bg-dark text-white border-secondary">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="text-muted small text-uppercase">Plan Actual</label>
                        <select name="plan_suscripcion" id="editPlan" class="form-select bg-dark text-white border-secondary">
                            <option value="Individual Semanal">Individual Semanal</option>
                            <option value="Individual Mensual">Individual Mensual</option>
                            <option value="Paquete Amigos">Paquete Amigos</option>
                            <option value="Familiar #1">Familiar #1</option>
                            <option value="Familiar #2">Familiar #2</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="text-muted small text-uppercase">Fecha Vencimiento</label>
                        <input type="date" name="fecha_vencimiento" id="editVence" class="form-control bg-dark text-white border-secondary" required>
                    </div>
                    
                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-warning fw-bold">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once '../templates/footer.php'; ?>

<script src="../../../public/js/directorio_logic.js"></script>