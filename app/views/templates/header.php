<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Manager</title>
    
    <link rel="icon" type="image/jpeg" href="/proyectos/ClientManager/public/img/logo.jpg">
    <link rel="manifest" href="/proyectos/ClientManager/manifest.json">
    <meta name="theme-color" content="#E62429"> <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/proyectos/ClientManager/public/css/style.css?v=2.1">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark eco-navbar fixed-top">
    <div class="container">
        <button onclick="history.back()" class="btn btn-link text-white me-2 text-decoration-none d-lg-none">
            <i class="bi bi-chevron-left fs-4"></i>
        </button>
        <a class="navbar-brand p-0" href="/proyectos/ClientManager/index.php">
            <img src="/proyectos/ClientManager/public/img/logo.jpg" 
                    alt="AG ALAN GARCIA" 
                    class="img-fluid logo-navbar">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#ecoNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-end" id="ecoNavbar">
            <ul class="navbar-nav mb-2 mb-lg-0 align-items-center">
                
                <li class="nav-item"><a class="nav-link" href="/proyectos/ClientManager/index.php">Inicio</a></li>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="/proyectos/ClientManager/app/views/main/tabla_clientes.php">
                            <i class="bi bi-person-plus-fill me-1"></i> Inscripciones
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="/proyectos/ClientManager/app/views/reports/index.php">
                            <i class="bi bi-graph-up-arrow me-1"></i> Reportes
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link text-white-50" href="/proyectos/ClientManager/app/views/inventory/index.php" title="Próximamente">
                            <i class="bi bi-box-seam me-1"></i> Inventario
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link text-white-50" href="/proyectos/ClientManager/app/views/directory/index.php" title="Próximamente">
                            <i class="bi bi-person-vcard me-1"></i> Directorio
                        </a>
                    </li>
                    
                    <li class="nav-item border-start border-secondary mx-2 d-none d-lg-block" style="height: 25px;"></li>

                    <li class="nav-item dropdown">
                        <a class="btn btn-primary-custom btn-sm dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-1"></i> Cuenta
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end">
                            <li><a class="dropdown-item" href="/proyectos/ClientManager/app/views/main/dashboard.php">Dashboard</a></li>
                        <li>
                        <a class="dropdown-item" href="/proyectos/ClientManager/app/views/session/register.php">
                            <i class="bi bi-person-plus-fill me-2"></i> Nuevo Usuario
                        </a>
                        </li>
                            <li>
                            <a class="dropdown-item text-danger" href="#" onclick="confirmarSalida(event)">
                            <i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión
                        </a>
                        </li>

                <?php else: ?>
                    <li class="nav-item border-start border-secondary mx-2 d-none d-lg-block"></li> 
                    <li class="nav-item">
                        <a class="nav-link" href="/proyectos/ClientManager/app/views/session/login.php">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Iniciar Sesión
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<main>