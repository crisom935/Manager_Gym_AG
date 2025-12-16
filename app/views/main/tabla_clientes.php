<?php
session_start();
include_once '../templates/header.php'; 
require_once '../../controllers/auth/auth_check.php';
?>

<div style="height: 20px;"></div>

<div class="container my-4">

    <?php if(isset($_SESSION['msg'])): ?>
        <div class="alert alert-<?php echo $_SESSION['msg_type']; ?> alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['msg']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php 
            unset($_SESSION['msg']);
            unset($_SESSION['msg_type']);
        ?>
    <?php endif; ?>

    <div class="row">
        
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header-custom">
                    <h5 class="mb-0"><i class="bi bi-person-plus-fill me-2"></i> Nuevo Registro</h5>
                </div>
                <div class="card-body">
                    <form action="../../controllers/crud/create_action.php" method="POST">
                        
                        <div class="mb-3">
                            <label class="form-label small text-muted">Selecciona el Paquete *</label>
                            <select name="plan_suscripcion" id="selectPlan" class="form-select" required onchange="generarInputsNombres()">
                                <option value="" selected disabled>-- Selecciona --</option>
                                <option value="Individual Semanal" data-cantidad="1" data-precio="200">1. Individual Semanal ($200)</option>
                                <option value="Individual Mensual" data-cantidad="1" data-precio="650">2. Individual Mensual ($650)</option>
                                <option value="Paquete Amigos" data-cantidad="2" data-precio="1100">3. Paquete Amigos - 2 Personas ($1100)</option>
                                <option value="Familiar #1" data-cantidad="3" data-precio="1650">4. Paquete Familiar #1 - 3 Personas ($1650)</option>
                                <option value="Familiar #2" data-cantidad="4" data-precio="2300">5. Paquete Familiar #2 - 4 Personas ($2300)</option>
                            </select>
                        </div>

                        <div id="contenedorNombres" class="mb-3">
                            <label class="form-label small text-muted">Nombre del Cliente (Titular) *</label>
                            <input type="text" name="nombres[]" class="form-control mb-2" placeholder="Nombre completo" required>
                        </div>

                        <hr class="border-secondary opacity-25">

                        <div class="mb-3">
                            <label class="form-label small text-muted">Cargo de Inscripción</label>
                            <select name="monto_inscripcion" id="selectInscripcion" class="form-select" onchange="ajustarInputsPago()">
                                <option value="0" selected>No Cobrar (GRATIS)</option>
                                <option value="150">Cobrar Inscripción ($150)</option>
                            </select>
                        </div>

                        <div class="bg-dark p-3 rounded mb-3 border border-secondary">
                            <h6 class="text-white mb-3"><i class="bi bi-cash-coin me-2"></i>Cobro</h6>
                            <div class="row">
                                <div class="col-12 mb-2">
                                    <label class="form-label small text-muted">Método de Pago</label>
                                    <select class="form-select mb-2" id="metodoPago" onchange="ajustarInputsPago()">
                                        <option value="Efectivo" selected>Sólo Efectivo</option>
                                        <option value="Tarjeta">Sólo Tarjeta</option>
                                        <option value="Transferencia">Sólo Transferencia</option>
                                        <option value="Mixto">Mixto (Combinado)</option>
                                    </select>
                                </div>

                                <div class="col-4">
                                    <label class="form-label small text-success">Efectivo $</label>
                                    <input type="number" step="0.50" name="monto_efectivo" id="inputEfectivo" class="form-control" value="0" required>
                                </div>
                                <div class="col-4">
                                    <label class="form-label small text-info">Tarjeta $</label>
                                    <input type="number" step="0.50" name="monto_tarjeta" id="inputTarjeta" class="form-control" value="0" readonly>
                                </div>
                                <div class="col-4">
                                    <label class="form-label small text-warning">Transf. $</label>
                                    <input type="number" step="0.50" name="monto_transferencia" id="inputTransferencia" class="form-control" value="0" readonly>
                                </div>
                                
                                <div class="col-12 mt-2 text-end">
                                    <span class="text-muted small">Total a registrar: </span>
                                    <span class="fw-bold text-white fs-5" id="labelTotal">$0.00</span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label small text-muted">Teléfono</label>
                                <input type="tel" name="telefono" class="form-control" placeholder="81...">
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label small text-muted">Email</label>
                                <input type="email" name="correo" class="form-control" placeholder="@...">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label small text-muted">Fecha de Inicio *</label>
                                <?php date_default_timezone_set('America/Monterrey'); ?>
                                <input type="date" name="fecha_inscripcion" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" name="btn_guardar" class="btn btn-primary-custom">
                                <i class="bi bi-save me-2"></i> Cobrar y Registrar
                            </button>
                        </div>
                    </form>
                </div> 
            </div> 
        </div> 

        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header-custom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-table me-2"></i> Directorio de Clientes</h5>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive-wrapper">
                        <table id="tablaClientes" class="table table-hover w-100">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Estado</th>
                                    <th>Cliente</th>     
                                    <th>Plan</th>        
                                    <th>Contacto</th>    
                                    <th>Vencimiento</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div> 
    </div> 
</div> 

<?php include_once '../templates/footer.php'; ?>

<script src="../../../public/js/clientes_logic.js"></script>

<script src="../../../public/js/precios_logic.js"></script>