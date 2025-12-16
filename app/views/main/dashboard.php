<?php
// 1. Iniciar sesión y seguridad (Usando el Middleware que creamos)
// Ya no necesitas el bloque if grande, el auth_check lo hace por ti.
require_once '../../controllers/auth/auth_check.php';

// 2. Incluir Header
include_once '../templates/header.php';

// Obtener nombre de usuario (o poner 'Admin' si no existe)
$username = isset($_SESSION['user_username']) ? htmlspecialchars($_SESSION['user_username']) : 'Administrador';
?>

<style>
    .card-dashboard {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: 1px solid #333;
        cursor: pointer;
        text-decoration: none; /* Quitar subrayado del link */
        color: inherit; /* Heredar color de texto */
        display: block; /* Hacer que el <a> ocupe todo */
    }
    .card-dashboard:hover {
        transform: translateY(-5px); /* Se levanta un poco */
        box-shadow: 0 10px 20px rgba(230, 36, 41, 0.2); /* Sombra Roja */
        border-color: #E62429; /* Borde se pone rojo */
    }
    .card-dashboard:hover .icon-box {
        color: #E62429 !important; /* El icono se pone rojo */
    }
    .card-dashboard .icon-box {
        font-size: 3rem;
        color: #6c757d; /* Gris por defecto */
        transition: color 0.3s ease;
    }
</style>

<div style="height: 100px;"></div>

<div class="container my-4">
    
    <div class="row mb-5 align-items-center">
        <div class="col-md-8">
            <h1 class="display-5 fw-bold text-white">Hola, <span class="text-danger"><?php echo $username; ?></span></h1>
            <p class="lead text-muted">Bienvenido al panel de control de <strong>AG ALAN GARCIA</strong>.</p>
        </div>
        <div class="col-md-4 text-md-end">
            <span class="badge bg-dark border border-secondary p-3">
                <i class="bi bi-calendar-event me-2"></i> <?php echo date('d-m-Y'); ?>
            </span>
        </div>
    </div>

    <div class="row g-4">
        
        <div class="col-md-6 col-lg-3">
            <a href="tabla_clientes.php" class="card card-dashboard h-100 bg-dark">
                <div class="card-body text-center p-4">
                    <div class="icon-box mb-3">
                        <i class="bi bi-person-plus-fill"></i>
                    </div>
                    <h5 class="fw-bold text-white">Inscripciones</h5>
                    <p class="small text-muted">Registrar nuevos clientes, cobrar mensualidades y visitas.</p>
                </div>
            </a>
        </div>

        <div class="col-md-6 col-lg-3">
            <a href="../reports/index.php" class="card card-dashboard h-100 bg-dark">
                <div class="card-body text-center p-4">
                    <div class="icon-box mb-3">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                    <h5 class="fw-bold text-white">Reportes</h5>
                    <p class="small text-muted">Ver corte de caja diario, ingresos por efectivo y tarjeta.</p>
                </div>
            </a>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card card-dashboard h-100 bg-dark opacity-75" style="border-style: dashed;">
                <div class="card-body text-center p-4">
                    <div class="icon-box mb-3">
                        <i class="bi bi-person-vcard"></i>
                    </div>
                    <h5 class="fw-bold text-white">Directorio</h5>
                    <p class="small text-muted">Gestión detallada de historial de clientes.</p>
                    <span class="badge bg-secondary text-dark mt-2">Próximamente</span>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card card-dashboard h-100 bg-dark opacity-75" style="border-style: dashed;">
                <div class="card-body text-center p-4">
                    <div class="icon-box mb-3">
                        <i class="bi bi-box-seam"></i>
                    </div>
                    <h5 class="fw-bold text-white">Inventario</h5>
                    <p class="small text-muted">Control de stock de aguas, vendas y productos.</p>
                    <span class="badge bg-secondary text-dark mt-2">Próximamente</span>
                </div>
            </div>
        </div>

    </div> <div class="row mt-5">
        <div class="col-12">
            <div class="card shadow-sm bg-dark border-secondary">
                <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <h6 class="text-white mb-0"><i class="bi bi-shield-lock me-2"></i>Seguridad</h6>
                        <small class="text-muted">Gestiona tu sesión</small>
                    </div>
                    <div>
                        <a href="../../controllers/auth/logout.php" class="btn btn-outline-danger">
                            <i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión Segura
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- AQUI IRIA OTRA DIV CLASS PARA CREAR MAS MODULOS -->

</div>

<?php include_once '../templates/footer.php'; ?>