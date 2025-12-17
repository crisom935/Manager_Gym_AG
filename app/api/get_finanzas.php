<?php
require_once '../../config/database.php';

header('Content-Type: application/json');

$mes_anio = $_GET['mes_anio'] ?? date('Y-m'); // 'YYYY-MM'
$agrupacion = $_GET['agrupacion'] ?? 'day';   // 'day', 'week', 'month'

try {
    $date_format_clientes = '';
    $date_format_productos = '';
    $where_clause_clientes = " WHERE 1 ";
    $where_clause_productos = " WHERE 1 ";
    $is_filtered_by_month = false;

    switch ($agrupacion) {
        case 'day':
            $is_filtered_by_month = true;
            $date_format_clientes = "DATE_FORMAT(fecha_inscripcion, '%Y-%m-%d')";
            $date_format_productos = "DATE_FORMAT(fecha_venta, '%Y-%m-%d')";
            $where_clause_clientes = " WHERE DATE_FORMAT(fecha_inscripcion, '%Y-%m') = :mes_anio_cl ";
            $where_clause_productos = " WHERE DATE_FORMAT(fecha_venta, '%Y-%m') = :mes_anio_pr ";
            break;

        case 'week':
            $is_filtered_by_month = true;
            $date_format_clientes = "CONCAT(YEAR(fecha_inscripcion), '-', WEEK(fecha_inscripcion, 1))";
            $date_format_productos = "CONCAT(YEAR(fecha_venta), '-', WEEK(fecha_venta, 1))";
            $where_clause_clientes = " WHERE DATE_FORMAT(fecha_inscripcion, '%Y-%m') = :mes_anio_cl ";
            $where_clause_productos = " WHERE DATE_FORMAT(fecha_venta, '%Y-%m') = :mes_anio_pr ";
            break;

        case 'month':
        default:
            $date_format_clientes = "DATE_FORMAT(fecha_inscripcion, '%Y-%m')";
            $date_format_productos = "DATE_FORMAT(fecha_venta, '%Y-%m')";
            break;
    }

    $sql = "
        SELECT
            subquery_combined.periodo AS fecha,
            SUM(subquery_combined.num_personas) AS personas,
            SUM(subquery_combined.total_efectivo) AS total_efectivo,
            SUM(subquery_combined.total_tarjeta) AS total_tarjeta,
            SUM(subquery_combined.total_transferencia) AS total_transferencia,
            SUM(subquery_combined.gran_total) AS gran_total
        FROM (
            SELECT
                {$date_format_clientes} AS periodo,
                1 AS num_personas,
                SUM(pago_efectivo) AS total_efectivo,
                SUM(pago_tarjeta) AS total_tarjeta,
                SUM(pago_transferencia) AS total_transferencia,
                SUM(pago_efectivo + pago_tarjeta + pago_transferencia) AS gran_total
            FROM tb_clientes
            {$where_clause_clientes}
            GROUP BY periodo

            UNION ALL

            SELECT
                {$date_format_productos} AS periodo,
                0 AS num_personas,
                SUM(pago_efectivo) AS total_efectivo,
                SUM(pago_tarjeta) AS total_tarjeta,
                SUM(pago_transferencia) AS total_transferencia,
                SUM(total_venta) AS gran_total
            FROM tb_ventas_productos
            {$where_clause_productos}
            GROUP BY periodo
        ) AS subquery_combined
        GROUP BY subquery_combined.periodo
        ORDER BY subquery_combined.periodo ASC
    ";

    $stmt = $pdo->prepare($sql);

    $params = [];

    if ($is_filtered_by_month) {
        $params[':mes_anio_cl'] = $mes_anio;
        $params[':mes_anio_pr'] = $mes_anio;
    }

    $stmt->execute($params);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['data' => $data]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'General error: ' . $e->getMessage()]);
}
?>
