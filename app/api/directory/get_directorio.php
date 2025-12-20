<?php
// app/api/directory/get_directorio.php
require_once '../../../config/database.php';

header('Content-Type: application/json; charset=utf-8');

try {
    // Usamos COALESCE para que si es null, valga 0
    $sql = "SELECT 
                id_cliente,
                nombre_cliente,
                telefono,
                correo,
                plan_suscripcion,
                fecha_inscripcion,
                fecha_vencimiento,
                created_at,
                
                -- Datos Financieros
                COALESCE(pago_efectivo, 0) as efectivo,
                COALESCE(pago_tarjeta, 0) as tarjeta,
                COALESCE(pago_transferencia, 0) as transferencia,
                
                -- CORRECCIÓN: Usamos el nombre real de tu columna
                COALESCE(inscripcion, 0) as costo_inscripcion, 
                
                COALESCE(descuento, 0) as descuento,
                
                -- Total Pagado
                (COALESCE(pago_efectivo, 0) + COALESCE(pago_tarjeta, 0) + COALESCE(pago_transferencia, 0)) as total_pagado
                
            FROM tb_clientes 
            ORDER BY id_cliente DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['data' => $data]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error BD: ' . $e->getMessage()]);
}
?>