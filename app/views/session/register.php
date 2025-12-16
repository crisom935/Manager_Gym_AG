<?php
// 1. Seguridad: Solo usuarios logueados pueden crear cuentas nuevas
session_start();
require_once '../../controllers/auth/auth_check.php';

include_once '../templates/header.php';
?> 

<div class="d-flex align-items-center" style="min-height: 85vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                
                <div class="card shadow-lg border-0">
                    <div class="card-body p-4 p-md-5">
                        
                        <div class="text-center mb-4">
                            <div class="d-inline-block p-3 rounded-circle bg-dark border border-danger mb-3">
                                <i class="bi bi-person-plus-fill text-danger fs-1"></i>
                            </div>
                            <h3 class="fw-bold text-white">Nuevo Usuario</h3>
                            <p class="text-muted small">Registra un nuevo administrador o empleado para el sistema.</p>
                        </div>
                        
                        <?php if (isset($_SESSION['message'])): ?>
                            <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show small" role="alert">
                                <?php if($_SESSION['message_type'] == 'success'): ?>
                                    <i class="bi bi-check-circle-fill me-2"></i>
                                <?php else: ?>
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                <?php endif; ?>
                                <?php echo $_SESSION['message']; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <?php 
                                unset($_SESSION['message']);
                                unset($_SESSION['message_type']);
                            ?>
                        <?php endif; ?>
                        
                        <form action="../../controllers/auth/register_action.php" method="POST" id="registerForm">
                            
                            <div class="mb-3">
                                <label class="form-label small text-muted text-uppercase fw-bold">Usuario</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                                    <input type="text" class="form-control" name="username" placeholder="Ej. AdminGym" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small text-muted text-uppercase fw-bold">Correo Electrónico</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                                    <input type="email" class="form-control" name="email" placeholder="correo@empresa.com" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small text-muted text-uppercase fw-bold">Contraseña</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                        <input type="password" class="form-control" id="pass1" name="password" placeholder="******" required minlength="6">
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small text-muted text-uppercase fw-bold">Confirmar</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-check-lg"></i></span>
                                        <input type="password" class="form-control" id="pass2" placeholder="******" required>
                                    </div>
                                </div>
                            </div>

                            <div id="passError" class="text-danger small mb-3 d-none">
                                <i class="bi bi-x-circle me-1"></i> Las contraseñas no coinciden.
                            </div>

                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-primary-custom btn-lg shadow-sm">
                                    <i class="bi bi-save me-2"></i> Crear Cuenta
                                </button>
                            </div>

                        </form>
                        
                        <div class="text-center mt-4 border-top border-secondary pt-3">
                            <a href="../main/dashboard.php" class="text-decoration-none text-muted small">
                                <i class="bi bi-arrow-left me-1"></i> Volver al Dashboard
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once '../templates/footer.php'; ?>

<script>
    document.getElementById('registerForm').addEventListener('submit', function(e) {
        const p1 = document.getElementById('pass1').value;
        const p2 = document.getElementById('pass2').value;
        const errorDiv = document.getElementById('passError');

        if (p1 !== p2) {
            e.preventDefault(); // Detener envío
            errorDiv.classList.remove('d-none'); // Mostrar error
            // Agitar visualmente (opcional)
            document.getElementById('pass2').classList.add('is-invalid');
        } else {
            errorDiv.classList.add('d-none');
            document.getElementById('pass2').classList.remove('is-invalid');
        }
    });
</script>