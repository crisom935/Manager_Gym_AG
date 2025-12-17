let myChart; // Variable global para la instancia de Chart.js
const API_URL = '/proyectos/ClientManager/app/api/get_finanzas.php'; // Cambia a la ruta correcta si es necesario

document.addEventListener("DOMContentLoaded", function() {
    // 1. Configuración de filtros al cargar
    const monthYearInput = document.getElementById('filterMonthYear');
    const loadButton = document.getElementById('btnLoadReport');
    
    // Establecer mes actual como valor por defecto
    const today = new Date();
    const currentMonth = String(today.getMonth() + 1).padStart(2, '0');
    const currentYear = today.getFullYear();
    monthYearInput.value = `${currentYear}-${currentMonth}`;

    // 2. Inicializar el evento de carga del reporte
    loadButton.addEventListener('click', loadReport);

    // 3. Cargar el reporte inicial al iniciar la página
    loadReport(); 
});

/**
 * Función principal para cargar y filtrar los datos financieros.
 */
function loadReport() {
    const selectedMonthYear = document.getElementById('filterMonthYear').value; // Ej: "2025-11"
    const granularity = document.getElementById('filterGranularity').value;     // Ej: "day", "week", "month"

    // Construye la URL con los parámetros de filtro
    const url = `${API_URL}?mes_anio=${selectedMonthYear}&agrupacion=${granularity}`;

    // Actualiza el indicador de rango
    document.getElementById('reportRangeLabel').innerText = `Reporte de ${granularity.toUpperCase()} (${selectedMonthYear})`;

    fetch(url)
        .then(response => {
            if (!response.ok) {
                // Si la API devuelve un error (500), lanzamos una excepción
                throw new Error('La respuesta de la red no fue satisfactoria.');
            }
            return response.json();
        })
        .then(json => {
            const datos = json.data;
            
            if (datos && datos.length > 0) {
                // Las funciones existentes ahora usan los datos filtrados
                actualizarKPIs(datos);
                renderizarGrafica(datos);
                llenarTabla(datos);
            } else {
                document.getElementById('tbodyReportes').innerHTML = '<tr><td colspan="6" class="text-center py-4 text-muted">No hay registros para el periodo seleccionado.</td></tr>';
                // Limpiar KPIs y Gráfica si no hay datos
                actualizarKPIs([]); 
                renderizarGrafica([]);
            }
        })
        .catch(error => {
            console.error('Error al obtener los datos:', error);
            alert('Error al cargar el reporte. Revisa la consola para más detalles.');
        });
}

// 2. Sumar totales generales
function actualizarKPIs(datos) {
    // ... (Tu función actualizadaKPIs, no necesita cambios, solo recibe 'datos')
    let totalG = 0;
    let totalE = 0;
    let totalT = 0;
    let totalTr = 0;

    datos.forEach(d => {
        totalG += parseFloat(d.gran_total || 0);
        totalE += parseFloat(d.total_efectivo || 0);
        totalT += parseFloat(d.total_tarjeta || 0);
        totalTr += parseFloat(d.total_transferencia || 0);
    });

    const fmt = new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' });

    document.getElementById('kpiTotal').innerText = fmt.format(totalG);
    document.getElementById('kpiEfectivo').innerText = fmt.format(totalE);
    document.getElementById('kpiTarjeta').innerText = fmt.format(totalT);
    document.getElementById('kpiTransferencia').innerText = fmt.format(totalTr);
}

// 3. Tabla HTML
function llenarTabla(datos) {
    // ... (Tu función llenarTabla)
    const tbody = document.getElementById('tbodyReportes');
    const meses = ["ene", "feb", "mar", "abr", "may", "jun", "jul", "ago", "sep", "oct", "nov", "dic"];
    let html = '';

    datos.forEach(d => {
        // La clave es que 'd.fecha' ahora contiene el período de agrupación (YYYY-MM-DD o YYYY-MM o YYYY-WEEK)
        // Necesitas adaptar el formato para 'fechaBonita' según la agrupación
        let fechaBonita;
        if (d.fecha.length === 10) { // YYYY-MM-DD (Día)
             let f = d.fecha.split('-');
             fechaBonita = `${f[2]}/${meses[parseInt(f[1])-1]}/${f[0]}`;
        } else if (d.fecha.includes('-')) { // YYYY-MM (Mes) o YYYY-WEEK (Semana)
             fechaBonita = d.fecha; // Muestra el periodo tal cual
        } else {
             fechaBonita = d.fecha; // fallback
        }


        let efec = parseFloat(d.total_efectivo || 0).toFixed(2);
        let tarj = parseFloat(d.total_tarjeta || 0).toFixed(2);
        let trans = parseFloat(d.total_transferencia || 0).toFixed(2);
        let total = parseFloat(d.gran_total || 0).toFixed(2);
        // La API ahora devuelve 'personas' por periodo
        let personas = parseInt(d.personas || 0); 

        html += `
            <tr>
                <td class="fw-bold text-uppercase text-secondary">${fechaBonita}</td>
                
                <td class="text-center"><span class="badge bg-secondary rounded-pill">${personas}</span></td>
                <td class="text-end text-success fw-bold">$${efec}</td>
                <td class="text-end text-info fw-bold">$${tarj}</td>
                <td class="text-end text-warning fw-bold">$${trans}</td>
                <td class="text-end text-white bg-danger bg-opacity-75 fw-bold border-start border-danger">$${total}</td>
            </tr>
        `;
    });

    tbody.innerHTML = html;
}


// 4. Gráfica de Barras Apiladas
function renderizarGrafica(datos) {
    // ... (Tu función renderizarGrafica, no necesita cambios grandes, solo recibe 'datos')
    
    // Destruir gráfica anterior si existe (evita sobreposición al recargar)
    const chartStatus = Chart.getChart("graficaFinanzas"); 
    if (chartStatus != undefined) {
        chartStatus.destroy();
    }

    const ctx = document.getElementById('graficaFinanzas').getContext('2d');
    const datosOrdenados = [...datos]; // Ya vienen ordenados de la API

    const etiquetas = datosOrdenados.map(d => {
        // Usamos la misma lógica simple que en la tabla para la etiqueta del eje X
        let fecha = d.fecha;
        if (fecha.length === 10) { // YYYY-MM-DD
             let f = fecha.split('-');
             return `${f[2]}/${f[1]}`;
        }
        return fecha;
    });
    
    const dataEfectivo = datosOrdenados.map(d => d.total_efectivo);
    const dataTarjeta = datosOrdenados.map(d => d.total_tarjeta);
    const dataTransferencia = datosOrdenados.map(d => d.total_transferencia);

    // Si no hay datos, evita dibujar la gráfica
    if (datos.length === 0) {
        ctx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height);
        return;
    }
    
    // ... (El resto de tu código de Chart.js) ...
     myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: etiquetas,
            datasets: [
                {
                    label: 'Efectivo',
                    data: dataEfectivo,
                    backgroundColor: '#198754',
                    borderRadius: 4
                },
                {
                    label: 'Tarjeta',
                    data: dataTarjeta,
                    backgroundColor: '#0dcaf0',
                    borderRadius: 4
                },
                {
                    label: 'Transferencia',
                    data: dataTransferencia,
                    backgroundColor: '#ffc107',
                    borderRadius: 4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: { stacked: true, ticks: { color: '#aaa' } },
                y: { 
                    stacked: true, 
                    ticks: { color: '#aaa', callback: function(value) { return '$' + value; } },
                    grid: { color: '#333' }
                }
            },
            plugins: {
                legend: { labels: { color: '#fff' } },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                label += new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(context.parsed.y);
                            }
                            return label;
                        }
                    }
                }
            }
        }
    });
}