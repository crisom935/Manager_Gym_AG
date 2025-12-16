<?php
// Limpiar cualquier basura (espacios, enters) que se haya generado en los includes
ob_clean(); 

header('Content-Type: application/json');

// Ajusta la ruta a tu config si es necesario
require_once '../../config/database.php';

try {
    $sql = "SELECT * FROM tb_clientes ORDER BY id_cliente DESC";
    $stmt = $pdo->query($sql);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Si no hay datos, devolver array vacío
    if(!$data) {
        $data = [];
    }

    echo json_encode(['data' => $data]);

} catch (PDOException $e) {
    // Devolver error en formato JSON
    echo json_encode(['error' => $e->getMessage()]);
}
// Sin etiqueta de cierre aquí tampoco