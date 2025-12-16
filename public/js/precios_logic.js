/* ==========================================
    LÓGICA DE PRECIOS Y FORMULARIO
    ========================================== */

// INICIO CAMBIO AÑADIDO: Función auxiliar para obtener el monto NETO REQUERIDO (Plan + Inscripción - Descuento)
function getMontoRequerido() {
    // 1. Obtiene el precio del plan seleccionado (MONTO BASE)
    var selectPlan = document.getElementById("selectPlan");
    // Usamos '?' para asegurar que no falle si selectPlan no está disponible (ej. en otras vistas)
    var precioPlan = selectPlan ? (parseFloat(selectPlan.options[selectPlan.selectedIndex].getAttribute("data-precio")) || 0) : 0;
    
    // 2. Obtiene el monto de la inscripción seleccionada
    var inscripcionSelect = document.getElementById("selectInscripcion");
    var inscripcionMonto = inscripcionSelect ? (parseFloat(inscripcionSelect.value) || 0) : 0; 
    
    // 3. Obtiene el monto del descuento (NUEVO)
    var inputDescuento = document.getElementById("descuento");
    // Usamos 0 si el campo no existe o no es un número válido
    var montoDescuento = inputDescuento ? (parseFloat(inputDescuento.value) || 0) : 0;
    
    // 4. Calcula el monto Neto (Bruto - Descuento)
    var montoNeto = (precioPlan + inscripcionMonto) - montoDescuento;
    
    // Aseguramos que no se muestren valores negativos
    return montoNeto > 0 ? montoNeto : 0;
}
// FIN CAMBIO AÑADIDO

// 1. Generar inputs de nombres y Actualizar Precios
function generarInputsNombres() {
    var select = document.getElementById("selectPlan");
    var contenedor = document.getElementById("contenedorNombres");
    var opcion = select.options[select.selectedIndex];
    
    // --- A. Lógica de Inputs de Nombres ---
    var cantidad = opcion.getAttribute("data-cantidad");
    
    // Si no hay selección válida (ej. el placeholder), salimos
    if (!cantidad) return;

    contenedor.innerHTML = '<label class="form-label small text-muted">Nombres (' + cantidad + ') * <small class="text-white-50 ms-1">El primero es el Titular</small></label>';

    for (var i = 1; i <= cantidad; i++) {
        var input = document.createElement("input");
        input.type = "text";
        input.name = "nombres[]"; 
        input.className = "form-control mb-2";
        input.placeholder = i === 1 ? "Titular (Paga)" : "Acompañante #" + (i-1);
        input.required = true;
        contenedor.appendChild(input);
    }

    // --- B. Lógica de Precios (Autocompletar) ---
    // Ahora, getMontoRequerido() ya incluye la resta del descuento.
    var montoNetoRequerido = getMontoRequerido();
    
    // Resetear todo a Efectivo por defecto al cambiar de plan
    document.getElementById("metodoPago").value = "Efectivo";
    
    // Efectivo activo con el precio total (NETO)
    var inputEfectivo = document.getElementById("inputEfectivo");
    inputEfectivo.value = montoNetoRequerido.toFixed(2);
    inputEfectivo.readOnly = false;
    
    // Tarjeta en 0 y bloqueada
    var inputTarjeta = document.getElementById("inputTarjeta");
    inputTarjeta.value = 0;
    inputTarjeta.readOnly = true;
    
    // Transferencia en 0 y bloqueada
    var inputTransferencia = document.getElementById("inputTransferencia");
    inputTransferencia.value = 0;
    inputTransferencia.readOnly = true;
    
    // Recalcular el total visual
    calcularTotal();
}

// 2. Controlar inputs según método de pago (Efectivo/Tarjeta/Transferencia/Mixto)
function ajustarInputsPago() {
    var metodo = document.getElementById("metodoPago").value;
    var efec = document.getElementById("inputEfectivo");
    var tarj = document.getElementById("inputTarjeta");
    var trans = document.getElementById("inputTransferencia");

    // Reiniciar readonlys (bloquear todos primero)
    efec.readOnly = true;
    tarj.readOnly = true;
    trans.readOnly = true;

    // Obtenemos el monto total requerido NETO (ya con descuento restado)
    var montoNetoRequerido = getMontoRequerido(); 

    if (metodo === "Efectivo") {
        efec.readOnly = false;
        // Asignamos el monto NETO requerido
        efec.value = montoNetoRequerido.toFixed(2);
        tarj.value = 0;
        trans.value = 0;
    } 
    else if (metodo === "Tarjeta") {
        tarj.readOnly = false;
        // Asignamos el monto NETO requerido
        tarj.value = montoNetoRequerido.toFixed(2);
        efec.value = 0;
        trans.value = 0;
    } 
    else if (metodo === "Transferencia") {
        trans.readOnly = false;
        // Asignamos el monto NETO requerido
        trans.value = montoNetoRequerido.toFixed(2);
        efec.value = 0;
        tarj.value = 0;
    } 
    else { // Mixto
        // En mixto desbloqueamos todo, pero solo si el total actual es 0 (para no borrar un monto escrito)
        efec.readOnly = false;
        tarj.readOnly = false;
        trans.readOnly = false;
        
        // Si el total actual de los campos de pago es 0, asignamos el total neto al efectivo para empezar
        var totalActualPagos = parseFloat(efec.value) + parseFloat(tarj.value) + parseFloat(trans.value);
        if (totalActualPagos === 0) {
            efec.value = montoNetoRequerido.toFixed(2);
        }
    }
    calcularTotal();
}

// 3. Sumar visualmente para confirmar
function calcularTotal() {
    var e = parseFloat(document.getElementById("inputEfectivo").value) || 0;
    var t = parseFloat(document.getElementById("inputTarjeta").value) || 0;
    var tr = parseFloat(document.getElementById("inputTransferencia").value) || 0;
    
    var total = e + t + tr;
    // La suma de los métodos de pago es lo que realmente se pagó. 
    // Esto coincide con $total_real en create_action.php.
    document.getElementById("labelTotal").innerText = "$" + total.toFixed(2);
}

// 4. Listeners al cargar el documento
document.addEventListener("DOMContentLoaded", function() {
    var inEfectivo = document.getElementById("inputEfectivo");
    var inTarjeta = document.getElementById("inputTarjeta");
    var inTrans = document.getElementById("inputTransferencia");
    var selectInscripcion = document.getElementById("selectInscripcion");
    // NUEVO: Capturar el input de descuento
    var inputDescuento = document.getElementById("descuento"); 

    if(inEfectivo) inEfectivo.addEventListener('input', calcularTotal);
    if(inTarjeta) inTarjeta.addEventListener('input', calcularTotal);
    if(inTrans) inTrans.addEventListener('input', calcularTotal);
    
    // Al cambiar el valor de Inscripción, ajustamos los inputs de pago.
    if(selectInscripcion) selectInscripcion.addEventListener('change', function() {
        ajustarInputsPago(); 
    });
    
    // NUEVO LISTENER: Al cambiar el valor del Descuento, ajustamos los inputs de pago.
    if(inputDescuento) inputDescuento.addEventListener('input', function() {
        ajustarInputsPago(); 
    });
});