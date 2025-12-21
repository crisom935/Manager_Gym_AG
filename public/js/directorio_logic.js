// public/js/directorio_logic.js

$(document).ready(function() {
    const fmt = new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' });

    // Precios oficiales para referencia visual
    const preciosOficiales = {
        'Individual Semanal': 200,
        'Individual Mensual': 650,
        'Paquete Amigos': 1100,
        'Familiar #1': 1650,
        'Familiar #2': 2300
    };

    $('#tablaDirectorio').DataTable({
        responsive: true,
        autoWidth: false,
        ajax: {
            url: '/proyectos/ClientManager/app/api/directory/get_directorio.php',
            type: 'GET',
            dataSrc: 'data'
        },
        language: { url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json' },
        order: [[ 0, "desc" ]], 
        columns: [
            // 1. ID
            { 
                data: 'id_cliente', 
                render: function(data) { return `<span class="text-white-50 small">#${data}</span>`; }
            },
            
            // 2. CLIENTE
            { 
                data: 'nombre_cliente', 
                render: function(data) { return `<span class="fw-bold text-white text-uppercase">${data}</span>`; }
            },
            
            // 3. CONTACTO
            { 
                data: null, 
                render: function(row) {
                    let html = '';
                    if(row.telefono) html += `<div style="font-size:0.85rem"><i class="bi bi-whatsapp text-success me-1"></i> ${row.telefono}</div>`;
                    if(row.correo) html += `<div style="font-size:0.85rem"><i class="bi bi-envelope text-secondary me-1"></i> ${row.correo}</div>`;
                    return html || '<span class="text-muted small">-</span>';
                }
            },
            
            // 4. PLAN Y ESTADO
            { 
                data: null, 
                render: function(row) {
                    let precioPlan = preciosOficiales[row.plan_suscripcion] || 0;
                    let textoPrecio = precioPlan > 0 ? fmt.format(precioPlan) : '';

                    const hoy = new Date();
                    hoy.setHours(0,0,0,0);
                    // Agregamos T00:00:00 para asegurar compatibilidad de fecha
                    const vence = new Date(row.fecha_vencimiento + 'T00:00:00');
                    
                    const diffTime = vence - hoy;
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                    
                    let badgeClass = 'bg-success';
                    let textoEstado = `${diffDays} días rest.`;

                    if (diffDays < 0) {
                        badgeClass = 'bg-danger';
                        textoEstado = 'Vencido';
                    } else if (diffDays === 0) {
                        badgeClass = 'bg-warning text-dark fw-bold';
                        textoEstado = 'Vence hoy';
                    } else if (diffDays <= 2) {
                        badgeClass = 'bg-warning text-dark fw-bold';
                        textoEstado = 'Por vencer';
                    }

                    return `
                        <div class="mb-1 d-flex justify-content-between align-items-center" style="max-width:200px">
                            <span class="badge border border-secondary text-white">${row.plan_suscripcion}</span>
                            <span class="text-success small fw-bold">${textoPrecio}</span>
                        </div>
                        <div>
                            <span class="badge ${badgeClass}">${textoEstado}</span> 
                            <small class="text-muted ms-1" style="font-size:0.75rem">${row.fecha_vencimiento}</small>
                        </div>
                    `;
                }
            },
            
            // 5. DETALLE FINANCIERO
            { 
                data: null, 
                render: function(row) {
                    // Generar lista de pagos detallada
                    let desglose = '';
                    
                    if(parseFloat(row.efectivo) > 0) {
                        desglose += `<div class="text-success small d-flex justify-content-between" style="font-size:0.8rem"><span><i class="bi bi-cash me-1"></i>Efe:</span> <span>${fmt.format(row.efectivo)}</span></div>`;
                    }
                    if(parseFloat(row.tarjeta) > 0) {
                        desglose += `<div class="text-info small d-flex justify-content-between" style="font-size:0.8rem"><span><i class="bi bi-credit-card me-1"></i>Tar:</span> <span>${fmt.format(row.tarjeta)}</span></div>`;
                    }
                    if(parseFloat(row.transferencia) > 0) {
                        desglose += `<div class="text-warning small d-flex justify-content-between" style="font-size:0.8rem"><span><i class="bi bi-bank me-1"></i>Tra:</span> <span>${fmt.format(row.transferencia)}</span></div>`;
                    }

                    // Extras (Inscripción y Descuento)
                    let extras = '';
                    if(parseFloat(row.costo_inscripcion) > 0) {
                        extras += `<div class="text-primary small mt-1 pt-1 border-top border-secondary" style="font-size:0.75rem"><i class="bi bi-plus-circle me-1"></i>Inscrip: ${fmt.format(row.costo_inscripcion)}</div>`;
                    }
                    if(parseFloat(row.descuento) > 0) {
                        extras += `<div class="text-danger small fw-bold" style="font-size:0.75rem"><i class="bi bi-dash-circle me-1"></i>Desc: -${fmt.format(row.descuento)}</div>`;
                    }

                    // Render Final de la celda
                    return `
                        <div class="mb-1 pb-1 border-bottom border-secondary">
                            <span class="text-muted small text-uppercase" style="font-size:0.7rem">Total Pagado:</span>
                            <div class="fw-bold text-white fs-6">${fmt.format(row.total_pagado)}</div>
                        </div>
                        <div style="min-width: 110px;">
                            ${desglose}
                            ${extras}
                        </div>
                    `;
                }
            },

            // 6. REGISTRADO POR (NUEVO - Insertado aquí)
            {
                data: 'registrado_por',
                render: function(d) {
                    return `<div class="badge bg-secondary bg-opacity-25 text-light border border-secondary">
                                <i class="bi bi-person-check-fill me-1"></i>${d}
                            </div>`;
                }
            },
            
            // 7. FECHAS REGISTRO
            { 
                data: null, 
                render: function(row) {
                    let creado = row.created_at ? row.created_at.substring(0, 10) : '-';
                    return `
                        <div class="small text-muted" style="font-size:0.75rem">
                            <div>Reg: ${creado}</div>
                            <div>Inicio: ${row.fecha_inscripcion}</div>
                        </div>
                    `;
                }
            },
            
            // 8. ACCIONES
            { 
                data: null,
                className: 'text-end',
                render: function(data, type, row) {
                    let jsonRow = JSON.stringify(row).replace(/'/g, "&#39;");
                    return `
                        <div class="btn-group" role="group">
                            <button class="btn btn-sm btn-outline-warning" onclick='cargarModal(${jsonRow})' title="Editar">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <a href="../../controllers/crud/delete_action.php?id=${row.id_cliente}" 
                                class="btn btn-sm btn-outline-danger" 
                                onclick="return confirm('¿Eliminar permanentemente a ${row.nombre_cliente}?')"
                                title="Eliminar">
                                <i class="bi bi-trash"></i>
                            </a>
                        </div>
                    `;
                }
            }
        ]
    });
});

function cargarModal(cliente) {
    document.getElementById('editId').value = cliente.id_cliente;
    document.getElementById('editNombre').value = cliente.nombre_cliente;
    document.getElementById('editTel').value = cliente.telefono;
    document.getElementById('editEmail').value = cliente.correo;
    document.getElementById('editPlan').value = cliente.plan_suscripcion;
    document.getElementById('editVence').value = cliente.fecha_vencimiento;
    
    const modalEl = document.getElementById('modalEditarCliente');
    const modal = new bootstrap.Modal(modalEl);
    modal.show();
}