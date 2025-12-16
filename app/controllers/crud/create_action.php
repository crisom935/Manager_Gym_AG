<?php
session_start();
require_once '../../../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btn_guardar'])) {
    
    // 1. Recibir Datos Generales
    $plan       = trim($_POST['plan_suscripcion']);
    $email      = trim($_POST['correo']);
    $tel        = trim($_POST['telefono']);
    $f_ini      = $_POST['fecha_inscripcion'];
    $nombres    = $_POST['nombres']; // Array de nombres

    // 2. Recibir Datos Financieros
    // Usamos floatval para asegurar que sean números (si vienen vacíos serán 0)
    $monto_efectivo      = floatval($_POST['monto_efectivo']);
    $monto_tarjeta       = floatval($_POST['monto_tarjeta']);
    $monto_transferencia = floatval($_POST['monto_transferencia']); 

    // Sumamos los 3 montos para el total real
    $total_real = $monto_efectivo + $monto_tarjeta + $monto_transferencia;

    // 3. Calcular Fecha de Vencimiento ($f_fin)
    // Esto faltaba en tu código y es necesario para la base de datos
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
            // AQUI FALTABA EL "try {"
            $pdo->beginTransaction();

            $sql = "INSERT INTO tb_clientes 
                    (nombre_cliente, plan_suscripcion, correo, telefono, fecha_inscripcion, fecha_vencimiento, pago_efectivo, pago_tarjeta, pago_transferencia, total_pagado) 
                    VALUES (:nombre, :plan, :correo, :tel, :fini, :ffin, :efec, :tarj, :trans, :total)";
            
            $stmt = $pdo->prepare($sql);

            foreach ($nombres as $index => $nombre_cliente) {
                if(trim($nombre_cliente) != ""){
                    
                    // LÓGICA FINANCIERA:
                    // Solo asignamos el dinero al primer registro (Titular index 0)
                    if ($index === 0) {
                        $pago_e   = $monto_efectivo;
                        $pago_t   = $monto_tarjeta;
                        $pago_tr  = $monto_transferencia;
                        $pago_tot = $total_real;
                    } else {
                        $pago_e   = 0;
                        $pago_t   = 0;
                        $pago_tr  = 0;
                        $pago_tot = 0;
                    }

                    $stmt->execute([
                        ':nombre' => trim($nombre_cliente),
                        ':plan'   => $plan,
                        ':correo' => $email,
                        ':tel'    => $tel,
                        ':fini'   => $f_ini,
                        ':ffin'   => $f_fin,
                        ':efec'   => $pago_e,
                        ':tarj'   => $pago_t,
                        ':trans'  => $pago_tr,
                        ':total'  => $pago_tot
                    ]);
                }
            }

            $pdo->commit();
            
            $_SESSION['msg'] = "Venta registrada correctamente. Total: $" . number_format($total_real, 2);
            $_SESSION['msg_type'] = "success";

        } catch (PDOException $e) {
            // Si hay error, deshacemos los cambios
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