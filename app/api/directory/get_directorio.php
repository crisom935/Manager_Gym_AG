<?php
// app/api/directory/get_directorio.php
require_once '../../../config/database.php';
header('Content-Type: application/json; charset=utf-8');

try {
    $sql = "SELECT 
                c.id_cliente,
                c.nombre_cliente,
                c.telefono,
                c.correo,
                c.plan_suscripcion,
                c.fecha_inscripcion,
                c.fecha_vencimiento,
                c.created_at,
                
                -- Datos Financieros
                COALESCE(c.pago_efectivo, 0) as efectivo,
                COALESCE(c.pago_tarjeta, 0) as tarjeta,
                COALESCE(c.pago_transferencia, 0) as transferencia,
                COALESCE(c.inscripcion, 0) as costo_inscripcion, 
                COALESCE(c.descuento, 0) as descuento,
                (COALESCE(c.pago_efectivo, 0) + COALESCE(c.pago_tarjeta, 0) + COALESCE(c.pago_transferencia, 0)) as total_pagado,

                -- AQUÍ TRAEMOS EL USUARIO (username)
                COALESCE(u.username, 'Sistema') as registrado_por

            FROM tb_clientes c
            -- JOIN CON USUARIOS
            LEFT JOIN tb_usuarios u ON c.id_usuario = u.id 
            ORDER BY c.id_cliente DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['data' => $data]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error BD: ' . $e->getMessage()]);
}
?>