<?php
session_start();
require_once '../../../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btn_guardar'])) {
    
    // 0. CAPTURAR USUARIO QUE REGISTRA (ESTO FALTABA)
    // Intentamos capturar el ID de la sesión. Si no existe, será null.
    $id_usuario = $_SESSION['user_id'] ?? $_SESSION['id_usuario'] ?? null;

    // 1. Recibir Datos Generales
    $plan       = trim($_POST['plan_suscripcion']);
    $email      = trim($_POST['correo']);
    $tel        = trim($_POST['telefono']);
    $f_ini      = $_POST['fecha_inscripcion'];
    $nombres    = $_POST['nombres']; // Array de nombres

    // 2. Recibir Datos Financieros
    $monto_efectivo      = floatval($_POST['monto_efectivo']);
    $monto_tarjeta       = floatval($_POST['monto_tarjeta']);
    $monto_transferencia = floatval($_POST['monto_transferencia']); 
    
    // CAPTURA DEL DESCUENTO
    $monto_descuento     = isset($_POST['descuento']) && is_numeric($_POST['descuento']) ? floatval($_POST['descuento']) : 0.00;
    
    $monto_inscripcion   = floatval($_POST['monto_inscripcion']);
    
    // Total real pagado
    $total_real = $monto_efectivo + $monto_tarjeta + $monto_transferencia;

    // 3. Calcular Fecha de Vencimiento ($f_fin)
    $fecha_inicio = new DateTime($f_ini);
    if (stripos($plan, 'Semanal') !== false) {
        $fecha_inicio->modify('+7 days');
    } else {
        $fecha_inicio->modify('+1 month');
    }
    $f_fin = $fecha_inicio->format('Y-m-d');

    // 4. Inserción en Base de Datos
    if (!empty($nombres) && !empty($plan)) {
        try {
            $pdo->beginTransaction();

            // MODIFICACIÓN SQL: Añadimos 'id_usuario' al INSERT
            // <--- ¡FÍJATE AQUÍ! Agregamos id_usuario y :id_user
            $sql = "INSERT INTO tb_clientes 
                    (id_usuario, nombre_cliente, plan_suscripcion, correo, telefono, fecha_inscripcion, fecha_vencimiento, pago_efectivo, pago_tarjeta, pago_transferencia, descuento, total_pagado, inscripcion) 
                    VALUES (:id_user, :nombre, :plan, :correo, :tel, :fini, :ffin, :efec, :tarj, :trans, :desc, :total, :inscripcion)";
            
            $stmt = $pdo->prepare($sql);

            foreach ($nombres as $index => $nombre_cliente) {
                if(trim($nombre_cliente) != ""){
                    
                    // LÓGICA FINANCIERA (Solo al titular)
                    if ($index === 0) {
                        $pago_e     = $monto_efectivo;
                        $pago_t     = $monto_tarjeta;
                        $pago_tr    = $monto_transferencia;
                        $pago_tot   = $total_real;
                        $monto_ins  = $monto_inscripcion; 
                        $monto_desc = $monto_descuento; 
                    } else {
                        // Acompañantes en ceros
                        $pago_e     = 0;
                        $pago_t     = 0;
                        $pago_tr    = 0;
                        $pago_tot   = 0;
                        $monto_ins  = 0; 
                        $monto_desc = 0; 
                    }

                    // Ejecutar la inserción
                    $stmt->execute([
                        ':id_user'   => $id_usuario, // <--- ¡AQUÍ FALTABA EL DATO!
                        ':nombre'    => trim($nombre_cliente),
                        ':plan'      => $plan,
                        ':correo'    => $email,
                        ':tel'       => $tel,
                        ':fini'      => $f_ini,
                        ':ffin'      => $f_fin,
                        ':efec'      => $pago_e,
                        ':tarj'      => $pago_t,
                        ':trans'     => $pago_tr,
                        ':desc'      => $monto_desc, 
                        ':total'     => $pago_tot,
                        ':inscripcion' => $monto_ins
                    ]);
                }
            }

            $pdo->commit();
            
            $_SESSION['msg'] = "Venta registrada correctamente. Total: $" . number_format($total_real, 2);
            $_SESSION['msg_type'] = "success";

        } catch (PDOException $e) {
            $pdo->rollBack();
            $_SESSION['msg'] = "Error en base de datos: " . $e->getMessage();
            $_SESSION['msg_type'] = "danger";
        }
    } else {
        $_SESSION['msg'] = "Por favor complete los campos obligatorios.";
        $_SESSION['msg_type'] = "warning";
    }
}

// Redireccionar siempre
header("Location: ../../views/main/tabla_clientes.php");
exit;
?>