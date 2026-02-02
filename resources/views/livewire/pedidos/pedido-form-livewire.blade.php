<div
    x-data="pedidoForm({
        pedido: @js($pedidoEncontrado),
        clientes: @js($clientes),
        bodegas: @js($bodegas),
        alistadores: @js($alistadores),
        users: @js($users),
        productos: @js($productos),
        detalles: @js($detalles)
    })"
    x-init="init()"
    class="space-y-4"
>

<div class="bg-white dark:bg-gray-900 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 p-6 mb-6">
    @include('livewire.pedidos.livewire-pedidos-seccion-general')
</div>
<div class="bg-white dark:bg-gray-900 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 p-6 mb-6">
@include('livewire.pedidos.livewire-pedidos-seccion-detalle')
</div>
<div class="sticky bottom-0 left-0 w-full z-30 bg-white/80 dark:bg-gray-900/80 backdrop-blur border-t border-gray-200 dark:border-gray-700 shadow-lg flex flex-col items-center py-4 space-y-2 transition-colors duration-300">
    @include('livewire.pedidos.livewire-pedidos-seccion-resumen')
</div>
@include('livewire.pos.pos-modal-confirmacion-venta')
</div>


<script src="{{ asset('js/pedidos.js') }}"></script>
<script src="{{ asset('js/pedidosCalculos.js') }}"></script>

<script>
function formatDateForInput(fecha) {
    if (!fecha) return '';
    // Si la fecha tiene formato ISO con zona horaria, recorta a yyyy-MM-ddTHH:mm
    if (fecha.length >= 16) {
        return fecha.slice(0, 16);
    }
    return fecha;
}

function pedidoForm({ pedido, clientes, bodegas, alistadores, users, productos, detalles }) {
    return {
        pedido,
        clientes,
        bodegas,
        alistadores,
        users,
        productos,
        detalles,
        formatDateForInput, // disponible en Alpine
        productoSeleccionado: null,
        cantidadSeleccionada: 1,
        init() {
            console.log('Datos del pedido:', this.pedido);

            // Forzar actualización de selects después de inicializar datos
            this.$nextTick(() => {
                // Bodega
                const selectBodega = document.querySelector('select[x-model="pedido.bodega_id"]');
                if (selectBodega && this.pedido.bodega_id !== undefined && this.pedido.bodega_id !== null) {
                    selectBodega.value = this.pedido.bodega_id;
                }
                // Alistador
                const selectAlistador = document.querySelector('select[x-model="pedido.alistador_id"]');
                if (selectAlistador && this.pedido.alistador_id !== undefined && this.pedido.alistador_id !== null) {
                    selectAlistador.value = this.pedido.alistador_id;
                }
                // Usuario
                const selectUsuario = document.querySelector('select[x-model="pedido.user_id"]');
                if (selectUsuario && this.pedido.user_id !== undefined && this.pedido.user_id !== null) {
                    selectUsuario.value = this.pedido.user_id;
                }
                // Obtener los Detalles
                this.pedido.detalles = detalles.map(detalle => ({
                    producto_id: detalle.producto_id,
                    cantidad: detalle.cantidad,
                    precio_unitario: detalle.precio_unitario,
                    aplicar_iva: detalle.aplicar_iva,
                    precio_con_iva: detalle.precio_con_iva,
                    subtotal: detalle.subtotal
                }));
            });
        },
        // Obtener el tipo de precio seleccionado
        get tipoPrecio() {
            return this.pedido.tipo_precio;
        },
        // Funciones para manejar los detalles del pedido
        agregarDetalle() {
            this.pedido.detalles.push({
                producto_id: null,
                cantidad: 1,
                precio_unitario: null,
                aplicar_iva: true,
                subtotal: 0
            });
        },
        // Funcion para Remover Algun Detalle
        removeDetalle(index) {
            this.pedido.detalles.splice(index, 1);
        },
        //Funcion para Obtener Precio Segun Tipo
        getPrecio(detalle, tipoPrecio) {
            const prod = this.productos.find(p => p.id == detalle.producto_id);
            if (!prod) return 0;
            switch (this.tipoPrecio) {
                case 'MAYORISTA': return prod.valor_mayorista_producto ?? 0;
                case 'FERRETERO': return prod.valor_ferretero_producto ?? 0;
                default: return prod.valor_detal_producto ?? 0;
            }
        },
        //Funcion para Obtener Precio con IVA
        getPrecioConIva(detalle, tipoPrecio) {
            const prod = this.productos.find(p => p.id == detalle.producto_id);
            let precio = detalle.precio_unitario || this.getPrecio(detalle, tipoPrecio);
            if (detalle.aplicar_iva && prod && prod.iva_producto) {
                precio = precio * (1 + prod.iva_producto / 100);
            }
            return Math.round(precio * 100) / 100;
        },
        //Funcion para Obtener Subtotal
        getSubtotal(detalle) {
            const prod = this.productos.find(p => p.id == detalle.producto_id);
            if (!prod) return 0;
            let precio = this.getPrecioConIva(detalle, this.tipoPrecio);
            return Math.round(precio * detalle.cantidad * 100) / 100;
        },
        //Funcion para Obtener Total General
        getTotal(pedido) {
            return pedido.detalles.reduce((acc, detalle) => acc + this.getSubtotal(detalle), 0);
        },
        getTotalFinal(pedido) {
            if (!pedido || !Array.isArray(pedido.detalles)) return 0;
            // tu lógica aquí, por ejemplo:
            return this.getTotal(pedido) + (pedido.flete || 0) - (pedido.descuento || 0);
        },

        //Funcion para Validar Detalles
        validarDetalles() {
            let errores = [];
            this.pedido.detalles.forEach((detalle, idx) => {
                if (!detalle.producto_id) {
                    errores.push(`Debe seleccionar un producto en la fila ${idx + 1}`);
                }
                if (!detalle.cantidad || detalle.cantidad < 1) {
                    errores.push(`La cantidad debe ser mayor a 0 en la fila ${idx + 1}`);
                }
            });
            return errores;
        },
        enviar() {
            const errores = this.validarDetalles();
            if (errores.length > 0) {
                alert(errores.join('\n'));
                return;
            }
            // Recalcular y sincronizar todos los detalles antes de enviar
            if (Array.isArray(this.pedido.detalles)) {
                this.pedido.detalles.forEach((detalle, idx) => {
                    // Si el usuario modificó manualmente el precio_unitario, se respeta
                    // pero siempre recalculamos precio_con_iva y subtotal
                    detalle.precio_unitario = detalle.precio_unitario ?? this.getPrecio(detalle, this.tipoPrecio);
                    detalle.precio_con_iva = this.getPrecioConIva(detalle, this.tipoPrecio);
                    detalle.subtotal = this.getSubtotal(detalle);
                });
            }
            // Actualizar subtotal y total_a_pagar del pedido
            this.pedido.subtotal = this.getTotal(this.pedido);
            this.pedido.total_a_pagar = this.getTotalFinal(this.pedido);
            console.log('JSON generado para enviar:', JSON.stringify(this.pedido, null, 2));
            console.log('Llamando a método Livewire: editarPedido');
            console.log('Enviando petición editarPedido...');
            this.$wire.editarPedido(this.pedido)
                .then(() => {
                    console.log('Petición editarPedido terminada (éxito)');
                })
                .catch(() => {
                    console.log('Petición editarPedido terminada (error)');
                });
        }
    }
}
</script>
