/* ==========================================
   LÓGICA DE PRECIOS Y FORMULARIO
   ========================================== */

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
    var precio = parseFloat(opcion.getAttribute("data-precio")) || 0;
    
    // Resetear todo a Efectivo por defecto al cambiar de plan
    document.getElementById("metodoPago").value = "Efectivo";
    
    // Efectivo activo con el precio total
    var inputEfectivo = document.getElementById("inputEfectivo");
    inputEfectivo.value = precio;
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

    // Obtener el total actual que se estaba mostrando para moverlo al nuevo método
    var totalActual = parseFloat(document.getElementById("labelTotal").innerText.replace('$','')) || 0;

    if (metodo === "Efectivo") {
        efec.readOnly = false;
        efec.value = totalActual;
        tarj.value = 0;
        trans.value = 0;
    } 
    else if (metodo === "Tarjeta") {
        tarj.readOnly = false;
        tarj.value = totalActual;
        efec.value = 0;
        trans.value = 0;
    } 
    else if (metodo === "Transferencia") {
        trans.readOnly = false;
        trans.value = totalActual;
        efec.value = 0;
        tarj.value = 0;
    } 
    else { // Mixto
        // En mixto desbloqueamos todo y dejamos que el usuario escriba los montos
        efec.readOnly = false;
        tarj.readOnly = false;
        trans.readOnly = false;
    }
    calcularTotal();
}

// 3. Sumar visualmente para confirmar
function calcularTotal() {
    var e = parseFloat(document.getElementById("inputEfectivo").value) || 0;
    var t = parseFloat(document.getElementById("inputTarjeta").value) || 0;
    var tr = parseFloat(document.getElementById("inputTransferencia").value) || 0;
    
    var total = e + t + tr;
    document.getElementById("labelTotal").innerText = "$" + total.toFixed(2);
}

// 4. Listeners al cargar el documento (para que funcione la suma manual)
document.addEventListener("DOMContentLoaded", function() {
    var inEfectivo = document.getElementById("inputEfectivo");
    var inTarjeta = document.getElementById("inputTarjeta");
    var inTrans = document.getElementById("inputTransferencia");

    if(inEfectivo) inEfectivo.addEventListener('input', calcularTotal);
    if(inTarjeta) inTarjeta.addEventListener('input', calcularTotal);
    if(inTrans) inTrans.addEventListener('input', calcularTotal);
});