// public/js/movimientos_inv.js

$(document).ready(function() {
    $('#tablaMovimientos').DataTable({
        responsive: true,
        autoWidth: false,
        ajax: {
            url: '/proyectos/ClientManager/app/api/inventory/get_movimientos.php',
            type: 'GET',
            dataSrc: 'data'
        },
        language: { url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json' },
        order: [[ 1, "desc" ]], // Ordenar por FECHA descendente (índice 1)
        columns: [
            // 1. ID
            { 
                data: 'id_movimiento', 
                render: d => `<span class="text-white-50 small">#${d}</span>` 
            },

            // 2. Fecha
            { 
                data: 'fecha_movimiento',
                render: function(d) {
                    return `<span class="text-white small">${d}</span>`;
                }
            },

            // 3. USUARIO (NUEVA COLUMNA)
            { 
                data: 'nombre_usuario',
                render: function(d) {
                    // Un badge gris sutil para el usuario
                    return `<div class="badge bg-secondary bg-opacity-25 text-light border border-secondary">
                                <i class="bi bi-person-fill me-1"></i>${d}
                            </div>`;
                }
            },

            // 4. Producto
            { 
                data: 'nombre_producto', 
                render: d => `<span class="fw-bold text-uppercase text-white">${d}</span>` 
            },

            // 5. Tipo
            { 
                data: 'tipo_movimiento',
                render: function(d) {
                    if (d === 'entrada') {
                        return `<span class="badge bg-success bg-opacity-25 text-success border border-success"><i class="bi bi-arrow-down-circle me-1"></i>Entrada</span>`;
                    } else {
                        return `<span class="badge bg-danger bg-opacity-25 text-danger border border-danger"><i class="bi bi-arrow-up-circle me-1"></i>Salida</span>`;
                    }
                }
            },

            // 6. Cantidad
            { 
                data: 'cantidad',
                render: function(d, type, row) {
                    let color = row.tipo_movimiento === 'entrada' ? 'text-success' : 'text-danger';
                    let signo = row.tipo_movimiento === 'entrada' ? '+' : '-';
                    return `<span class="fw-bold fs-6 ${color}">${signo}${d}</span>`;
                }
            },

            // 7. Nota
            { 
                data: 'nota',
                render: function(d) {
                    return d ? `<div class="small text-white-50 fst-italic">"${d}"</div>` : '<span class="text-muted small">-</span>';
                }
            }
        ]
    });
});