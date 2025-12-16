<?php 
// Seguridad: Iniciar sesión si no está iniciada para verificar estatus
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once 'app/views/templates/header.php'; 
?>

<style>
    /* Efecto para el logo */
    .hero-logo {
        max-width: 250px;
        filter: drop-shadow(0 0 10px rgba(230, 36, 41, 0.3)); /* Resplandor rojo sutil */
        margin-bottom: 20px;
    }
    
    /* Tarjetas de características */
    .feature-card {
        background: rgba(26, 26, 26, 0.8);
        border: 1px solid #333;
        transition: transform 0.3s;
    }
    .feature-card:hover {
        transform: translateY(-5px);
        border-color: #E62429;
    }
    .feature-icon {
        font-size: 2.5rem;
        color: #E62429;
    }
</style>

<div class="hero-section d-flex align-items-center justify-content-center" style="min-height: 85vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10 text-center">
                
                <div class="card bg-transparent border-0">
                    <div class="card-body p-4">

                        <img src="/proyectos/ClientManager/public/img/logo.jpg" alt="AG Alan Garcia" class="img-fluid hero-logo rounded-3">

                        <h1 class="display-5 fw-bold text-white mb-2">
                            CLIENT <span class="text-danger">MANAGER</span>
                        </h1>
                        
                        <p class="lead text-white-50 mb-5">
                            Sistema integral de punto de venta, control de accesos y reportes financieros para <strong class="text-white">AG Alan Garcia</strong>.
                        </p>

                        <div class="d-flex flex-column flex-sm-row justify-content-center gap-3">
                            <?php if (isset($_SESSION['user_id'])): ?>
                                
                                <a href="/proyectos/ClientManager/app/views/main/dashboard.php" class="btn btn-primary-custom btn-lg shadow px-5 py-3">
                                    <i class="bi bi-speedometer2 me-2"></i> Ir al Dashboard
                                </a>

                            <?php else: ?>
                                
                                <a href="/proyectos/ClientManager/app/views/session/login.php" class="btn btn-primary-custom btn-lg shadow px-5 py-3">
                                    <i class="bi bi-box-arrow-in-right me-2"></i> Iniciar Sesión
                                </a>
                                
                            <?php endif; ?>
                        </div>

                    </div>
                </div> 

            </div>
        </div>

        <div class="row mt-5 g-4">
            <div class="col-md-4">
                <div class="card feature-card h-100 p-3 text-center">
                    <div class="feature-icon mb-2"><i class="bi bi-person-check-fill"></i></div>
                    <h5 class="text-white fw-bold">Inscripciones</h5>
                    <p class="small text-muted mb-0">Gestión rápida de altas, paquetes semanales y mensuales.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card feature-card h-100 p-3 text-center">
                    <div class="feature-icon mb-2"><i class="bi bi-cash-stack"></i></div>
                    <h5 class="text-white fw-bold">Corte de Caja</h5>
                    <p class="small text-muted mb-0">Reportes detallados de efectivo, tarjeta y transferencias.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card feature-card h-100 p-3 text-center">
                    <div class="feature-icon mb-2"><i class="bi bi-shield-lock-fill"></i></div>
                    <h5 class="text-white fw-bold">Seguridad</h5>
                    <p class="small text-muted mb-0">Control de accesos y roles de administración.</p>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include_once 'app/views/templates/footer.php'; ?>