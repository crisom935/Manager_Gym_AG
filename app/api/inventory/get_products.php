<?php
ob_clean();
header('Content-Type: application/json');
require_once '../../../config/database.php';

try {
    $sql = "SELECT * FROM tb_productos";
    $stmt = $pdo->query($sql);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['data' => $data]);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>