</main> 
    <footer class="eco-footer text-center mt-auto">
        <div class="container py-5">
            <div class="row gy-4">

                <div class="col-md-4 d-flex flex-column align-items-start align-items-md-center"> 
                <h6 class="fw-bold text-uppercase mb-4 text-white d-flex align-items-center">
                    <span class="border-start border-danger border-4 me-2 ps-1" style="height: 20px;"></span>
                    Navegación
                </h6>

                <div class="d-flex flex-column gap-2 mb-4 w-100" style="max-width: 200px;"> 
                    
                    <a href="/proyectos/ClientManager/index.php" class="nav-link-custom">
                        <i class="bi bi-house-door-fill me-2 text-danger"></i> Inicio
                    </a>

                    <?php if (isset($_SESSION['user_rol'])): ?>
                        <?php $rol = $_SESSION['user_rol']; ?>

                        <?php if ($rol === 'empleado' || $rol === 'administrador'): ?>
                            <a href="/proyectos/ClientManager/app/views/main/tabla_clientes.php" class="nav-link-custom">
                                <i class="bi bi-people-fill me-2 text-primary"></i> Inscripciones
                            </a>
                            <a href="/proyectos/ClientManager/app/views/inventory/index.php" class="nav-link-custom">
                                <i class="bi bi-box-seam-fill me-2 text-success"></i> Inventario
                            </a>
                        <?php endif; ?>

                        <?php if ($rol === 'administrador'): ?>
                            <a href="/proyectos/ClientManager/app/views/reports/index.php" class="nav-link-custom">
                                <i class="bi bi-bar-chart-line-fill me-2 text-warning"></i> Reportes Financieros
                            </a>
                            <a href="/proyectos/ClientManager/app/views/directory/index.php" class="nav-link-custom">
                                <i class="bi bi-journal-bookmark-fill me-2 text-info"></i> Directorio
                            </a>
                        <?php endif; ?>

                    <?php endif; ?>
                </div>
                
                <button onclick="history.back()" class="btn btn-outline-light btn-sm rounded-pill px-4 py-2 opacity-75 hover-opacity-100 w-100" style="max-width: 200px;">
                    <i class="bi bi-arrow-left me-2"></i> Regresar
                </button>

            </div>

                <div class="col-md-4 text-md-end">
                    <h6 class="fw-bold text-uppercase mb-3 text-white">Soporte técnico</h6>
                    <div class="d-flex justify-content-md-end justify-content-center gap-3 fs-4">
                        <a href="https://wa.me/528131396358" target="_blank" class="social-link" title="WhatsApp">
                            <i class="bi bi-whatsapp"></i>
                        </a>
                        <a href="#" class="social-link" title="Instagram">
                            <i class="bi bi-instagram"></i>
                        </a>
                        <a href="#" class="social-link" title="Facebook">
                            <i class="bi bi-facebook"></i>
                        </a>
                        <a href="mailto:onealovand@gmail.com" class="social-link" title="Enviar Correo">
                            <i class="bi bi-envelope-at-fill"></i>
                        </a>
                    </div>
                </div>
            </div>

            <hr class="my-4 opacity-25">

            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <p class="small mb-0 opacity-75">
                    © <span id="year"></span> <strong>CRISOMDEV SOLUCIONES TECNOLOGICAS</strong>. Todos los derechos reservados.
                </p>
                <small class="opacity-50">v2.1</small>
            </div>
        </div>
    </footer>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Función para confirmar cierre de sesión
    function confirmarSalida(e) {
        e.preventDefault(); // 1. Detiene el "fum" (evita que el enlace te lleve de inmediato)

        Swal.fire({
            title: '¿Cerrar Sesión?',
            text: "¿Estás seguro que deseas salir del sistema?",
            icon: 'warning',
            showCancelButton: true,
            // Estilos Dark/Red para combinar con tu app
            background: '#1a1a1a', 
            color: '#ffffff',
            confirmButtonColor: '#E62429', // Rojo AG
            cancelButtonColor: '#444',     // Gris oscuro
            confirmButtonText: 'Sí, salir',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        }).then((result) => {
            // 2. Solo si dice que SÍ, lo mandamos al logout.php
            if (result.isConfirmed) {
                window.location.href = '/proyectos/ClientManager/app/controllers/auth/logout.php';
            }
        });
    }
</script>

</body>
</html>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

    <script>
        document.getElementById('year').textContent = new Date().getFullYear();
    </script>

    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                // CAMBIO: Ruta corregida de EcoVision a ClientManager
                navigator.serviceWorker.register('/proyectos/ClientManager/sw.js')
                    .then(reg => console.log('SW registrado correctamente'))
                    .catch(err => console.log('Error SW:', err));
            });
        }
    </script>

    </body>
</html>