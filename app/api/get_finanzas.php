<?php
// Cabeceras Anti-Caché extremas
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// ... Resto de tu código (ob_clean, content-type, etc) ...
ob_clean();
header('Content-Type: application/json');
require_once '../../config/database.php';

try {
    $sql = "SELECT 
                fecha_inscripcion as fecha,
                COUNT(*) as personas,
                SUM(pago_efectivo) as total_efectivo,
                SUM(pago_tarjeta) as total_tarjeta,
                SUM(pago_transferencia) as total_transferencia, /* NUEVO */
                SUM(total_pagado) as gran_total
            FROM tb_clientes 
            GROUP BY fecha_inscripcion 
            ORDER BY fecha_inscripcion DESC
            LIMIT 30";

    $stmt = $pdo->query($sql);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if(!$data) $data = [];

    echo json_encode(['data' => $data]);

} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage(), 'data' => []]);
}
?>