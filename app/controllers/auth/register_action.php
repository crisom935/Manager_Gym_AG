<?php
// Iniciar la sesión para poder usar variables $_SESSION para mensajes
session_start();

// 1. Verificar que la solicitud sea por método POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 2. Incluir la conexión a la base de datos
    require_once '../../../config/database.php';

    // 3. Obtener los datos del formulario (los 'name' de tus inputs)
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    
    // Capturar el rol del nuevo usuario desde el formulario
    $rol = trim($_POST['rol']);

    // 4. Validación simple
    if (empty($username) || empty($email) || empty($password) || empty($rol)) {
        // Guardar mensaje de error en la sesión
        $_SESSION['message'] = "Error: Todos los campos son obligatorios.";
        $_SESSION['message_type'] = "danger"; 
        
        // Redirigir de vuelta al formulario de registro
        header("Location: ../../views/session/register.php");
        exit;
    }

    // 5. Hashear la contraseña
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // 6. Preparar la consulta SQL para insertar el usuario (incluyendo 'rol')
    $sql = "INSERT INTO tb_usuarios (username, email, password, rol) VALUES (?, ?, ?, ?)";

    try {
        // $pdo viene de tu archivo 'database.php'
        $stmt = $pdo->prepare($sql);
        
        // Ejecutar la consulta pasando los valores
        $stmt->execute([$username, $email, $hashed_password, $rol]);

        // 7. Éxito: Redirigir al Login con mensaje de éxito
        $_SESSION['message'] = "¡Registro exitoso! Por favor, inicia sesión.";
        $_SESSION['message_type'] = "success";
        
        header("Location: ../../views/session/login.php");
        exit;

    } catch (PDOException $e) {
        // 8. Manejar Errores
        if ($e->getCode() === '23000') {
                $_SESSION['message'] = "El usuario o correo electrónico ya existe.";
        } else {
                $_SESSION['message'] = "Error de base de datos: " . $e->getMessage();
        }
        
        $_SESSION['message_type'] = "danger";
        header("Location: ../../views/session/register.php");
        exit;
    }

} else {
    // Si no es POST, redirigir
    header("Location: ../../views/session/register.php");
    exit;
}
?>