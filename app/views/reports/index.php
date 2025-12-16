<?php
session_start();
require_once '../../controllers/auth/auth_check.php';
include_once '../templates/header.php'; 
?>

<div style="height: 20px;"></div>

<div class="container my-4">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-white fw-bold"><i class="bi bi-bar-chart-line-fill text-danger me-2"></i>Reporte Financiero</h2>
        <span class="badge bg-dark border border-secondary p-2">Últimos 30 días</span>
    </div>

    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-start border-danger border-5 h-100">
                <div class="card-body">
                    <small class="text-muted text-uppercase fw-bold" style="font-size: 0.75rem;">Ingreso Total</small>
                    <h3 class="fw-bold text-white mt-2" id="kpiTotal">$0.00</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <small class="text-success text-uppercase fw-bold" style="font-size: 0.75rem;"><i class="bi bi-cash me-1"></i> Efectivo</small>
                    <h4 class="fw-bold text-white mt-2" id="kpiEfectivo">$0.00</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <small class="text-info text-uppercase fw-bold" style="font-size: 0.75rem;"><i class="bi bi-credit-card me-1"></i> Tarjeta</small>
                    <h4 class="fw-bold text-white mt-2" id="kpiTarjeta">$0.00</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <small class="text-warning text-uppercase fw-bold" style="font-size: 0.75rem;"><i class="bi bi-bank me-1"></i> Transf.</small>
                    <h4 class="fw-bold text-white mt-2" id="kpiTransferencia">$0.00</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        
        <div class="col-lg-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-header-custom">
                    <h6 class="mb-0">Comportamiento de Ventas</h6>
                </div>
                <div class="card-body bg-dark">
                    <canvas id="graficaFinanzas" style="max-height: 300px;"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="card shadow-sm">
                <div class="card-header-custom">
                    <h6 class="mb-0">Desglose por Día</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive-wrapper">
                        <table id="tablaReportes" class="table table-hover w-100 mb-0">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th class="text-center">Personas</th>
                                    <th class="text-end text-success">Efectivo</th>
                                    <th class="text-end text-info">Tarjeta</th>
                                    <th class="text-end text-warning">Transf.</th> <th class="text-end text-white bg-danger bg-opacity-25">Gran Total</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyReportes">
                                </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<?php include_once '../templates/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="../../../public/js/reportes_logic.js"></script>