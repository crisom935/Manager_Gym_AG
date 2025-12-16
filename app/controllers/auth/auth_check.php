<?php
// Si no hay sesión iniciada, la iniciamos para poder verificar
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// VERIFICACIÓN: ¿Existe el ID de usuario?
if (!isset($_SESSION['user_id'])) {
    // Si no existe, es un intruso. Lo mandamos al login.
    // Ajusta la ruta si tu login está en otro lado
    header("Location: /proyectos/ClientManager/app/views/session/login.php");
    exit; // Importante: Mata la ejecución del script aquí.
}
?>