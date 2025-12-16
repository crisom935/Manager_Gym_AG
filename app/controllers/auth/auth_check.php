v<?php
// Si no hay sesión iniciada, la iniciamos para poder verificar
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// -----------------------------------------------------------------------------------
// CAMBIO IMPORTANTE: Función para asegurar que el rol esté en la sesión y verificar acceso
// -----------------------------------------------------------------------------------

function check_auth_and_role($required_role = null) {
    // 1. Verificar si hay ID de usuario (Autenticación básica)
    if (!isset($_SESSION['user_id'])) {
        header("Location: /proyectos/ClientManager/app/views/session/login.php");
        exit;
    }

    // 2. Verificar/Cargar el Rol
    if (!isset($_SESSION['user_rol'])) {
        // Si el rol no está en la sesión, debemos cargarlo de la DB.
        // REQUIERE LA CONEXIÓN A LA BASE DE DATOS AQUÍ.
        // Asegúrate de que la ruta a 'database.php' sea correcta desde este archivo.
        require_once '../../../config/database.php'; // Ajusta esta ruta si es necesario

        try {
            global $pdo; // Asegura que usamos la conexión global
            $stmt = $pdo->prepare("SELECT rol FROM tb_usuarios WHERE id = :id");
            $stmt->execute([':id' => $_SESSION['user_id']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $_SESSION['user_rol'] = $user['rol'];
            } else {
                // Si el usuario no existe en DB, forzar cierre de sesión
                session_destroy();
                header("Location: /proyectos/ClientManager/app/views/session/login.php");
                exit;
            }
        } catch (PDOException $e) {
            // Manejo de errores de DB
            error_log("Error cargando rol de usuario: " . $e->getMessage());
            // Por seguridad, redirigir al login si falla la DB
            header("Location: /proyectos/ClientManager/app/views/session/login.php");
            exit;
        }
    }
    
    // 3. Verificar el Rol Requerido (Autorización)
    if ($required_role) {
        $current_role = $_SESSION['user_rol'];
        
        // El administrador siempre tiene acceso total
        if ($current_role === 'administrador') {
            return;
        }

        // Si el usuario no es admin y el rol requerido no coincide
        if ($current_role !== $required_role) {
            // Aquí puedes redirigir a una página de "Acceso Denegado"
            // Por ahora, redirigiremos al dashboard
            header("Location: ../../views/main/dashboard.php");
            exit;
        }
    }
}

// Llama a la función si este script se incluye directamente
// Solo realiza la autenticación (user_id check) si no se usa para RBAC específico
// Si el script se incluye, es porque queremos al menos verificar la autenticación
check_auth_and_role();

// -----------------------------------------------------------------------------------
// FIN CAMBIO IMPORTANTE
// -----------------------------------------------------------------------------------
?>