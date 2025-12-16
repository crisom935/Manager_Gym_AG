/* ==========================================
   LÓGICA DE PRECIOS Y FORMULARIO
   ========================================== */

// INICIO CAMBIO AÑADIDO: Función auxiliar para obtener el monto requerido (Plan + Inscripción)
function getMontoRequerido() {
    // Obtiene el precio del plan seleccionado
    var selectPlan = document.getElementById("selectPlan");
    // Usamos '?' para asegurar que no falle si selectPlan no está disponible (ej. en otras vistas)
    var precioPlan = selectPlan ? (parseFloat(selectPlan.options[selectPlan.selectedIndex].getAttribute("data-precio")) || 0) : 0;
    
    // Obtiene el monto de la inscripción seleccionada
    var inscripcionSelect = document.getElementById("selectInscripcion");
    var inscripcionMonto = inscripcionSelect ? (parseFloat(inscripcionSelect.value) || 0) : 0; 
    
    return precioPlan + inscripcionMonto;
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
    // CAMBIO: Usamos el monto total requerido (Plan + Inscripción)
    var montoRequerido = getMontoRequerido();
    // FIN CAMBIO
    
    // Resetear todo a Efectivo por defecto al cambiar de plan
    document.getElementById("metodoPago").value = "Efectivo";
    
    // Efectivo activo con el precio total
    var inputEfectivo = document.getElementById("inputEfectivo");
    // CAMBIO: Asignamos el montoRequerido
    inputEfectivo.value = montoRequerido.toFixed(2);
    // FIN CAMBIO
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

    // CAMBIO: Obtenemos el monto total requerido para los modos NO Mixto
    var montoRequerido = getMontoRequerido(); 
    // FIN CAMBIO

    // Obtenemos el total actual. NOTA: Ya no lo usamos para reasignar, 
    // sino para respetar la lógica original si se necesita
    var totalActual = parseFloat(document.getElementById("labelTotal").innerText.replace('$','')) || 0;


    if (metodo === "Efectivo") {
        efec.readOnly = false;
        // CAMBIO: Usamos el monto requerido (Plan + Inscripción)
        efec.value = montoRequerido.toFixed(2);
        // FIN CAMBIO
        tarj.value = 0;
        trans.value = 0;
    } 
    else if (metodo === "Tarjeta") {
        tarj.readOnly = false;
        // CAMBIO: Usamos el monto requerido (Plan + Inscripción)
        tarj.value = montoRequerido.toFixed(2);
        // FIN CAMBIO
        efec.value = 0;
        trans.value = 0;
    } 
    else if (metodo === "Transferencia") {
        trans.readOnly = false;
        // CAMBIO: Usamos el monto requerido (Plan + Inscripción)
        trans.value = montoRequerido.toFixed(2);
        // FIN CAMBIO
        efec.value = 0;
        tarj.value = 0;
    } 
    else { // Mixto
        // En mixto desbloqueamos todo y dejamos que el usuario escriba los montos
        efec.readOnly = false;
        tarj.readOnly = false;
        trans.readOnly = false;
        
        // Nota: En Mixto, los inputs deben ser editados por el usuario para sumar el montoRequerido.
    }
    calcularTotal();
}

// 3. Sumar visualmente para confirmar
function calcularTotal() {
    var e = parseFloat(document.getElementById("inputEfectivo").value) || 0;
    var t = parseFloat(document.getElementById("inputTarjeta").value) || 0;
    var tr = parseFloat(document.getElementById("inputTransferencia").value) || 0;
    
    var total = e + t + tr;
    // NOTA: No necesitamos sumar la inscripción aquí, ya que la función ajustarInputsPago() 
    // se encarga de que el input activo (Efectivo/Tarjeta/Transf) contenga el monto total requerido (Plan + Inscripción) 
    // cuando se selecciona un método de pago simple.
    document.getElementById("labelTotal").innerText = "$" + total.toFixed(2);
}

// 4. Listeners al cargar el documento (para que funcione la suma manual)
document.addEventListener("DOMContentLoaded", function() {
    var inEfectivo = document.getElementById("inputEfectivo");
    var inTarjeta = document.getElementById("inputTarjeta");
    var inTrans = document.getElementById("inputTransferencia");
    
    // INICIO CAMBIO AÑADIDO: Listener para el nuevo campo de inscripción
    var selectInscripcion = document.getElementById("selectInscripcion");
    // FIN CAMBIO AÑADIDO

    if(inEfectivo) inEfectivo.addEventListener('input', calcularTotal);
    if(inTarjeta) inTarjeta.addEventListener('input', calcularTotal);
    if(inTrans) inTrans.addEventListener('input', calcularTotal);
    
    // INICIO CAMBIO AÑADIDO: Al cambiar el valor de Inscripción, ajustamos los inputs de pago.
    // Esto recalcula el monto requerido (Plan + Inscripción) y lo reasigna al input de pago.
    if(selectInscripcion) selectInscripcion.addEventListener('change', function() {
        ajustarInputsPago(); 
    });
    // FIN CAMBIO AÑADIDO
});