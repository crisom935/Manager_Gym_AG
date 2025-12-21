<?php
// app/controllers/inventory/stock_action.php
session_start();
require_once '../../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 1. CAPTURAR USUARIO (Para que salga en el historial)
    // Ajusta esto si tu variable de sesión tiene otro nombre (ej. $_SESSION['id_usuario'])
    $id_usuario = $_SESSION['user_id'] ?? $_SESSION['id_usuario'] ?? null; 

    $id_producto = $_POST['id_producto'];
    $tipo = $_POST['tipo_movimiento'];
    $cantidad = $_POST['cantidad'];
    $nota = $_POST['nota'] ?? '';
    
    // Datos monetarios (solo importan si es salida/venta)
    $efectivo = $_POST['pago_efectivo'] ?? 0;
    $tarjeta = $_POST['pago_tarjeta'] ?? 0;
    $transf = $_POST['pago_transferencia'] ?? 0;
    $total_venta = $efectivo + $tarjeta + $transf;

    try {
        $pdo->beginTransaction();

        // A. ACTUALIZAR STOCK ACTUAL (Corrección: usamos 'stock_actual')
        if ($tipo === 'entrada') {
            // Sumar al inventario
            $sqlStock = "UPDATE tb_productos SET stock_actual = stock_actual + :cant WHERE id_producto = :id";
        } else {
            // Verificar stock suficiente antes de restar
            $check = $pdo->prepare("SELECT stock_actual FROM tb_productos WHERE id_producto = ?");
            $check->execute([$id_producto]);
            $current = $check->fetchColumn();
            
            if ($current < $cantidad) {
                throw new Exception("Stock insuficiente (Tienes: $current, Intentas vender: $cantidad)");
            }
            // Restar del inventario
            $sqlStock = "UPDATE tb_productos SET stock_actual = stock_actual - :cant WHERE id_producto = :id";
        }
        
        $stmtS = $pdo->prepare($sqlStock);
        $stmtS->execute([':cant' => $cantidad, ':id' => $id_producto]);

        // B. GUARDAR MOVIMIENTO (Historial)
        $sqlMov = "INSERT INTO tb_movimientos_inv 
                    (id_producto, id_usuario, tipo_movimiento, cantidad, fecha_movimiento, nota) 
                    VALUES (:prod, :user, :tipo, :cant, NOW(), :nota)";
        
        $stmtM = $pdo->prepare($sqlMov);
        $stmtM->execute([
            ':prod' => $id_producto,
            ':user' => $id_usuario, // Guardamos quién hizo el movimiento
            ':tipo' => $tipo,
            ':cant' => $cantidad,
            ':nota' => $nota
        ]);

        // C. SI ES VENTA, GUARDAR EN tb_ventas_productos (Para reporte financiero)
        if ($tipo === 'salida') {
            $sqlVenta = "INSERT INTO tb_ventas_productos 
                        (id_producto, cantidad, total_venta, pago_efectivo, pago_tarjeta, pago_transferencia, fecha_venta)
                        VALUES (:prod, :cant, :total, :efe, :tar, :tra, NOW())";
            $stmtV = $pdo->prepare($sqlVenta);
            $stmtV->execute([
                ':prod' => $id_producto,
                ':cant' => $cantidad,
                ':total' => $total_venta,
                ':efe' => $efectivo,
                ':tar' => $tarjeta,
                ':tra' => $transf
            ]);
        }

        $pdo->commit();
        $_SESSION['msg'] = "Movimiento registrado correctamente.";
        $_SESSION['msg_type'] = "success";

    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['msg'] = "Error: " . $e->getMessage();
        $_SESSION['msg_type'] = "danger";
    }

    header('Location: ../../views/inventory/index.php');
    exit;
}
?>