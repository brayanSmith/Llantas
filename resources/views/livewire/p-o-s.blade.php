{{-- ...existing code... --}}
<div id="alpine-pos" x-data="pedidoForm(
    @js($clientes),
    @js($alistadores),
    @js($bodegas),
    @js($productos),
    @js($users),
    @js($empresa),
    @js($bodegaSeleccionada),
    @js($userId)
)" x-init="
window.addEventListener('limpiar-catalogos', () => {
    limpiarCacheCatalogos();
    location.reload();
});
init();" class="space-y-4">

    <div id="alpine-pos" x-show="mostrarToast" x-transition
        class="fixed top-4 right-4 bg-red-500 text-white px-4 py-2 rounded shadow" style="z-index:9999;">
        <span x-text="mensajeToast"></span>
    </div>
    @include('livewire.pos.pos-panel-izquierdo')
    @include('livewire.pos.pos-panel-derecho')
    @include('livewire.pos.pos-modal-confirmacion-venta')
</div>
{{-- ...existing code... --}}
<script src="{{ asset('js/pedidos.js') }}"></script>
<script src="{{ asset('js/pedidosCalculos.js') }}"></script>
<script src="{{ asset('js/pedidosCalculosStock.js') }}"></script>
<script src="{{ asset('js/pedidosPaginadoSearch.js') }}"></script>

@vite(['resources/js/app.js'])

<script>
    function pedidoForm(clientes = [], alistadores = [], bodegas = [], productos = [], users = [], empresa = null,
        bodegaSeleccionada = null, stockBodegas = [], userId = null) {

        return {
            // --- Funciones para limpiar catálogos cacheados ---
            // Eliminadas funciones de limpieza de cache localStorage
            mostrarToast: false,
            mensajeToast: '',
            error: '',
            success: '',
            clientes: clientes,
            alistadores: alistadores,
            bodegas: bodegas,
            productos: productos,
            users: users,
            empresa: empresa,
            bodegaSeleccionada: bodegaSeleccionada,
            totalCantidadProductos: 0, // Nueva propiedad reactiva
            productoSeleccionado: null,
            cantidadSeleccionada: 1,
            pedido: {
                codigo: '',
                fe: '',
                cliente_id: null,
                fecha: '',
                dias_plazo_vencimiento: 30,
                fecha_vencimiento: '',
                ciudad: '',
                estado: 'PENDIENTE',
                stock_retirado: false,
                en_cartera: false,
                metodo_pago: 'CREDITO',
                tipo_precio: 'FERRETERO',
                tipo_venta: 'REMISIONADA',
                estado_pago: 'EN_CARTERA',
                estado_cartera: 'CARTERA_AL_DIA',
                estado_venta: 'VENTA',
                estado_vencimiento: 'AL_DIA',
                // Siempre asignar un valor válido a bodega_id
                bodega_id: bodegaSeleccionada ?? (empresa ? empresa.bodega_id : null),
                primer_comentario: '',
                subtotal: 0,
                abono: 0,
                descuento: 0,
                flete: 0,
                total_a_pagar: 0,
                saldo_pendiente: 0,
                user_id: userId,
                alistador_id: userId,
                detalles: [],
                created_at: '',
                updated_at: '',
                iva: 0
            },
            init() {
                const pedidoGuardado = localStorage.getItem('pedidoPOS');
                if (pedidoGuardado) {
                    this.pedido = JSON.parse(pedidoGuardado);
                    this.totalCantidadProductos = this.pedido.detalles.reduce((acc, d) => acc + (parseFloat(d
                        .cantidad) || 0), 0);
                }
                // Watcher automático para guardar cualquier cambio en pedido
                if (typeof this.$watch === 'function') {
                    this.$watch('pedido', value => {
                        localStorage.setItem('pedidoPOS', JSON.stringify(value));
                    }, {
                        deep: true
                    });
                }
                // Suscripción a eventos de Echo dentro de Alpine
                if (window.Echo) {
                    window.Echo.channel('stock')
                        .listen('.StockActualizado', (e) => {
                            if (Array.isArray(e.productos)) {
                                this.modificarStockProducto(e.productos);
                                // Aquí puedes actualizar this.productos si lo necesitas:
                                // this.productos = e.productos;
                            }
                        });
                }
            },
            // Función para modificar el stock de productos, ahora soporta un array de productos con id y stock
            modificarStockProducto(productosActualizar, nuevoStock = null) {
                if (Array.isArray(productosActualizar)) {
                    // Soporta objetos con 'id' o 'producto_id'
                    productosActualizar.forEach(item => {
                        const id = item.producto_id;
                        const stock = item.stock;
                        const prod = this.productos.find(p => p.id === id);
                        if (prod) {
                            prod.stock = stock;
                        }
                    });
                } else {
                    // Caso original: id y nuevoStock como argumentos
                    const prod = this.productos.find(p => p.id === productosActualizar);
                    if (prod) {
                        prod.stock = nuevoStock;
                    }
                }
            },

            // Obtener el tipo de precio seleccionado
            get tipoPrecio() {
                return this.pedido.tipo_precio;
            },
            // Funciones para manejar los detalles del pedido
            agregarDetalle() {
                const precio = getPrecio(this.productoSeleccionado, this.pedido.tipo_precio);
                const precioConIva = getPrecioConIva(this.productoSeleccionado, precio, true);
                const subTotal = getSubtotal(precio, this.cantidadSeleccionada);

                agregarDetalleReutilizable(
                    this.pedido,
                    this.productoSeleccionado,
                    this.cantidadSeleccionada,
                    precio,
                    false,
                    precio,
                    subTotal,
                    (msg) => {
                        this.mensajeToast = msg;
                        this.mostrarToast = true;
                        setTimeout(() => this.mostrarToast = false, 3000);
                    }
                );
                const total = getTotal(this.pedido);

                console.log('Detalles del pedido:', JSON.parse(JSON.stringify(this.pedido.detalles)));
                // Imprimir el array de detalles actualizado
                console.log('Detalles del pedido:', this.pedido.detalles);
                // Imprimir el total de productos
                console.log('Total cantidad productos:', this.totalCantidadProductos);
                // Imprimir el producto seleccionado
                console.log('Producto seleccionado:', this.productoSeleccionado);
                // Imprimir el pedido completo
                console.log('Pedido completo:', this.pedido);
                // Actualizar total y guardar en memoria después de agregar
                this.totalCantidadProductos = this.pedido.detalles.reduce((acc, d) => acc + (parseFloat(d.cantidad) || 0), 0);

                this.guardarPedidoEnMemoria();
            },
            removeDetalle(index) {
                //const producto = this.pedido.detalles[index];
                //const productoId = producto ? producto.producto_id : null;
                removeDetalleReutilizable(
                    this.pedido,
                    index,
                    (total) => {
                        this.totalCantidadProductos = total;
                        this.guardarPedidoEnMemoria();
                    }
                );
            },
            actualizarCantidad(index) {
                actualizarCantidadReutilizable(
                    this.pedido,
                    index,
                    (total) => {
                        this.totalCantidadProductos = total;
                        this.guardarPedidoEnMemoria();
                    }
                );
            },

            //Funcion para Enviar Pedido
            enviar() {
                enviarPedidoReutilizable(
                    this.pedido,
                    (pedido) => {
                        const payload = {
                            productos: pedido.detalles.map(d => ({
                                producto_id: d.producto_id,
                                bodega_id: pedido.bodega_id
                            })),
                            bodega_id: pedido.bodega_id
                        };
                        console.log('Payload enviado a /api/recalcular-stock:', payload);
                        // Guardar el pedido primero y luego recalcular el stock por API
                        this.$wire.guardarPedido(pedido).then(() => {
                            // Después de guardar el pedido, recalcula el stock por API
                            fetch('/api/recalcular-stock', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')
                                        .content
                                },
                                body: JSON.stringify(payload)
                            });
                        });
                    }
                );
            },

            getTotal() {
                const total = getTotal(this.pedido);
                console.log('Detalles del pedido:', JSON.parse(JSON.stringify(this.pedido.detalles)));
                console.log('Subtotal calculado:', total);
                return total;
            },
            getTotalAPagar() {
                return getTotalAPagar(this.pedido);
            },
            getSaldoPendiente() {
                return getSaldoPendiente(this.pedido);
            },

            // Función para resetear todos los datos del pedido
            resetPedido() {
                this.pedido = crearPedidoVacio(this.bodegaSeleccionada, this.empresa, userId);
                localStorage.removeItem('pedidoPOS');
                this.totalCantidadProductos = 0;
                // Emitir evento para sincronizar Tom Select
                window.dispatchEvent(new CustomEvent('reset-tomselect-cliente'));
            },
            // --- Paginación de productos (Alpine.js) ---
            paginaProductos: 1,
            productosPorPagina: 10,
            get totalPaginasProductos() {
                return productosFiltradosPaginados.getTotalPaginasProductos(this.productos, this
                    .productosPorPagina);
            },
            get productosPaginados() {
                return productosFiltradosPaginados.getProductosPaginados(this.productos, this.paginaProductos, this
                    .productosPorPagina);
            },
            cambiarPaginaProductos(nuevaPagina) {
                if (nuevaPagina >= 1 && nuevaPagina <= this.totalPaginasProductos) {
                    this.paginaProductos = nuevaPagina;
                }
            },
            // --- Buscador de productos (Alpine.js) ---
            search: '',
            setSearch(valor) {
                this.search = valor;
                this.paginaProductos = 1;
            },
            get productosFiltrados() {
                return productosFiltradosPaginados.getProductosFiltrados(this.productos, this.search);
            },
            //Paguinación de productos filtrados
            get productosFiltradosPaginados() {
                return productosFiltradosPaginados.getProductosFiltradosPaginados(this.productos, this.search, this
                    .paginaProductos, this
                    .productosPorPagina);
            },
            get totalPaginasProductos() {
                return productosFiltradosPaginados.getTotalPaginasProductosFiltrados(this.productos, this.search,
                    this.productosPorPagina);
            },

            // Puedes usar productosFiltrados en lugar de productos para mostrar la lista filtrada
            get clienteSeleccionado() {
                return this.clientes.find(c => c.id == this.pedido.cliente_id) || {};
            },
            guardarPedidoEnMemoria() {
                localStorage.setItem('pedidoPOS', JSON.stringify(this.pedido));
            }
        }
    }
</script>
