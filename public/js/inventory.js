let productosData = []; // Global
let tipoMovimientoSelect; // Global
let cantidadInput; // Global
let efecInput; // Global
let tarjInput; // Global
let transInput; // Global

// FUNCIÓN GLOBAL para calcular el monto requerido y validar el pago
function calcularMontoVenta() {
    // Aseguramos que los elementos existan antes de usarlos, en caso de que se llame antes de DOMContentLoaded
    if (!tipoMovimientoSelect || tipoMovimientoSelect.value !== 'salida') return; 

    const precio = parseFloat(document.getElementById('modalPrecioUnitario').value) || 0;
    const cantidad = parseInt(document.getElementById('inputCantidad').value) || 0;
    
    const montoRequerido = precio * cantidad;
    
    document.getElementById('labelMontoRequerido').innerText = montoRequerido.toFixed(2);
    
    // Sumar pagos recibidos
    const pagoEfectivo = parseFloat(efecInput.value) || 0;
    const pagoTarjeta = parseFloat(tarjInput.value) || 0;
    const pagoTransferencia = parseFloat(transInput.value) || 0;
    
    const totalPagado = pagoEfectivo + pagoTarjeta + pagoTransferencia;
    const diferencia = montoRequerido - totalPagado;
    
    const alertDiferencia = document.getElementById('alertDiferencia');
    const btnGuardar = document.getElementById('btnGuardarMovimiento');
    
    if (diferencia > 0.001) { 
        alertDiferencia.style.display = 'block';
        document.getElementById('montoFaltante').innerText = diferencia.toFixed(2);
        btnGuardar.disabled = true;
    } else {
        alertDiferencia.style.display = 'none';
        btnGuardar.disabled = (totalPagado === 0 && montoRequerido > 0); 
    }
}


document.addEventListener("DOMContentLoaded", function() {
    // 1. Fetch de productos y renderizado
    fetch('/proyectos/ClientManager/app/api/inventory/get_products.php')
        .then(res => res.json())
        .then(json => {
            productosData = json.data; 
            const grid = document.getElementById('gridProductos');
            grid.innerHTML = '';
            
            json.data.forEach(p => {
                grid.innerHTML += `
                    <div class="col-md-4 col-lg-3">
                        <div class="card h-100 bg-dark border-secondary shadow-sm">
                            <div class="card-body text-center">
                                <div class="mb-3 text-danger display-4">
                                    <i class="bi ${p.nombre_producto.includes('Agua') ? 'bi-droplet-fill' : 'bi-bandaid-fill'}"></i>
                                </div>
                                <h5 class="card-title fw-bold text-white">${p.nombre_producto}</h5>
                                <h2 class="fw-bold my-3 ${p.stock_actual < 5 ? 'text-danger' : 'text-success'}">${p.stock_actual}</h2>
                                <p class="text-muted small">En existencia</p>
                                <div class="badge bg-secondary mb-3">$${p.precio_venta} MXN</div>
                                <div class="d-grid">
                                    <button class="btn btn-outline-light btn-sm" onclick="abrirModal(${p.id_producto}, '${p.nombre_producto}', ${p.precio_venta})">
                                        <i class="bi bi-arrow-left-right me-2"></i> Ajustar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
        });
        
    // 2. Listeners y Lógica de Pago
    // ASIGNAMOS LAS VARIABLES GLOBALES DE LOS INPUTS DENTRO DE DOMContentLoaded
    tipoMovimientoSelect = document.getElementById('selectTipoMovimiento');
    cantidadInput = document.getElementById('inputCantidad');
    efecInput = document.getElementById('inputEfectivo');
    tarjInput = document.getElementById('inputTarjeta');
    transInput = document.getElementById('inputTransferencia');
    
    // Función que maneja la visibilidad de la sección de pago y el cálculo
    function handleModalChange() {
        const isSalida = tipoMovimientoSelect.value === 'salida';
        document.getElementById('paymentSection').style.display = isSalida ? 'block' : 'none';
        
        if (isSalida) {
            calcularMontoVenta();
        } else {
            document.getElementById('btnGuardarMovimiento').disabled = false;
        }
    }
    
    // Asignar listeners
    tipoMovimientoSelect.addEventListener('change', handleModalChange);
    cantidadInput.addEventListener('input', handleModalChange);
    // Para los inputs de pago, solo tienen que recalcular el monto de venta
    efecInput.addEventListener('input', calcularMontoVenta);
    tarjInput.addEventListener('input', calcularMontoVenta);
    transInput.addEventListener('input', calcularMontoVenta);
});

// FUNCIÓN GLOBAL para abrir el modal (DEBE SER GLOBAL)
function abrirModal(id, nombre, precio) {
    document.getElementById('modalIdProducto').value = id;
    document.getElementById('modalNombreProducto').innerText = nombre;
    document.getElementById('modalPrecioUnitario').value = precio.toFixed(2); 
    
    const precioBase = precio.toFixed(2);
    document.getElementById('selectTipoMovimiento').value = 'salida'; 
    document.getElementById('inputCantidad').value = '1';
    
    document.getElementById('inputEfectivo').value = precioBase; 
    document.getElementById('inputTarjeta').value = '0.00';
    document.getElementById('inputTransferencia').value = '0.00';
    
    // Esto se inicializa aquí, usando los elementos que ya se asignaron en DOMContentLoaded
    const isSalida = document.getElementById('selectTipoMovimiento').value === 'salida';
    document.getElementById('paymentSection').style.display = isSalida ? 'block' : 'none';
    
    // Llama a la función global
    calcularMontoVenta(); 
    
    new bootstrap.Modal(document.getElementById('modalStock')).show();
}