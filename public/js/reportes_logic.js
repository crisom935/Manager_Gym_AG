document.addEventListener("DOMContentLoaded", function() {
    cargarReportes();
});

function cargarReportes() {
    // TRUCO ANTI-CACHÉ: Agregamos un timestamp al final (?t=...)
    // Esto obliga al navegador a pedir datos frescos siempre.
    const url = '/proyectos/ClientManager/app/api/get_finanzas.php?t=' + new Date().getTime();

    fetch(url)
        .then(response => response.json())
        .then(json => {
            const datos = json.data;
            
            if (datos.length > 0) {
                actualizarKPIs(datos);
                renderizarGrafica(datos);
                llenarTabla(datos);
            } else {
                document.getElementById('tbodyReportes').innerHTML = '<tr><td colspan="6" class="text-center py-4 text-muted">No hay registros financieros aún.</td></tr>';
            }
        })
        .catch(error => console.error('Error:', error));
}

// 2. Sumar totales generales
function actualizarKPIs(datos) {
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
    const tbody = document.getElementById('tbodyReportes');
    const meses = ["ene", "feb", "mar", "abr", "may", "jun", "jul", "ago", "sep", "oct", "nov", "dic"];
    let html = '';

    datos.forEach(d => {
        let f = d.fecha.split('-');
        let fechaBonita = `${f[2]}/${meses[parseInt(f[1])-1]}/${f[0]}`;

        let efec = parseFloat(d.total_efectivo || 0).toFixed(2);
        let tarj = parseFloat(d.total_tarjeta || 0).toFixed(2);
        let trans = parseFloat(d.total_transferencia || 0).toFixed(2);
        let total = parseFloat(d.gran_total || 0).toFixed(2);

        html += `
            <tr>
                <td class="fw-bold text-uppercase text-secondary">${fechaBonita}</td>
                
                <td class="text-center"><span class="badge bg-secondary rounded-pill">${d.personas}</span></td>
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
    // Destruir gráfica anterior si existe (evita sobreposición al recargar)
    const chartStatus = Chart.getChart("graficaFinanzas"); 
    if (chartStatus != undefined) {
        chartStatus.destroy();
    }

    const ctx = document.getElementById('graficaFinanzas').getContext('2d');
    const datosOrdenados = [...datos].reverse(); // Cronológico

    const etiquetas = datosOrdenados.map(d => {
        let f = d.fecha.split('-');
        return `${f[2]}/${f[1]}`;
    });
    
    const dataEfectivo = datosOrdenados.map(d => d.total_efectivo);
    const dataTarjeta = datosOrdenados.map(d => d.total_tarjeta);
    const dataTransferencia = datosOrdenados.map(d => d.total_transferencia);

    new Chart(ctx, {
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