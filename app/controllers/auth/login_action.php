<?php
// ¡El paso MÁS importante para logins!
session_start();

// 1. Verificar que la solicitud sea por método POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 2. Incluir la conexión (CORREGIDO)
    // Usamos ../ para subir niveles en las carpetas de tu compu
    require_once '../../../config/database.php';

    // 3. Obtener los datos del formulario
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // 4. Validación simple
    if (empty($email) || empty($password)) {
        $_SESSION['message'] = "Correo y contraseña son obligatorios.";
        $_SESSION['message_type'] = "danger";
        // Header usa URL del navegador: Empieza con /proyectos/...
        header("Location: /proyectos/ClientManager/app/views/session/login.php");
        exit;
    }

    // 5. Preparar y ejecutar la consulta
    try {
        $sql = "SELECT id, username, email, password FROM tb_usuarios WHERE email = ?"; 
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email]);
        
        if ($stmt->rowCount() == 1) {
            $user = $stmt->fetch();

            // 6. Verificar contraseña
            if (password_verify($password, $user['password'])) {
                
                // 7. Login exitoso
                session_regenerate_id(true);

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_username'] = $user['username'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['logged_in'] = true;

                // 9. Redirigir al Dashboard (CORREGIDO - Ruta absoluta de navegador)
                header("Location: /proyectos/ClientManager/app/views/main/dashboard.php");
                exit;

            } else {
                // Contraseña incorrecta
                $_SESSION['message'] = "Correo o contraseña incorrectos.";
                $_SESSION['message_type'] = "danger";
                header("Location: /proyectos/ClientManager/app/views/session/login.php");
                exit;
            }

        } else {
            // Usuario no encontrado
            $_SESSION['message'] = "Correo o contraseña incorrectos.";
            $_SESSION['message_type'] = "danger";
            header("Location: /proyectos/ClientManager/app/views/session/login.php");
            exit;
        }

    } catch (PDOException $e) {
        $_SESSION['message'] = "Error de base de datos: " . $e->getMessage();
        $_SESSION['message_type'] = "danger";
        header("Location: /proyectos/ClientManager/app/views/session/login.php");
        exit;
    }

} else {
    // Si no es POST, redirigir
    header("Location: /proyectos/ClientManager/app/views/session/login.php");
    exit;
}
?>