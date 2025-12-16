<?php

// Configuración de la Base de Datos
define('DB_HOST', '127.0.0.1');      
define('DB_NAME', 'client_manager_db');    
define('DB_USER', 'root');            
define('DB_PASS', '');                
define('DB_CHARSET', 'utf8mb4');      

// Opciones de configuración para PDO
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, 
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       
    PDO::ATTR_EMULATE_PREPARES   => false,                  
];

// DSN
$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;

try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    // Es importante que este archivo NO haga echo de nada si falla, 
    // o romperá el JSON. Mejor dejar que el script principal maneje el try/catch,
    // o matar el script aquí sin imprimir HTML.
    die("Error de conexión a la BD."); 
}

// ¡IMPORTANTE: NO PONER LA ETIQUETA DE CIERRE AQUÍ!
// Dejar el archivo abierto previene que se cuelen espacios en blanco.