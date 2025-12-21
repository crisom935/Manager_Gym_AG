<?php
session_start();
require_once '../../controllers/auth/auth_check.php';
// check_auth_and_role('administrador'); // Descomenta si solo el admin puede ver el historial, o déjalo así si el empleado también.
include_once '../templates/header.php';
?>

<div style="height: 100px;"></div>

<div class="container my-4">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-white fw-bold"><i class="bi bi-clock-history text-warning me-2"></i>Historial de Movimientos</h2>
            <p class="text-muted mb-0">Registro de entradas y salidas de producto.</p>
        </div>
        <a href="index.php" class="btn btn-outline-light">
            <i class="bi bi-box-arrow-in-left me-2"></i>Volver a Stock
        </a>
    </div>

    <div class="card shadow-sm bg-dark border-secondary">
        <div class="card-body">
            <div class="table-responsive">
                <table id="tablaMovimientos" class="table table-dark table-hover w-100 align-middle">
                    <thead class="table-secondary text-dark">
                        <tr>
                            <th>ID</th>
                            <th>Fecha</th>
                            <th>Usuario</th> <th>Producto</th>
                            <th>Tipo</th>
                            <th>Cantidad</th>
                            <th>Nota</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<?php include_once '../templates/footer.php'; ?>
<script src="/proyectos/ClientManager/public/js/movimientos_inv.js"></script>