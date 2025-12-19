<?php
require_once '../../config/database.php';

header('Content-Type: application/json');
error_reporting(0);

$mes_anio = $_GET['mes_anio'] ?? date('Y-m'); 
list($anio, $mes) = explode('-', $mes_anio);

try {
    $diasEnMes = cal_days_in_month(CAL_GREGORIAN, $mes, $anio);
    $reporte = [];
    
    // Estructuras base
    $base_moneda = ['personas' => 0, 'efectivo' => 0, 'tarjeta' => 0, 'transf' => 0, 'total' => 0];
    
    // Totales Mensuales (Acumulador)
    $meta_mensual = [
        'gran_total' => 0, 'inscripciones' => 0, 'productos' => 0,
        'efectivo' => 0, 'tarjeta' => 0, 'transferencia' => 0,
        'total_personas' => 0 // Nuevo campo
    ];

    // Totales Semanales (Array de acumuladores)
    $meta_semanal = []; 

    for ($d = 1; $d <= $diasEnMes; $d++) {
        $fecha = sprintf("%04d-%02d-%02d", $anio, $mes, $d);
        
        // Calcular número de semana (Semana 1, 2, 3...) del mes
        // Usamos week number ISO o una logica simple de división por 7 días si prefieres bloques fijos.
        // Para ser precisos con calendario:
        $numSemana = ceil($d / 7); // Semana 1 (dias 1-7), Semana 2 (8-14)... Aproximación simple para reporte AG
        // O si prefieres semana de calendario real (Lunes-Domingo):
        // $numSemana = date('W', strtotime($fecha)); 
        
        $keySemana = "Semana " . $numSemana;

        // Inicializar acumulador semanal si no existe
        if (!isset($meta_semanal[$keySemana])) {
            $meta_semanal[$keySemana] = [
                'rango' => '', // Lo llenaremos luego
                'gran_total' => 0, 'inscripciones' => 0, 'productos' => 0,
                'efectivo' => 0, 'tarjeta' => 0, 'transferencia' => 0,
                'total_personas' => 0
            ];
        }

        // Inicializar Día
        $reporte[$fecha] = [
            'fecha_humana' => obtenerFechaHumana($fecha),
            'semana' => $keySemana, // Para filtrar en JS
            'paquetes' => [
                'Individual Semanal' => $base_moneda,
                'Individual Mensual' => $base_moneda,
                'Paquete Amigos'     => $base_moneda,
                'Familiar #1'        => $base_moneda,
                'Familiar #2'        => $base_moneda
            ],
            'productos' => [
                'Aguas'  => ['cantidad' => 0, 'efectivo' => 0, 'tarjeta' => 0, 'transf' => 0, 'total' => 0],
                'Vendas' => ['cantidad' => 0, 'efectivo' => 0, 'tarjeta' => 0, 'transf' => 0, 'total' => 0]
            ],
            'total_dia' => 0
        ];
    }

    // --- CONSULTA CLIENTES ---
    $sqlClientes = "SELECT DATE(fecha_inscripcion) as fecha, plan_suscripcion, 
                    pago_efectivo, pago_tarjeta, pago_transferencia 
                    FROM tb_clientes WHERE DATE_FORMAT(fecha_inscripcion, '%Y-%m') = :mes";
    $stmtC = $pdo->prepare($sqlClientes);
    $stmtC->execute([':mes' => $mes_anio]);
    $clientes = $stmtC->fetchAll(PDO::FETCH_ASSOC);

    foreach ($clientes as $row) {
        $fecha = $row['fecha'];
        $plan = $row['plan_suscripcion'];
        $key = 'Otros'; // Mapeo (igual que antes)
        if (stripos($plan, 'Semanal') !== false) $key = 'Individual Semanal';
        elseif (stripos($plan, 'Mensual') !== false) $key = 'Individual Mensual';
        elseif (stripos($plan, 'Amigos') !== false) $key = 'Paquete Amigos';
        elseif (stripos($plan, 'Familiar #1') !== false) $key = 'Familiar #1';
        elseif (stripos($plan, 'Familiar #2') !== false) $key = 'Familiar #2';

        if (isset($reporte[$fecha])) {
            $total_row = $row['pago_efectivo'] + $row['pago_tarjeta'] + $row['pago_transferencia'];
            $sem = $reporte[$fecha]['semana'];

            // 1. Sumar al Día
            $reporte[$fecha]['paquetes'][$key]['personas'] += 1; 
            $reporte[$fecha]['paquetes'][$key]['efectivo'] += $row['pago_efectivo'];
            $reporte[$fecha]['paquetes'][$key]['tarjeta'] += $row['pago_tarjeta'];
            $reporte[$fecha]['paquetes'][$key]['transf'] += $row['pago_transferencia'];
            $reporte[$fecha]['paquetes'][$key]['total'] += $total_row;
            $reporte[$fecha]['total_dia'] += $total_row;

            // 2. Sumar al Mes
            $meta_mensual['inscripciones'] += $total_row;
            $meta_mensual['gran_total'] += $total_row;
            $meta_mensual['efectivo'] += $row['pago_efectivo'];
            $meta_mensual['tarjeta'] += $row['pago_tarjeta'];
            $meta_mensual['transferencia'] += $row['pago_transferencia'];
            $meta_mensual['total_personas'] += 1;

            // 3. Sumar a la Semana
            $meta_semanal[$sem]['inscripciones'] += $total_row;
            $meta_semanal[$sem]['gran_total'] += $total_row;
            $meta_semanal[$sem]['efectivo'] += $row['pago_efectivo'];
            $meta_semanal[$sem]['tarjeta'] += $row['pago_tarjeta'];
            $meta_semanal[$sem]['transferencia'] += $row['pago_transferencia'];
            $meta_semanal[$sem]['total_personas'] += 1;
        }
    }

    // --- CONSULTA PRODUCTOS ---
    $sqlProd = "SELECT v.fecha_venta, p.nombre_producto, v.cantidad, v.total_venta,
                v.pago_efectivo, v.pago_tarjeta, v.pago_transferencia
                FROM tb_ventas_productos v JOIN tb_productos p ON v.id_producto = p.id_producto
                WHERE DATE_FORMAT(v.fecha_venta, '%Y-%m') = :mes";
    $stmtP = $pdo->prepare($sqlProd);
    $stmtP->execute([':mes' => $mes_anio]);
    $productos = $stmtP->fetchAll(PDO::FETCH_ASSOC);

    foreach ($productos as $row) {
        $fecha = $row['fecha_venta'];
        $nombre = $row['nombre_producto'];
        $key = '';
        if (stripos($nombre, 'Agua') !== false) $key = 'Aguas';
        elseif (stripos($nombre, 'Venda') !== false) $key = 'Vendas';

        if ($key && isset($reporte[$fecha])) {
            $sem = $reporte[$fecha]['semana'];
            
            // 1. Día
            $reporte[$fecha]['productos'][$key]['cantidad'] += $row['cantidad'];
            $reporte[$fecha]['productos'][$key]['efectivo'] += $row['pago_efectivo'];
            $reporte[$fecha]['productos'][$key]['tarjeta'] += $row['pago_tarjeta'];
            $reporte[$fecha]['productos'][$key]['transf'] += $row['pago_transferencia'];
            $reporte[$fecha]['productos'][$key]['total'] += $row['total_venta'];
            $reporte[$fecha]['total_dia'] += $row['total_venta'];

            // 2. Mes
            $meta_mensual['productos'] += $row['total_venta'];
            $meta_mensual['gran_total'] += $row['total_venta'];
            $meta_mensual['efectivo'] += $row['pago_efectivo'];
            $meta_mensual['tarjeta'] += $row['pago_tarjeta'];
            $meta_mensual['transferencia'] += $row['pago_transferencia'];
            // Nota: No sumamos personas en productos
            
            // 3. Semana
            $meta_semanal[$sem]['productos'] += $row['total_venta'];
            $meta_semanal[$sem]['gran_total'] += $row['total_venta'];
            $meta_semanal[$sem]['efectivo'] += $row['pago_efectivo'];
            $meta_semanal[$sem]['tarjeta'] += $row['pago_tarjeta'];
            $meta_semanal[$sem]['transferencia'] += $row['pago_transferencia'];
        }
    }

    echo json_encode([
        'meta_mensual' => $meta_mensual,
        'meta_semanal' => $meta_semanal, // Objeto con totales por semana
        'dias' => array_values($reporte)
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

function obtenerFechaHumana($fecha) {
    $dias = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
    $meses = ['','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
    $ts = strtotime($fecha);
    return $dias[date('w', $ts)] . " " . date('j', $ts) . " de " . $meses[date('n', $ts)];
}
?>