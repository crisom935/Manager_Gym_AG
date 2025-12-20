<?php
session_start();
require_once '../../controllers/auth/auth_check.php';
check_auth_and_role('administrador');
include_once '../templates/header.php'; 
?>

<div style="height: 20px;"></div>

<div class="container my-4">
    
    <div class="row align-items-center mb-4 gy-3">
        <div class="col-md-6">
            <h2 class="text-white fw-bold mb-0">
                <i class="bi bi-file-earmark-spreadsheet-fill text-danger me-2"></i>Bitácora de Reportes
            </h2>
        </div>
        
        <div class="col-md-6">
            <div class="d-flex gap-2 flex-wrap justify-content-md-end">
                <input type="month" id="filterMonthYear" class="form-control bg-dark text-white border-secondary" style="min-width: 140px;">
                
                <select id="filterWeek" class="form-select bg-dark text-white border-secondary" style="min-width: 140px;">
                    <option value="all">Todo el Mes</option>
                </select>

                <div class="d-flex gap-2 w-100-mobile">
                    <button class="btn btn-danger flex-grow-1" id="btnLoadReport"><i class="bi bi-search"></i></button>
                    <button class="btn btn-secondary flex-grow-1" onclick="window.print()"><i class="bi bi-printer"></i></button>
                </div>
            </div>
        </div>
    </div>

    <div class="card bg-dark text-white border-secondary mb-3">
        <div class="card-header border-secondary bg-secondary bg-opacity-25">
            <h5 class="mb-0 fw-bold text-center text-uppercase">Gran Total Mensual</h5>
        </div>
        <div class="card-body">
            <div class="row text-center gy-3">
                <div class="col-6 col-md-3 border-end-md border-secondary">
                    <small class="text-muted d-block text-uppercase small-label">Total Ingresos</small>
                    <h2 class="fw-bold text-success mb-0 responsive-amount" id="mtTotal">$0.00</h2>
                </div>
                <div class="col-6 col-md-2 border-end-md border-secondary">
                    <small class="text-muted d-block text-uppercase small-label">Personas</small>
                    <h2 class="fw-bold text-white mb-0 responsive-amount" id="mtPersonas">0</h2>
                </div>
                <div class="col-12 col-md-3 border-end-md border-secondary">
                    <div class="d-flex justify-content-around justify-content-md-between px-md-3">
                        <div class="text-center text-md-start">
                            <small class="text-muted d-block small-label">Inscripciones</small>
                            <h5 class="fw-bold text-white mb-0" id="mtInsc">$0.00</h5>
                        </div>
                        <div class="text-center text-md-end">
                            <small class="text-muted d-block small-label">Productos</small>
                            <h5 class="fw-bold text-white mb-0" id="mtProd">$0.00</h5>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <small class="text-muted d-block mb-1 small-label">FORMA DE PAGO</small>
                    <div class="d-flex justify-content-around flex-wrap gap-2">
                        <span class="badge bg-success bg-opacity-25 text-success border border-success p-2"><i class="bi bi-cash"></i> <span id="mtEfe">$0</span></span>
                        <span class="badge bg-info bg-opacity-25 text-info border border-info p-2"><i class="bi bi-card-heading"></i> <span id="mtTar">$0</span></span>
                        <span class="badge bg-warning bg-opacity-25 text-warning border border-warning p-2"><i class="bi bi-bank"></i> <span id="mtTra">$0</span></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card bg-dark text-white border-secondary mb-4" id="cardSemanal" style="display:none;">
        <div class="card-header border-secondary bg-danger bg-opacity-25">
            <h6 class="mb-0 fw-bold text-center text-uppercase text-danger" id="labelSemanal">Gran Total Semanal</h6>
        </div>
        <div class="card-body py-2">
            <div class="row text-center align-items-center gy-2">
                <div class="col-6 col-md-3 border-end-md border-secondary">
                    <small class="text-white-50 d-block small-label">TOTAL SEMANA</small>
                    <h3 class="fw-bold text-danger mb-0 responsive-amount" id="stTotal">$0.00</h3>
                </div>
                <div class="col-6 col-md-2 border-end-md border-secondary">
                    <small class="text-white-50 d-block small-label">PERSONAS</small>
                    <h4 class="fw-bold text-white mb-0 responsive-amount" id="stPersonas">0</h4>
                </div>
                    <div class="col-12 col-md-3 border-end-md border-secondary">
                    <div class="d-flex justify-content-center gap-4">
                            <div><small class="text-white-50 d-block small-label">Insc.</small><span class="fw-bold" id="stInsc">$0</span></div>
                            <div><small class="text-white-50 d-block small-label">Prod.</small><span class="fw-bold" id="stProd">$0</span></div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="d-flex justify-content-around">
                        <span class="text-success small fw-bold"><i class="bi bi-cash"></i> <span id="stEfe">$0</span></span>
                        <span class="text-info small fw-bold"><i class="bi bi-card-heading"></i> <span id="stTar">$0</span></span>
                        <span class="text-warning small fw-bold"><i class="bi bi-bank"></i> <span id="stTra">$0</span></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="reporteContainer">
        </div>

</div>

<?php include_once '../templates/footer.php'; ?>
<script src="../../../public/js/reportes_logic.js"></script>

<style>
    /* Estilos base de la tabla */
    .table-ag th { background-color: #2c3034; color: #fff; font-weight: 600; font-size: 0.85rem; white-space: nowrap; }
    .table-ag td { font-size: 0.85rem; vertical-align: middle; white-space: nowrap; } /* white-space: nowrap evita que el texto se rompa feo en celular */
    .row-total-dia { border-top: 2px solid #555; background-color: #2d0a0a !important; }

    /* Ajustes para Móvil (Pantallas menores a 768px) */
    @media (max-width: 768px) {
        .w-100-mobile { width: 100% !important; margin-top: 10px; }
        .border-end-md { border-right: none !important; border-bottom: 1px solid #444; padding-bottom: 10px; margin-bottom: 10px; }
        .small-label { font-size: 0.7rem; }
        .responsive-amount { font-size: 1.5rem; }
        
        /* Asegurar que las tablas tengan scroll horizontal */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
    }

    /* Estilos de Impresión (Sin cambios, para que salga perfecto en hoja) */
    @media print {
        body { background-color: white !important; color: black !important; }
        .bg-dark, .card, .table { background-color: white !important; color: black !important; border: 1px solid #ccc !important; }
        .text-white { color: black !important; }
        .btn, header, footer, .d-flex.gap-2 { display: none !important; } 
        #cardSemanal { display: block !important; border: 1px solid #000 !important; }
        .table-responsive { overflow: visible !important; } /* En papel no hay scroll */
    }
</style>