<?php
// Iniciar la sesión para poder usar variables $_SESSION para mensajes
session_start();

// 1. Verificar que la solicitud sea por método POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 2. Incluir la conexión a la base de datos
    // (Ajusta la ruta si es necesario, 
    // .../auth/ -> .../controllers/ -> .../app/ -> (raíz)
    require_once '../../../config/database.php';

    // 3. Obtener los datos del formulario (los 'name' de tus inputs)
    // Usamos trim() para eliminar espacios en blanco al inicio o final
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // 4. Validación simple (puedes agregar más aquí)
    if (empty($username) || empty($email) || empty($password)) {
        // Guardar mensaje de error en la sesión
        $_SESSION['message'] = "Error: Todos los campos son obligatorios.";
        $_SESSION['message_type'] = "danger"; // (Para Bootstrap alerts)
        
        // Redirigir de vuelta al formulario de registro
        header("Location: ../../views/session/register.php");
        exit;
    }

    // 5. Hashear la contraseña
    // PASSWORD_BCRYPT es el algoritmo recomendado actualmente
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // 6. Preparar la consulta SQL para insertar el usuario
    // Usamos consultas preparadas (prepare) para prevenir inyección SQL
    $sql = "INSERT INTO tb_usuarios (username, email, password) VALUES (?, ?, ?)";

    try {
        // $pdo viene de tu archivo 'database.php'
        $stmt = $pdo->prepare($sql);
        
        // Ejecutar la consulta pasando los valores
        $stmt->execute([$username, $email, $hashed_password]);

        // 7. Éxito: Redirigir al Login con mensaje de éxito
        $_SESSION['message'] = "¡Registro exitoso! Por favor, inicia sesión.";
        $_SESSION['message_type'] = "success";
        
        header("Location: ../../views/session/login.php");
        exit;

    } catch (PDOException $e) {
        // 8. Manejar Errores (Ej. usuario o email duplicado)
        
        // El código de error 1062 es para 'Entrada duplicada' (UNIQUE constraint)
        if ($e->errorInfo[1] == 1062) {
            $_SESSION['message'] = "Error: El nombre de usuario o el correo electrónico ya existen.";
        } else {
            // Otro error de base de datos
            $_SESSION['message'] = "Error al registrar el usuario: " . $e->getMessage();
        }
        
        $_SESSION['message_type'] = "danger";
        header("Location: ../../views/session/register.php");
        exit;
    }

} else {
    // Si alguien intenta acceder al archivo directamente por URL
    header("Location: ../../views/session/register.php");
    exit;
}
?>