</main> 
    <footer class="eco-footer text-center mt-auto">
        <div class="container py-5">
            <div class="row gy-4">
                
                <div class="col-md-4 text-md-start">
                    <h5 class="fw-bold mb-3 text-white"><i class="bi bi-clipboard2-data"></i> Client Manager</h5>
                    <p class="small mb-0 opacity-75">
                        Versión demo del gestionador de clientes para <br>
                        gimnasios de todo tipo, box, muay thai, gym.
                    </p>
                </div>

                <div class="col-md-4">
                    <h6 class="fw-bold text-uppercase mb-3 text-white">Navegación</h6>
                    <ul class="list-unstyled small mb-3">
                        <li class="mb-2"><a href="/proyectos/ClientManager/index.php">Inicio</a></li>
                        <li class="mb-2"><a href="/proyectos/ClientManager/app/views/main/tabla_clientes.php">Inscripciones</a></li>
                        <li class="mb-2"><a href="/proyectos/ClientManager/app/views/reports/index.php">Reportes</a></li>
                    </ul>
                    
                    <button onclick="history.back()" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                        <i class="bi bi-arrow-left me-1"></i> Regresar
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