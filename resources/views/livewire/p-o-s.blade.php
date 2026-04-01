{{-- ...existing code... --}}
<div class="pos-container">

    <div id="alpine-pos-main" x-data="pedidoForm(

        @js($clientes),
        @js($alistadores),
        @js($bodegas),
        @js($productos),
        @js($users),
        @js($empresa),
        @js($bodegaSeleccionada),
        @js($stock), // <-- Pasar el stock inicial desde Livewire
        @js($userId),
        @js($pucs),
        null, // pucSeleccionado
        false, // mostrarModalPago
        false, // mostrarModalAgregarProducto
        'agregar', // accionModalAgregarProducto
        false, // mostrarModalPanelDerecho
        0, // conCuantoPaga
        @js($tipoPrecio),
        @js($esAdmin) // <-- Pasar la variable esAdmin desde Livewire

        //pucSeleccionado: null
    )" x-init="window.addEventListener('limpiar-catalogos', () => {
        limpiarCacheCatalogos();
        location.reload();
    });
    //console.log('Stock inicial desde Livewire:', stock);
    //console.log('prodcutos cargados en Alpine:', productos);

    init();" class="space-y-4">

        {{-- Toast Notification --}}
        <div id="alpine-pos-toast" x-show="mostrarToast" x-transition
            class="fixed top-4 right-4 bg-red-500 text-white px-4 py-2 rounded shadow" style="z-index:9999;">
            <span x-text="mensajeToast"></span>
        </div>

        @include('livewire.pos.pos-panel-izquierdo')
        @include('livewire.pos.pos-panel-derecho')
        {{-- Seccion Modales --}}
        @include('livewire.pos.pos-modal-agregar-producto')
        @include('livewire.pos.pos-modal-pago')
        @include('livewire.pos.pos-modal-confirmacion-venta')


        {{-- Scripts --}}
        <script src="{{ asset('js/pedidos.js') }}"></script>
        <script src="{{ asset('js/pedidosCalculos.js') }}"></script>
        <script src="{{ asset('js/pedidosCalculosStock.js') }}"></script>
        <script src="{{ asset('js/pedidosPaginadoSearch.js') }}"></script>

        @vite(['resources/js/app.js'])

        <script>
            function pedidoForm(
                clientes = [],
                alistadores = [],
                bodegas = [],
                productos = [],
                users = [],
                empresa = null,
                bodegaSeleccionada = 1,
                stock = {}, // <-- Recibe el stock inicial aquí
                userId = null,
                pucs = [],
                pucSeleccionado = null,
                mostrarModalPago = false,
                mostrarModalAgregarProducto = false,
                accionModalAgregarProducto = "agregar",
                mostrarModalPanelDerecho = false,
                conCuantoPaga = 0,
                tipoPrecio = null,
                esAdmin = false

            ) {

                console.log('bodegaSeleccionada:', bodegaSeleccionada);
                console.log('tipoPrecio:', tipoPrecio);
                console.log('esAdmin:', esAdmin);
                // Exponer el stock globalmente para Alpine
                if (window.Alpine) {
                    Alpine.store('pos', {
                        stock: stock
                    });
                } else {
                    document.addEventListener('alpine:init', () => {
                        Alpine.store('pos', {
                            stock: stock
                        });
                    });
                }
                return {
                    stock: stock, // <-- Inicializa la variable stock en Alpine.js
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
                    precioSeleccionado: 0,

                    mostrarModalPago: mostrarModalPago,
                    mostrarModalAgregarProducto: mostrarModalAgregarProducto,
                    accionModalAgregarProducto: accionModalAgregarProducto,
                    mostrarModalPanelDerecho: mostrarModalPanelDerecho,
                    conCuantoPaga: conCuantoPaga,
                    tipoPrecio: tipoPrecio,
                    esAdmin: esAdmin,
                    isLoading: false,

                    pedido: {
                        //codigo: '',
                        cliente_id: null,
                        fecha: '',
                        estado: 'PENDIENTE',
                        estado_pago: 'EN_CARTERA',
                        tipo_pago: '',
                        tipo_precio: tipoPrecio || 'DETAL', // Establecer el tipo de precio inicial basado en el rol del usuario
                        id_puc: null,
                        bodega_id: bodegaSeleccionada ?? (empresa ? empresa.bodega_id : null),
                        observacion: '',
                        observacion_pago: '',
                        subtotal: 0,
                        abono: 0,
                        descuento: 0,
                        flete: 0,
                        total_a_pagar: 0,
                        saldo_pendiente: 0,
                        user_id: userId,
                        aplica_turno: false,
                        turno: null,
                        detalles: [],
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

                        this.$watch('pedido.tipo_precio', () => {
                            // Recalcular precios de detalles al cambiar tipo de precio
                            this.pedido.detalles.forEach(detalle => {
                                const producto = this.productos.find(p => p.id === detalle.producto_id);
                                if (producto) {
                                    const nuevoPrecio = getPrecio(producto, this.pedido.tipo_precio);
                                    detalle.precio_unitario = nuevoPrecio;
                                    detalle.subtotal = getSubtotal(nuevoPrecio, detalle.cantidad);
                                }
                                console.log('Detalle actualizado por cambio de tipo_precio:', detalle);
                            });
                            // Recalcular totales del pedido después de actualizar precios
                            this.calcularTotales();
                        });

                        this.$watch('pedido.descuento', () => {
                            this.calcularTotales();
                        });

                        this.$watch('pedido.flete', () => {
                            this.calcularTotales();
                        });

                        this.$watch('pedido.con_cuanto_paga', () => {
                            this.calcularTotales();
                        });

                        // Suscripción a eventos de Echo dentro de Alpine
                        if (window.Echo) {
                            window.Echo.channel('stock')
                                .listen('.StockActualizado', (e) => {
                                    console.log("¡Evento capturado en el navegador!", e); // <--- AGREGA ESTO
                                    if (Array.isArray(e.productos)) {
                                        console.log("Productos a actualizar:", e.productos);
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
                            productosActualizar.forEach(item => {
                                const id = item.producto_id;
                                const stock = item.stock;
                                // Actualiza el store global de Alpine
                                if (this.$store && this.$store.pos && this.$store.pos.stock) {
                                    this.$store.pos.stock[id] = stock;
                                }
                                // Actualiza el stock en el array de productos locales
                                const prod = this.productos.find(p => p.id === id);
                                if (prod) {
                                    prod.stock = stock;
                                }
                            });
                        } else {
                            // Caso original: id y nuevoStock como argumentos
                            if (this.$store && this.$store.pos && this.$store.pos.stock) {
                                this.$store.pos.stock[productosActualizar] = nuevoStock;
                            }
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
                    agregarDetalle(productoAgregar, cantidadAgregar, precioAgregar) {
                        if (!cantidadAgregar || cantidadAgregar < 1){
                            this.mensajeToast = 'La cantidad debe ser al menos 1.';
                            this.mostrarToast = true;
                            setTimeout(() => this.mostrarToast = false, 3000);
                            return;
                        }
                        //validar quer no este repetido
                        const yaExiste = this.pedido.detalles.some(d => d.producto_id === productoAgregar.id);
                        if (yaExiste) {
                            this.mensajeToast = 'El producto ya está en el carrito. Edita la cantidad desde el carrito.';
                            this.mostrarToast = true;
                            setTimeout(() => this.mostrarToast = false, 3000);
                            return;
                        }

                        const cantidad = parseFloat(cantidadAgregar) || 0;
                        const precioUnitario = parseFloat(precioAgregar) || 0;

                        const nuevoDetalle = {
                            producto_id: productoAgregar.id,
                            cantidad: cantidad,
                            precio_unitario: precioUnitario,
                            subtotal: getSubtotal(precioUnitario, cantidad),
                        };
                        this.pedido.detalles.push(nuevoDetalle);
                        console.log('Detalle agregado:', nuevoDetalle);
                        this.calcularTotales();

                    },

                    editarDetalle(index, cantidadAModificar, valorUnitarioAModificar) {
                        if (!cantidadAModificar || cantidadAModificar < 1){
                            this.mensajeToast = 'La cantidad debe ser al menos 1.';
                            this.mostrarToast = true;
                            setTimeout(() => this.mostrarToast = false, 3000);
                            return;
                        }
                        const cantidad = parseFloat(cantidadAModificar) || 0;
                        const valorUnitario = parseFloat(valorUnitarioAModificar) || 0;

                        const detalle = this.pedido.detalles[index];
                        if (detalle) {
                            detalle.cantidad = cantidad;
                            detalle.precio_unitario = valorUnitario;
                            detalle.subtotal = getSubtotal(valorUnitario, cantidad);
                        }
                        console.log('Detalle editado:', detalle);

                        this.calcularTotales();
                    },

                    removeDetalle(index) {
                        this.pedido.detalles.splice(index, 1);
                        this.calcularTotales();

                    },

                    //Funcion para Enviar Pedido
                    enviar() {
                        //console.log('Ejecutando enviar()');
                        const errores = validarRegistros(this.pedido);
                        if (errores.length > 0) {
                            this.mensajeToast = errores.join("\n");
                            //console.log('Mostrando Toast con mensaje:', this.mensajeToast);
                            this.mostrarToast = true;
                            setTimeout(() => this.mostrarToast = false, 3000);
                            return;
                        }

                        const pedidoSalida = {
                            //codigo: this.pedido.codigo,
                            cliente_id: this.pedido.cliente_id,
                            fecha: this.pedido.fecha,
                            estado: this.pedido.estado,
                            estado_pago: this.pedido.estado_pago,
                            tipo_pago: this.pedido.tipo_pago,
                            tipo_precio: this.pedido.tipo_precio,
                            id_puc: this.pedido.id_puc,
                            bodega_id: this.pedido.bodega_id,
                            observacion: this.pedido.observacion,
                            observacion_pago: this.pedido.observacion_pago,
                            aplica_turno: this.pedido.aplica_turno,
                            subtotal: this.pedido.subtotal,
                            abono: this.pedido.abono,
                            descuento: this.pedido.descuento,
                            flete: this.pedido.flete,
                            total_a_pagar: this.pedido.total_a_pagar,
                            saldo_pendiente: this.pedido.saldo_pendiente,
                            detalles: this.pedido.detalles,
                        }
                        console.log('Pedido a enviar:', pedidoSalida);
                        this.isLoading = true;
                        console.log('Enviando pedido...');

                        const payload = {
                            productos: pedidoSalida.detalles.map(d => ({
                                producto_id: d.producto_id,
                                bodega_id: pedidoSalida.bodega_id
                            })),
                            bodega_id: pedidoSalida.bodega_id
                        };
                        console.log('Payload enviado a /api/recalcular-stock:', payload);
                        // Guardar el pedido primero y luego recalcular el stock por API
                        this.$wire.guardarPedido(pedidoSalida).then(() => {
                            this.isLoading = false;
                            this.mostrarModalPago = false;
                            console.log('Pedido Enviado');
                            // Después de guardar el pedido, recalcula el stock por API
                            fetch('/api/recalcular-stock', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                                },
                                body: JSON.stringify(payload)


                            });
                        });
                        localStorage.removeItem("pedidoPOS");

                    },

                    tipoPrecio(tipoPrecio) {
                        if(tipoPrecio === 'DETAL'){
                            return getPrecio(this.productoSeleccionado, 'DETAL');
                        }else{
                            return getPrecio(this.productoSeleccionado, 'MAYORISTA');
                        }
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

                    getCambio(totalAPagar, conCuantoPaga) {
                        const cambio = Number(conCuantoPaga) - Number(totalAPagar);
                        return cambio >= 0 ? cambio : 0;
                    },

                    calcularTotales() {
                        this.pedido.subtotal = getTotal(this.pedido);
                        this.pedido.total_a_pagar = getTotalAPagar(this.pedido);
                        this.pedido.saldo_pendiente = getSaldoPendiente(this.pedido);
                        this.pedido.cambio = this.getCambio(this.pedido.total_a_pagar, Number(this.pedido.con_cuanto_paga));
                        console.log('Totales recalculados:', {
                            subtotal: this.pedido.subtotal,
                            total_a_pagar: this.pedido.total_a_pagar,
                            saldo_pendiente: this.pedido.saldo_pendiente,
                            con_cuanto_paga: this.pedido.con_cuanto_paga,
                            cambio: this.pedido.cambio
                        });
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
                    get productosPorPagina() {
                        return window.productosFiltradosPaginados.getProductosPorPagina(this.pedido.cliente_id);
                    },
                    set productosPorPagina(valor) {
                        window.productosFiltradosPaginados.setProductosPorPagina(this.pedido.cliente_id, valor);
                        this.paginaProductos = 1; // Reiniciar a primera página
                    },
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
                    // Obtener array de números de páginas para paginación numérica
                    get paginasArray() {
                        return window.productosFiltradosPaginados.getPaginasArray(this.totalPaginasProductos);
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
    </div>
</div>
