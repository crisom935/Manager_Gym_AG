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
            // ... (Tus columnas anteriores 0 al 5 siguen igual) ...
            
            // COLUMNA 0: ID
            { data: 'id_cliente', render: function(data) { return `<span class="fw-bold text-muted">#${data}</span>`; } },
            
            // COLUMNA 1: ESTADO
            { 
                data: null, 
                render: function (data, type, row) {
                    const hoy = new Date();
                    const vence = new Date(row.fecha_vencimiento);
                    hoy.setHours(0,0,0,0);
                    vence.setHours(0,0,0,0);
                    vence.setDate(vence.getDate() + 1); 

                    return (vence >= hoy) 
                        ? '<span class="badge rounded-pill text-bg-success" style="font-size: 0.8em;">ACTIVO</span>'
                        : '<span class="badge rounded-pill text-bg-danger" style="font-size: 0.8em;">VENCIDO</span>';
                }
            },
            
            // COLUMNA 2: CLIENTE
            { 
                data: null, 
                render: function (data, type, row) {
                    return `<div class="fw-bold text-uppercase">${row.nombre_cliente}</div>
                            <small class="text-muted" style="font-size: 0.85em;">${row.correo || ''}</small>`;
                }
            },
            // COLUMNA 3: PLAN Y PAGO
            { 
                data: null, // Usamos null para combinar columnas
                render: function(data, type, row) {
                    let badge = `<span class="badge bg-secondary mb-1">${row.plan_suscripcion}</span>`;
                    
                    // Convertimos a número para asegurar
                    let total = parseFloat(row.total_pagado);
                    let efectivo = parseFloat(row.pago_efectivo);
                    let tarjeta = parseFloat(row.pago_tarjeta);

                    if (total > 0) {
                        // Es Titular
                        let infoPago = `<div class="small fw-bold text-success">$${total.toFixed(2)}</div>`;
                        
                        // Detalles hover o texto pequeño
                        let detalle = [];
                        if(efectivo > 0) detalle.push(`Efe: $${efectivo}`);
                        if(tarjeta > 0) detalle.push(`Tar: $${tarjeta}`);
                        
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
                    return `<span class="fw-bold small text-uppercase">${partes[2]}/${meses[parseInt(partes[1]) - 1]}/${partes[0]}</span>`;
                }
            },

            // ... dentro de columns: [ ...

                     // COLUMNA 6: ACCIONES
                    {
                        data: null,
                        orderable: false,
                        render: function (data, type, row) {
                            // Nota: Ya no creamos la URL aquí directo en el href
                            // Llamamos a la función confirmarEliminar con el ID y el Nombre
                            
                            // Escapar comillas simples en el nombre para evitar errores de JS
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
                ]
            });
        });

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