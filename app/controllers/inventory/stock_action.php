<?php
session_start();
require_once '../../../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_producto = $_POST['id_producto'];
    $tipo        = $_POST['tipo_movimiento']; // 'entrada' o 'salida'
    $cantidad    = intval($_POST['cantidad']);
    $nota        = trim($_POST['nota']);

    if ($cantidad > 0) {
        try {
            $pdo->beginTransaction();

            // 1. Actualizar Stock en tb_productos
            if ($tipo == 'entrada') {
                $sqlStock = "UPDATE tb_productos SET stock_actual = stock_actual + :cant WHERE id_producto = :id";
            } else {
                // Validar que haya stock suficiente
                $check = $pdo->prepare("SELECT stock_actual FROM tb_productos WHERE id_producto = :id");
                $check->execute([':id' => $id_producto]);
                $actual = $check->fetchColumn();

                if ($actual < $cantidad) {
                    throw new Exception("No hay suficiente stock para realizar la salida.");
                }
                $sqlStock = "UPDATE tb_productos SET stock_actual = stock_actual - :cant WHERE id_producto = :id";
            }
            
            $stmtStock = $pdo->prepare($sqlStock);
            $stmtStock->execute([':cant' => $cantidad, ':id' => $id_producto]);

            // 2. Registrar en Historial (tb_movimientos_inv)
            $sqlHist = "INSERT INTO tb_movimientos_inv (id_producto, tipo_movimiento, cantidad, nota) VALUES (:id, :tipo, :cant, :nota)";
            $stmtHist = $pdo->prepare($sqlHist);
            $stmtHist->execute([':id' => $id_producto, ':tipo' => $tipo, ':cant' => $cantidad, ':nota' => $nota]);

            $pdo->commit();
            $_SESSION['msg'] = "Movimiento registrado correctamente.";
            $_SESSION['msg_type'] = "success";

        } catch (Exception $e) {
            $pdo->rollBack();
            $_SESSION['msg'] = "Error: " . $e->getMessage();
            $_SESSION['msg_type'] = "danger";
        }
    }
}
header("Location: ../../views/inventory/index.php");
exit;
?>