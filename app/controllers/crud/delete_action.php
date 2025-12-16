<?php
session_start();
// Ajusta la ruta para llegar a config (subir 3 niveles desde controllers/crud)
require_once '../../../config/database.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        $sql = "DELETE FROM tb_clientes WHERE id_cliente = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);

        $_SESSION['msg'] = "Registro eliminado correctamente.";
        $_SESSION['msg_type'] = "success";

    } catch (PDOException $e) {
        $_SESSION['msg'] = "Error al eliminar: " . $e->getMessage();
        $_SESSION['msg_type'] = "danger";
    }
}

// Redireccionar a la tabla (ajusta la ruta de regreso a la vista)
header("Location: ../../views/main/tabla_clientes.php");
exit;
?>