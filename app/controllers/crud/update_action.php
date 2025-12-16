<?php
session_start();
require_once '../../../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id     = $_POST['id_cliente'];
    $nombre = trim($_POST['nombre_cliente']);
    $email  = trim($_POST['correo']);
    $tel    = trim($_POST['telefono']);
    $plan   = $_POST['plan_suscripcion'];
    $vence  = $_POST['fecha_vencimiento'];

    try {
        $sql = "UPDATE tb_clientes SET 
                nombre_cliente = :nombre,
                correo = :email,
                telefono = :tel,
                plan_suscripcion = :plan,
                fecha_vencimiento = :vence
                WHERE id_cliente = :id";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nombre' => $nombre,
            ':email' => $email,
            ':tel' => $tel,
            ':plan' => $plan,
            ':vence' => $vence,
            ':id' => $id
        ]);

        $_SESSION['msg'] = "Cliente actualizado correctamente.";
        $_SESSION['msg_type'] = "success";

    } catch (PDOException $e) {
        $_SESSION['msg'] = "Error al actualizar: " . $e->getMessage();
        $_SESSION['msg_type'] = "danger";
    }
}
// Regresar al directorio
header("Location: ../../views/directory/index.php");
exit;
?>