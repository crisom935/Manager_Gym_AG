// ¡OJO AQUÍ! Cambiamos a la API específica para este reporte
const API_URL = '/proyectos/ClientManager/app/api/get_finanzas.php';

let datosGlobales = null; // Almacenamos la respuesta para filtrar sin recargar

document.addEventListener("DOMContentLoaded", function() {
    const monthInput = document.getElementById('filterMonthYear');
    const weekSelect = document.getElementById('filterWeek');
    
    // Default mes actual
    const today = new Date();
    const yyyy = today.getFullYear();
    const mm = String(today.getMonth() + 1).padStart(2, '0');
    monthInput.value = `${yyyy}-${mm}`;

    // Listeners
    document.getElementById('btnLoadReport').addEventListener('click', loadReport);
    
    // Cuando cambiamos de semana en el select, filtramos localmente (rápido)
    weekSelect.addEventListener('change', function() {
        if (datosGlobales) filtrarYRenderizar();
    });

    loadReport();
});

function loadReport() {
    const mesAnio = document.getElementById('filterMonthYear').value;
    const container = document.getElementById('reporteContainer');
    const btn = document.getElementById('btnLoadReport');
    
    // Feedback visual
    container.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-danger"></div><p class="mt-2 text-muted">Generando bitácora...</p></div>';
    btn.disabled = true;

    fetch(`${API_URL}?mes_anio=${mesAnio}`)
        .then(res => {
            if (!res.ok) throw new Error("Error en la red o archivo no encontrado");
            return res.json();
        })
        .then(data => {
            datosGlobales = data; // Guardamos datos en memoria
            
            // 1. Llenar Select de Semanas dinámicamente
            llenarSelectSemanas(data.meta_semanal);
            
            // 2. Pintar Header Mensual (siempre fijo)
            renderTotalesMensuales(data.meta_mensual);
            
            // 3. Pintar días y header semanal según filtro
            filtrarYRenderizar();
        })
        .catch(err => {
            console.error("Error cargando reporte:", err);
            container.innerHTML = '<div class="alert alert-danger text-center">Error al cargar datos. Verifica que el archivo <b>api/finanzas/get_reporte_ag.php</b> exista y la ruta sea correcta.</div>';
        })
        .finally(() => {
            btn.disabled = false;
        });
}

function llenarSelectSemanas(semanas) {
    const select = document.getElementById('filterWeek');
    select.innerHTML = '<option value="all">Todo el Mes</option>';
    
    if (semanas) {
        // Iteramos las claves "Semana 1", "Semana 2"...
        Object.keys(semanas).forEach(key => {
            select.innerHTML += `<option value="${key}">${key}</option>`;
        });
    }
}

function filtrarYRenderizar() {
    const semanaSeleccionada = document.getElementById('filterWeek').value; // "all" o "Semana 1"
    const cardSemanal = document.getElementById('cardSemanal');
    
    // validación de seguridad
    if (!datosGlobales || !datosGlobales.dias) return;

    // 1. Filtrar Días
    // Si es "all", mostramos todos. Si no, solo los que coincidan con la semana.
    const diasFiltrados = datosGlobales.dias.filter(dia => {
        return semanaSeleccionada === 'all' || dia.semana === semanaSeleccionada;
    });

    // 2. Renderizar Días
    renderDias(diasFiltrados);

    // 3. Manejar Header Semanal
    if (semanaSeleccionada !== 'all' && datosGlobales.meta_semanal[semanaSeleccionada]) {
        // Mostrar datos de esa semana específica
        const datosSemana = datosGlobales.meta_semanal[semanaSeleccionada];
        renderTotalesSemanales(datosSemana, semanaSeleccionada);
        cardSemanal.style.display = 'block';
    } else {
        // Si vemos todo el mes, ocultamos el header semanal
        cardSemanal.style.display = 'none';
    }
}

function renderTotalesMensuales(meta) {
    const fmt = new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' });
    
    // Verificamos que meta exista para no dar error
    if (!meta) return;

    document.getElementById('mtTotal').innerText = fmt.format(meta.gran_total || 0);
    document.getElementById('mtPersonas').innerText = meta.total_personas || 0;
    document.getElementById('mtInsc').innerText = fmt.format(meta.inscripciones || 0);
    document.getElementById('mtProd').innerText = fmt.format(meta.productos || 0);
    document.getElementById('mtEfe').innerText = fmt.format(meta.efectivo || 0);
    document.getElementById('mtTar').innerText = fmt.format(meta.tarjeta || 0);
    document.getElementById('mtTra').innerText = fmt.format(meta.transferencia || 0);
}

function renderTotalesSemanales(meta, label) {
    const fmt = new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' });
    
    if (!meta) return;

    document.getElementById('labelSemanal').innerText = "Resumen " + label;
    document.getElementById('stTotal').innerText = fmt.format(meta.gran_total || 0);
    document.getElementById('stPersonas').innerText = meta.total_personas || 0;
    document.getElementById('stInsc').innerText = fmt.format(meta.inscripciones || 0);
    document.getElementById('stProd').innerText = fmt.format(meta.productos || 0);
    document.getElementById('stEfe').innerText = fmt.format(meta.efectivo || 0);
    document.getElementById('stTar').innerText = fmt.format(meta.tarjeta || 0);
    document.getElementById('stTra').innerText = fmt.format(meta.transferencia || 0);
}

function renderDias(dias) {
    const container = document.getElementById('reporteContainer');
    const fmt = new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' });
    let html = '';

    if (dias.length === 0) {
        container.innerHTML = '<div class="text-center text-muted py-5"><i class="bi bi-calendar-x display-4"></i><p class="mt-2">No hay datos para el periodo seleccionado.</p></div>';
        return;
    }

    dias.forEach(dia => {
        let filasPaquetes = '';
        let totalPaqDia = 0;
        
        // PAQUETES
        for (const [nombre, valores] of Object.entries(dia.paquetes)) {
            totalPaqDia += valores.total;
            filasPaquetes += `<tr>
                <td class="text-white-50">${nombre}</td>
                <td class="text-center">${valores.personas}</td>
                <td class="text-end">${valores.efectivo > 0 ? fmt.format(valores.efectivo) : '-'}</td>
                <td class="text-end">${valores.tarjeta > 0 ? fmt.format(valores.tarjeta) : '-'}</td>
                <td class="text-end">${valores.transf > 0 ? fmt.format(valores.transf) : '-'}</td>
                <td class="text-end fw-bold text-white">${valores.total > 0 ? fmt.format(valores.total) : '$0.00'}</td>
            </tr>`;
        }
        
        // PRODUCTOS
        let filasProductos = '';
        let totalProdDia = 0;
        for (const [nombre, valores] of Object.entries(dia.productos)) {
            totalProdDia += valores.total;
            filasProductos += `<tr>
                <td class="text-white-50">${nombre}</td>
                <td class="text-center">${valores.cantidad}</td>
                <td class="text-end">${valores.efectivo > 0 ? fmt.format(valores.efectivo) : '-'}</td>
                <td class="text-end">${valores.tarjeta > 0 ? fmt.format(valores.tarjeta) : '-'}</td>
                <td class="text-end">${valores.transf > 0 ? fmt.format(valores.transf) : '-'}</td>
                <td class="text-end fw-bold text-white">${valores.total > 0 ? fmt.format(valores.total) : '$0.00'}</td>
            </tr>`;
        }

        const granTotalDia = totalPaqDia + totalProdDia;

        html += `
        <div class="card bg-dark border-secondary mb-4 shadow-sm page-break">
            <div class="card-header border-secondary d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h6 class="mb-0 fw-bold text-danger text-uppercase"><i class="bi bi-calendar-day me-2"></i>${dia.fecha_humana}</h6>
                <span class="badge ${granTotalDia > 0 ? 'bg-success' : 'bg-secondary'}">Total Día: ${fmt.format(granTotalDia)}</span>
            </div>
            
            <div class="card-body p-0">
                <div class="table-responsive"> 
                    <table class="table table-dark table-sm table-ag mb-0 table-bordered border-secondary">
                        <thead>
                            <tr>
                                <th style="min-width: 180px;">Concepto</th> 
                                <th class="text-center">Cant.</th> 
                                <th class="text-end text-success">Efectivo</th>
                                <th class="text-end text-info">Tarjeta</th>
                                <th class="text-end text-warning">Transf.</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="table-active"><td colspan="6" class="fw-bold text-primary ps-3" style="font-size:0.75rem">PAQUETES / INSCRIPCIONES</td></tr>
                            ${filasPaquetes}
                            <tr class="table-active"><td colspan="6" class="fw-bold text-secondary ps-3" style="font-size:0.75rem">PRODUCTOS</td></tr>
                            ${filasProductos}
                        </tbody>
                        <tfoot>
                            <tr class="row-total-dia">
                                <td colspan="5" class="text-end fw-bold text-uppercase pe-3">Gran Total Diario</td>
                                <td class="text-end fw-bold text-white bg-danger bg-opacity-50">${fmt.format(granTotalDia)}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>`;
    });

    container.innerHTML = html;
}