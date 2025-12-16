<?php
session_start();

// 1. Destruir sesión en el servidor
session_unset();
session_destroy();

// 2. EL BOTÓN NUCLEAR: Ordenar al navegador limpiar caché y cookies locales
// Esto soluciona que tengas que borrar caché manualmente
header("Clear-Site-Data: 'cache', 'cookies', 'storage', 'executionContexts'");

// 3. Cabeceras anti-caché estándar (por si acaso el navegador es viejo)
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// 4. Redirigir al Login con mensaje de éxito
// Ajusta la ruta si es necesario, pero esta debería llevarte al login directo
header('Location: ../../views/session/login.php?logout=success');
exit();
?>