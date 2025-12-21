/* ==========================================
    LÓGICA DE PRECIOS, NOMBRES Y VALIDACIÓN
   ========================================== */

// 1. Obtener el monto neto (Precio + Inscripción - Descuento)
function getMontoRequerido() {
    var selectPlan = document.getElementById("selectPlan");
    var precioPlan = selectPlan ? (parseFloat(selectPlan.options[selectPlan.selectedIndex].getAttribute("data-precio")) || 0) : 0;
    
    var inscripcionSelect = document.getElementById("selectInscripcion");
    var inscripcionMonto = inscripcionSelect ? (parseFloat(inscripcionSelect.value) || 0) : 0; 
    
    var inputDescuento = document.getElementById("descuento");
    var montoDescuento = inputDescuento ? (parseFloat(inputDescuento.value) || 0) : 0;
    
    var montoNeto = (precioPlan + inscripcionMonto) - montoDescuento;
    return montoNeto > 0 ? montoNeto : 0;
}

// 2. Generar inputs de nombres (¡ESTO ES LO QUE TE FALTABA!)
function generarInputsNombres() {
    var select = document.getElementById("selectPlan");
    var contenedor = document.getElementById("contenedorNombres");
    var opcion = select.options[select.selectedIndex];
    var cantidad = opcion.getAttribute("data-cantidad");
    
    if (!cantidad) return;

    // Limpiamos y generamos los campos según el plan (1, 2, 3 o 4)
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

    // Al cambiar de plan, también reseteamos el pago a "Efectivo" y recalculamos
    document.getElementById("metodoPago").value = "Efectivo";
    ajustarInputsPago();
}

// 3. Ajustar los inputs de dinero
function ajustarInputsPago() {
    var metodo = document.getElementById("metodoPago").value;
    var efec = document.getElementById("inputEfectivo");
    var tarj = document.getElementById("inputTarjeta");
    var trans = document.getElementById("inputTransferencia");
    var montoNetoRequerido = getMontoRequerido(); 

    efec.readOnly = true;
    tarj.readOnly = true;
    trans.readOnly = true;

    if (metodo === "Efectivo") {
        efec.value = montoNetoRequerido.toFixed(2);
        tarj.value = "0.00"; trans.value = "0.00";
    } 
    else if (metodo === "Tarjeta") {
        tarj.value = montoNetoRequerido.toFixed(2);
        efec.value = "0.00"; trans.value = "0.00";
    } 
    else if (metodo === "Transferencia") {
        trans.value = montoNetoRequerido.toFixed(2);
        efec.value = "0.00"; tarj.value = "0.00";
    } 
    else if (metodo === "Mixto") {
        efec.readOnly = false;
        tarj.readOnly = false;
        trans.readOnly = false;
        
        // Si el total actual no cuadra con el nuevo descuento/plan, reseteamos al efectivo
        var sumaActual = (parseFloat(efec.value) || 0) + (parseFloat(tarj.value) || 0) + (parseFloat(trans.value) || 0);
        if (Math.abs(sumaActual - montoNetoRequerido) > 0.1) {
            efec.value = montoNetoRequerido.toFixed(2);
            tarj.value = "0.00";
            trans.value = "0.00";
        }
    }
    calcularTotal();
}

// 4. Calcular total visual y alertar error
function calcularTotal() {
    var e = parseFloat(document.getElementById("inputEfectivo").value) || 0;
    var t = parseFloat(document.getElementById("inputTarjeta").value) || 0;
    var tr = parseFloat(document.getElementById("inputTransferencia").value) || 0;
    var requerido = getMontoRequerido();
    
    var totalIngresado = e + t + tr;
    var label = document.getElementById("labelTotal");
    
    label.innerText = "$" + totalIngresado.toFixed(2);

    // Si no cuadra, ponemos el total en rojo
    if (Math.abs(totalIngresado - requerido) > 0.01) {
        label.classList.replace("text-white", "text-danger");
    } else {
        label.classList.replace("text-danger", "text-white");
    }
}

// 5. Listeners y Validaciones al cargar
document.addEventListener("DOMContentLoaded", function() {
    // Listeners para inputs manuales
    ["inputEfectivo", "inputTarjeta", "inputTransferencia"].forEach(id => {
        document.getElementById(id).addEventListener('input', calcularTotal);
    });

    // Listener para el descuento
    var inputDesc = document.getElementById("descuento");
    if(inputDesc) {
        inputDesc.addEventListener('input', ajustarInputsPago);
    }

    // VALIDACIÓN CRÍTICA ANTES DE ENVIAR
    var form = document.querySelector("form");
    if(form) {
        form.onsubmit = function(e) {
            var total = (parseFloat(document.getElementById("inputEfectivo").value) || 0) + 
                        (parseFloat(document.getElementById("inputTarjeta").value) || 0) + 
                        (parseFloat(document.getElementById("inputTransferencia").value) || 0);
            var neto = getMontoRequerido();
            
            if (Math.abs(total - neto) > 0.1) {
                alert("¡Atención!\nLa suma de los pagos ($" + total.toFixed(2) + ") no coincide con el total con descuento ($" + neto.toFixed(2) + "). Por favor ajusta los montos.");
                e.preventDefault();
                return false;
            }
        };
    }
});

// 6. Validación de nombres: solo letras y espacios
document.addEventListener('input', function (e) {
    // Verificamos si el input que disparó el evento es uno de los nombres
    if (e.target && e.target.name === 'nombres[]') {
        // Reemplazamos cualquier cosa que NO sea letra o espacio
        e.target.value = e.target.value.replace(/[^a-zA-ZñÑáéíóúÁÉÍÓÚ\s]/g, '');
    }
});