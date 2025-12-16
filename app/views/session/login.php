<?php
// 1. Seguridad: Si ya está logueado, lo mandamos al dashboard
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: ../main/dashboard.php");
    exit;
}

include_once '../templates/header.php'; 
?>

<div class="d-flex align-items-center" style="min-height: 80vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                
                <div class="card shadow-lg border-0">
                    <div class="card-body p-4 p-md-5">
                        
                        <div class="text-center mb-4">
                            <img src="/proyectos/ClientManager/public/img/logo.jpg" 
                                    alt="Logo" 
                                    class="rounded-circle shadow mb-3" 
                                    style="width: 80px; height: 80px; object-fit: cover; border: 2px solid #E62429;">
                            <h3 class="fw-bold text-white">Bienvenido</h3>
                            <p class="text-muted small">Ingresa tus credenciales para acceder</p>
                        </div>

                        <?php if (isset($_SESSION['message'])): ?>
                            <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show small" role="alert">
                                <i class="bi bi-info-circle me-2"></i><?php echo $_SESSION['message']; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <?php 
                                unset($_SESSION['message']);
                                unset($_SESSION['message_type']);
                            ?>
                        <?php endif; ?>
                        
                        <form action="../../controllers/auth/login_action.php" method="POST">
                            
                            <div class="mb-3">
                                <label for="loginEmail" class="form-label small text-muted text-uppercase fw-bold">Correo Electrónico</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                                    <input type="email" class="form-control" id="loginEmail" name="email" placeholder="ejemplo@correo.com" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <label for="loginPassword" class="form-label small text-muted text-uppercase fw-bold">Contraseña</label>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                    <input type="password" class="form-control" id="loginPassword" name="password" placeholder="••••••••" required>
                                    <button class="btn btn-outline-secondary border-start-0" type="button" id="togglePassword" style="border-color: #444;">
                                        <i class="bi bi-eye-slash"></i>
                                    </button>
                                </div>
                                <!-- <div class="text-end mt-1">
                                    <a href="#" class="forgot-link">¿Olvidaste tu contraseña?</a>
                                </div> -->
                            </div>

                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-primary-custom btn-lg shadow-sm">
                                    INICIAR SESIÓN <i class="bi bi-arrow-right ms-2"></i>
                                </button>
                            </div>

                        </form>

                        <!-- <div class="text-center mt-4">
                            <p class="text-muted small mb-0">¿Eres nuevo aquí?</p>
                            <a href="register.php" class="text-danger fw-bold text-decoration-none">Crear una cuenta</a>
                        </div> -->

                    </div>
                </div>
                
                <div class="text-center mt-3 text-muted small opacity-50">
                    &copy; <?php echo date('Y'); ?> Client Manager
                </div>

            </div>
        </div>
    </div>
</div>

<?php include_once '../templates/footer.php'; ?>

<script>
    document.getElementById('togglePassword').addEventListener('click', function () {
        const passwordInput = document.getElementById('loginPassword');
        const icon = this.querySelector('i');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        }
    });
</script>