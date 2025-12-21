$(document).ready(function() {
    $('#tablaClientes').DataTable({
        responsive: false, 
        scrollX: true,     
        ajax: {
            url: '/proyectos/ClientManager/app/api/get_clientes.php', 
            type: 'GET',
            dataSrc: 'data'
        },
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
        },
        order: [[ 0, "desc" ]], 
        columns: [
            // COLUMNA 0: ID
            { data: 'id_cliente', render: function(data) { return `<span class="fw-bold text-muted">#${data}</span>`; } },
            
            // COLUMNA 1: ESTADO ACTUALIZADA
            { 
                data: null, 
                render: function (data, type, row) {
                    const hoy = new Date();
                    // Forzamos la fecha de vencimiento a la zona horaria local para evitar desfases
                    const fechaVencePartes = row.fecha_vencimiento.split('-');
                    const vence = new Date(fechaVencePartes[0], fechaVencePartes[1] - 1, fechaVencePartes[2]);
                    
                    hoy.setHours(0,0,0,0);
                    vence.setHours(0,0,0,0);
                    
                    const diffTime = vence.getTime() - hoy.getTime();
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)); 

                    let badge;

                    if (diffDays < 0) {
                        // Ya pasó la fecha
                        badge = '<span class="badge rounded-pill text-bg-danger" style="font-size: 0.8em;">VENCIDO</span>';
                    } else if (diffDays === 0) {
                        // Es hoy mismo
                        badge = '<span class="badge rounded-pill text-bg-info" style="font-size: 0.8em; color: #fff !important; background-color: #fd7e14 !important;">VENCE HOY</span>';
                    } else if (diffDays <= 2) {
                        // Le quedan 1 o 2 días
                        badge = '<span class="badge rounded-pill text-bg-warning" style="font-size: 0.8em; color: #000 !important;">POR VENCER</span>';
                    } else {
                        // Más de 2 días
                        badge = '<span class="badge rounded-pill text-bg-success" style="font-size: 0.8em;">ACTIVO</span>';
                    }

                    return badge;
                }
            },
            
            // COLUMNA 2: CLIENTE
            { 
                data: null, 
                render: function (data, type, row) {
                    return `<div class="fw-bold text-uppercase">${row.nombre_cliente}</div>
                                <small class="text-muted" style="font-size: 0.85em;">${row.correo || '---'}</small>`;
                }
            },
            
            // COLUMNA 3: PLAN Y PAGO (MODIFICADA para incluir Transferencia y Descuento)
            { 
                data: null, 
                render: function(data, type, row) {
                    let badge = `<span class="badge bg-secondary mb-1">${row.plan_suscripcion}</span>`;
                    
                    // Convertimos a número para asegurar
                    let total = parseFloat(row.total_pagado);
                    let descuento = parseFloat(row.descuento); // <-- NUEVO: Capturamos el descuento
                    let efectivo = parseFloat(row.pago_efectivo);
                    let tarjeta = parseFloat(row.pago_tarjeta);
                    let transferencia = parseFloat(row.pago_transferencia); 

                    if (total > 0) {
                        let infoPago = `<div class="small fw-bold text-success">$${total.toFixed(2)}</div>`;
                        
                        // Detalles (Texto pequeño)
                        let detalle = [];
                        if(efectivo > 0) detalle.push(`Efe: $${efectivo}`);
                        if(tarjeta > 0) detalle.push(`Tar: $${tarjeta}`);
                        if(transferencia > 0) detalle.push(`Transf: $${transferencia}`); 
                        // NUEVO: Añadimos el descuento al detalle si es > 0
                        if(descuento > 0) detalle.push(`<span class="text-warning">Desc: $${descuento.toFixed(2)}</span>`); 

                        return `<div>${badge}</div>${infoPago}<div style="font-size:0.7em; color:#888;">${detalle.join(' / ')}</div>`;
                    } else {
                        // Es Acompañante
                        return `<div>${badge}</div><div class="small text-muted fst-italic">Acompañante</div>`;
                    }
                }
            },
            
            // COLUMNA 4: CONTACTO
            { data: 'telefono', render: function(data) { return data ? `<i class="bi bi-whatsapp text-success me-1"></i> ${data}` : '---'; } },

            // COLUMNA 5: FECHA
            { 
                data: 'fecha_vencimiento',
                render: function(data) {
                    if (!data) return '';
                    const meses = ["enero", "febrero", "marzo", "abril", "mayo", "junio", "julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre"];
                    const partes = data.split('-');
                    // Formato DD/Mes/YYYY
                    return `<span class="fw-bold small text-uppercase">${partes[2]} ${meses[parseInt(partes[1]) - 1]} ${partes[0]}</span>`;
                }
            },

            // COLUMNA 6: ACCIONES (SE MANTIENE AQUÍ, SE OCULTARÁ EN LA VISTA PHP)
            {
                data: null,
                orderable: false,
                render: function (data, type, row) {
                    const nombreSeguro = row.nombre_cliente.replace(/'/g, "\\'");

                    return `
                        <div class="d-flex gap-2 justify-content-end">
                            <button class="btn btn-sm btn-outline-warning" title="Editar">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <button onclick="confirmarEliminar(${row.id_cliente}, '${nombreSeguro}')" 
                                    class="btn btn-sm btn-outline-danger" 
                                    title="Eliminar">
                                <i class="bi bi-trash3-fill"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ],
        // NUEVA PROPIEDAD: Ocultar la Columna 6 (Acciones)
        columnDefs: [
            { 
                targets: 6, // Índice de la columna 'Acciones' (cero basado)
                visible: false,
                searchable: false
            }
        ]
    });
});

// ... (El resto de la función confirmarEliminar se mantiene igual)

        /* ==========================================
        FUNCIÓN SWEETALERT2 PARA ELIMINAR
        ========================================== */
        function confirmarEliminar(id, nombre) {
            Swal.fire({
                title: '¿Eliminar registro?',
                text: `Se borrará permanentemente a: ${nombre}`,
                icon: 'warning',
                showCancelButton: true,
                // Estilos Dark Mode / Rojo
                background: '#1a1a1a', 
                color: '#ffffff',
                confirmButtonColor: '#E62429', // Rojo AG
                cancelButtonColor: '#444',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Si confirma, redirigimos al PHP que borra
                    window.location.href = `../../controllers/crud/delete_action.php?id=${id}`;
                }
            });
        }