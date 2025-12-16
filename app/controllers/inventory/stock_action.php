<?php
session_start();
require_once '../../../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_producto = $_POST['id_producto'];
    $tipo        = $_POST['tipo_movimiento']; // 'entrada' o 'salida'
    $cantidad    = intval($_POST['cantidad']);
    $nota        = trim($_POST['nota']);
    
    // CAPTURA DE CAMPOS DE VENTA (Solo relevantes si $tipo == 'salida')
    $precio_unitario      = floatval($_POST['precio_unitario'] ?? 0);
    $pago_efectivo        = floatval($_POST['pago_efectivo'] ?? 0);
    $pago_tarjeta         = floatval($_POST['pago_tarjeta'] ?? 0);
    $pago_transferencia   = floatval($_POST['pago_transferencia'] ?? 0);
    
    $total_pagado = $pago_efectivo + $pago_tarjeta + $pago_transferencia;
    
    if ($cantidad > 0) {
        try {
            $pdo->beginTransaction();

            // 1. Lógica de Salida (Venta/Merma) y Validaciones
            if ($tipo == 'salida') {
                $monto_requerido = $precio_unitario * $cantidad;
                
                // VALIDACIÓN DE PAGO (Si el tipo de movimiento es venta, debe cuadrar)
                // Usamos abs() para comparar flotantes
                if (abs($total_pagado - $monto_requerido) > 0.01) {
                    throw new Exception("Error de pago: El total pagado ($" . number_format($total_pagado, 2) . ") no coincide con el total de la venta ($" . number_format($monto_requerido, 2) . ").");
                }
                
                // Validar que haya stock suficiente antes de la salida
                $check = $pdo->prepare("SELECT stock_actual FROM tb_productos WHERE id_producto = :id");
                $check->execute([':id' => $id_producto]);
                $actual = $check->fetchColumn();

                if ($actual < $cantidad) {
                    throw new Exception("No hay suficiente stock para realizar la salida (actual: $actual, solicitada: $cantidad).");
                }
            }
            
            // 2. Actualizar Stock en tb_productos
            $sqlStock = "";
            if ($tipo == 'entrada') {
                $sqlStock = "UPDATE tb_productos SET stock_actual = stock_actual + :cant WHERE id_producto = :id";
            } else {
                $sqlStock = "UPDATE tb_productos SET stock_actual = stock_actual - :cant WHERE id_producto = :id";
            }
            
            $stmtStock = $pdo->prepare($sqlStock);
            $stmtStock->execute([':cant' => $cantidad, ':id' => $id_producto]);

            // 3. Registrar en Historial (tb_movimientos_inv)
            $sqlHist = "INSERT INTO tb_movimientos_inv (id_producto, tipo_movimiento, cantidad, nota) VALUES (:id, :tipo, :cant, :nota)";
            $stmtHist = $pdo->prepare($sqlHist);
            $stmtHist->execute([':id' => $id_producto, ':tipo' => $tipo, ':cant' => $cantidad, ':nota' => $nota]);

            // 4. REGISTRAR VENTA FINANCIERA (SOLO SI ES SALIDA/VENTA)
            if ($tipo == 'salida') {
                $sqlVenta = "INSERT INTO tb_ventas_productos 
                            (id_producto, cantidad, precio_unitario, fecha_venta, pago_efectivo, pago_tarjeta, pago_transferencia, total_venta)
                            VALUES (:id, :cant, :precio_u, CURDATE(), :efectivo, :tarjeta, :transferencia, :total)";
                
                $stmtVenta = $pdo->prepare($sqlVenta);
                $stmtVenta->execute([
                    ':id' => $id_producto,
                    ':cant' => $cantidad,
                    ':precio_u' => $precio_unitario,
                    ':efectivo' => $pago_efectivo,
                    ':tarjeta' => $pago_tarjeta,
                    ':transferencia' => $pago_transferencia,
                    ':total' => $total_pagado
                ]);
            }

            $pdo->commit();
            $_SESSION['msg'] = ($tipo == 'salida' ? 'Venta y m' : 'M') . "ovimiento registrado correctamente.";
            $_SESSION['msg_type'] = "success";

        } catch (Exception $e) {
            $pdo->rollBack();
            $_SESSION['msg'] = "Error: " . $e->getMessage();
            $_SESSION['msg_type'] = "danger";
        }
    } else {
        $_SESSION['msg'] = "La cantidad debe ser mayor a cero.";
        $_SESSION['msg_type'] = "danger";
    }
}
header("Location: ../../views/inventory/index.php");
exit;