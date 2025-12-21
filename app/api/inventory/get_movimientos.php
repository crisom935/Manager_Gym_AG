<?php
// app/api/inventory/get_movimientos.php
require_once '../../../config/database.php';
header('Content-Type: application/json; charset=utf-8');

try {
    // CORREGIDO: Usamos u.username que es el nombre real en tu BD
    $sql = "SELECT 
                m.id_movimiento,
                m.tipo_movimiento,
                m.cantidad,
                m.fecha_movimiento,
                m.nota,
                p.nombre_producto,
                
                -- Aquí tomamos el 'username' de la tabla tb_usuarios
                -- Si por alguna razón es nulo, mostramos 'Sistema'
                COALESCE(u.username, 'Sistema') as nombre_usuario

            FROM tb_movimientos_inv m
            JOIN tb_productos p ON m.id_producto = p.id_producto
            
            -- Hacemos LEFT JOIN por si se borró el usuario, no perder el registro del movimiento
            LEFT JOIN tb_usuarios u ON m.id_usuario = u.id 
            
            ORDER BY m.fecha_movimiento DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['data' => $data]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error BD: ' . $e->getMessage()]);
}
?>