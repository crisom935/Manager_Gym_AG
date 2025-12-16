<?php
session_start();
require_once '../../controllers/auth/auth_check.php';
check_auth_and_role('administrador');
include_once '../templates/header.php';
?>

<div style="height: 100px;"></div>

<div class="container my-4">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-white fw-bold"><i class="bi bi-person-vcard text-danger me-2"></i>Directorio Completo</h2>
    </div>

    <?php if(isset($_SESSION['msg'])): ?>
        <div class="alert alert-<?php echo $_SESSION['msg_type']; ?> alert-dismissible fade show">
            <?php echo $_SESSION['msg']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['msg']); unset($_SESSION['msg_type']); ?>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body p-4">
            <div class="table-responsive-wrapper">
                <table id="tablaDirectorio" class="table table-hover w-100">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Plan Actual</th>
                            <th>Vencimiento</th>
                            <th>Acciones</th>
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
                <h5 class="modal-title">Editar Cliente</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="../../controllers/crud/update_action.php" method="POST">
                    <input type="hidden" name="id_cliente" id="editId">
                    
                    <div class="mb-3">
                        <label class="text-muted">Nombre</label>
                        <input type="text" name="nombre_cliente" id="editNombre" class="form-control bg-dark text-white border-secondary" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="text-muted">Teléfono</label>
                            <input type="text" name="telefono" id="editTel" class="form-control bg-dark text-white border-secondary">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="text-muted">Correo</label>
                            <input type="email" name="correo" id="editEmail" class="form-control bg-dark text-white border-secondary">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted">Plan</label>
                        <select name="plan_suscripcion" id="editPlan" class="form-select bg-dark text-white border-secondary">
                            <option value="Individual Semanal">Individual Semanal</option>
                            <option value="Individual Mensual">Individual Mensual</option>
                            <option value="Paquete Amigos">Paquete Amigos</option>
                            <option value="Familiar #1">Familiar #1</option>
                            <option value="Familiar #2">Familiar #2</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted">Vencimiento</label>
                        <input type="date" name="fecha_vencimiento" id="editVence" class="form-control bg-dark text-white border-secondary" required>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary-custom">Actualizar Datos</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once '../templates/footer.php'; ?>

<script>
$(document).ready(function() {
    $('#tablaDirectorio').DataTable({
        responsive: true,
        scrollX: false,
        autoWidth: false,
        ajax: {
            url: '/proyectos/ClientManager/app/api/get_clientes.php', // Reusamos la API
            type: 'GET',
            dataSrc: 'data'
        },
        language: { url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json' },
        order: [[ 0, "desc" ]],
        columns: [
            { data: 'id_cliente', render: d => `<span class="fw-bold text-muted">#${d}</span>` },
            { data: null, render: r => `<div class="fw-bold text-white text-uppercase">${r.nombre_cliente}</div><small class="text-muted">${r.telefono || ''}</small>` },
            { data: 'plan_suscripcion', render: d => `<span class="badge bg-secondary">${d}</span>` },
            { data: 'fecha_vencimiento' },
            { 
                data: null,
                render: function(data, type, row) {
                    // Preparamos datos para el modal (escapando comillas)
                    let jsonRow = JSON.stringify(row).replace(/'/g, "&#39;");
                    return `
                        <button class="btn btn-sm btn-outline-warning" onclick='cargarModal(${jsonRow})'>
                            <i class="bi bi-pencil-square"></i>
                        </button>
                    `;
                }
            }
        ]
    });
});

function cargarModal(cliente) {
    document.getElementById('editId').value = cliente.id_cliente;
    document.getElementById('editNombre').value = cliente.nombre_cliente;
    document.getElementById('editTel').value = cliente.telefono;
    document.getElementById('editEmail').value = cliente.correo;
    document.getElementById('editPlan').value = cliente.plan_suscripcion;
    document.getElementById('editVence').value = cliente.fecha_vencimiento;
    
    new bootstrap.Modal(document.getElementById('modalEditarCliente')).show();
}
</script>