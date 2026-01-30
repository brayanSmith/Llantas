{{-- ...existing code... --}}
    <div
        x-data="pedidoForm(
            (localStorage.getItem('clientesPOS') ? JSON.parse(localStorage.getItem('clientesPOS')) : @js($clientes)),
            @js($alistadores),
            @js($bodegas),
            (localStorage.getItem('productosPOS') ? JSON.parse(localStorage.getItem('productosPOS')) : @js($productos)),
            @js($users),
            @js($empresa),
            @js($bodegaSeleccionada),
            //(localStorage.getItem('stockBodegasPOS') ? JSON.parse(localStorage.getItem('stockBodegasPOS')) : @js($stockBodegas)),
            @js($userId)
        )"
        x-init="
            if (!localStorage.getItem('productosPOS')) {
                localStorage.setItem('productosPOS', JSON.stringify(productos));
            }
            if (!localStorage.getItem('clientesPOS')) {
                localStorage.setItem('clientesPOS', JSON.stringify(clientes));
            }
            /*if (!localStorage.getItem('stockBodegasPOS')) {
                localStorage.setItem('stockBodegasPOS', JSON.stringify(stockBodegas));
            }*/
            window.addEventListener('limpiar-catalogos', () => {
                limpiarCacheCatalogos();
                location.reload();
            });
            init();
        "
        class="space-y-4"
    >
    <div x-show="mostrarToast" x-transition class="fixed top-4 right-4 bg-red-500 text-white px-4 py-2 rounded shadow" style="z-index:9999;">
        <span x-text="mensajeToast"></span>
    </div>
        @include('livewire.pos.pos-panel-izquierdo')
         @include('livewire.pos.pos-panel-derecho')
        @include('livewire.pos.pos-modal-confirmacion-venta')
    </div>
{{-- ...existing code... --}}
<script>

function pedidoForm(clientes = [], alistadores = [], bodegas = [], productos = [], users = [], empresa = null, bodegaSeleccionada = null, stockBodegas = [], userId = null) {
    return {
                // --- Funciones para limpiar catálogos cacheados ---
                limpiarCacheProductos() {
                    localStorage.removeItem('productosPOS');
                },
                limpiarCacheClientes() {
                    localStorage.removeItem('clientesPOS');
                },
                limpiarCacheStockBodegas() {
                    //localStorage.removeItem('stockBodegasPOS');
                },
                limpiarCacheCatalogos() {
                    this.limpiarCacheProductos();
                    this.limpiarCacheClientes();
                    this.limpiarCacheStockBodegas();
                },
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
        stockBodegas: stockBodegas,
        totalCantidadProductos: 0, // Nueva propiedad reactiva
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
            tipo_venta: 'ELECTRONICA',
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
                this.totalCantidadProductos = this.pedido.detalles.reduce((acc, d) => acc + (parseFloat(d.cantidad) || 0), 0);
            }
            // Watcher automático para guardar cualquier cambio en pedido
            if (typeof this.$watch === 'function') {
                this.$watch('pedido', value => {
                    localStorage.setItem('pedidoPOS', JSON.stringify(value));
                }, { deep: true });
            }
        },
        // Obtener el tipo de precio seleccionado
        get tipoPrecio() {
            return this.pedido.tipo_precio;
        },
        // Funciones para manejar los detalles del pedido
        agregarDetalle() {
            const yaExiste = this.pedido.detalles.some(
                d => d.producto_id === this.productoSeleccionado.id
            );
            if (yaExiste) {
                this.mensajeToast = 'Este producto ya se encuentra en el carrito. Si desea modificar la cantidad, hágalo manualmente desde ahí.';
                this.mostrarToast = true;
                setTimeout(() => this.mostrarToast = false, 3000);
                return;
            }
            /*const stockDescontado = this.getStockTotal(
                this.productoSeleccionado.id,
                this.empresa.bodega_id,
                this.cantidadSeleccionada,
                'agregar'
            );*/
            const detalle = {
                producto_id: this.productoSeleccionado.id,
                cantidad: this.cantidadSeleccionada,
                precio_unitario: this.getPrecio({ producto_id: this.productoSeleccionado.id }, this.tipoPrecio),
                aplicar_iva: true,
                iva: this.productoSeleccionado.iva_producto || 0,
                precio_con_iva: this.getPrecioConIva(
                    { producto_id: this.productoSeleccionado.id },
                    this.tipoPrecio,
                    true,
                    this.productoSeleccionado.iva_producto || 0
                ),
                subtotal: 0,
                //stockDescontado: stockDescontado
            };
            // Descontar stock visualmente

            //console.log('stockEntry:', stockDescontado);
            //
            this.pedido.detalles.push(detalle);
            this.totalCantidadProductos = this.pedido.detalles.reduce((acc, d) => acc + (parseFloat(d.cantidad) || 0), 0);
            this.guardarPedidoEnMemoria();
        },
        // Funcion para Remover Algun Detalle
        removeDetalle(index) {
            // Devolver stock visualmente
            const detalle = this.pedido.detalles[index];
            /*detalle.stockDescontado = this.getStockTotal(
                this.detalle.producto_id,
                this.empresa.bodega_id,
                this.detalle.cantidad,
                'remover'
            );*/
            //console.log('Stock después de remover:', detalle.stockDescontado);
            this.pedido.detalles.splice(index, 1);
            this.totalCantidadProductos = this.pedido.detalles.reduce((acc, d) => acc + (parseFloat(d.cantidad) || 0), 0);
            this.guardarPedidoEnMemoria();
        },
        // Funcion para actualizar la cantidad de un detalle
        actualizarCantidad(index) {
            const detalle = this.pedido.detalles[index];
            /*detalle.stockDescontado = this.getStockTotal(
                this.detalle.producto_id,
                this.empresa.bodega_id,
                this.detalle.cantidad,
                'actualizar'
            );*/
            //console.log('Stock Descontado Actualizado:', detalle.stockDescontado);
            // También puedes actualizar otras propiedades si lo necesitas
            this.guardarPedidoEnMemoria();
        },

        //Funcion para Enviar Pedido
        enviar() {
            // Validar que el carrito no esté vacío
            if (!this.pedido.detalles || this.pedido.detalles.length === 0) {
                this.mensajeToast = 'El carrito está vacío. Agregue al menos un producto antes de enviar el pedido.';
                this.mostrarToast = true;
                setTimeout(() => this.mostrarToast = false, 3000);
                return;
            }
            // Validar que se haya seleccionado un cliente
            if (!this.pedido.cliente_id) {
                this.mensajeToast = 'Debe seleccionar un cliente antes de enviar el pedido.';
                this.mostrarToast = true;
                setTimeout(() => this.mostrarToast = false, 3000);
                return;
            }
            const errores = this.validarDetalles();
            if (errores.length > 0) {
                alert(errores.join('\n'));
                return;
            }
            // Actualizar saldo pendiente antes de guardar
            this.getSaldoPendiente();
            console.log('JSON generado para enviar:', JSON.stringify(this.pedido, null, 2));
            console.log('Llamando a método Livewire: guardarPedido');
            this.$wire.guardarPedido(this.pedido);
            localStorage.removeItem('pedidoPOS');
        },
        //Funcion para Obtener Precio Segun Tipo
        getPrecio(detalle, tipoPrecio = 'FERRETERO') {
            const prod = this.productos.find(p => p.id == detalle.producto_id);
            if (!prod) return 0;
            switch (this.tipoPrecio) {
                case 'MAYORISTA': return prod.valor_mayorista_producto ?? 0;
                case 'FERRETERO': return prod.valor_ferretero_producto ?? 0;
                default: return prod.valor_detal_producto ?? 0;
            }
        },
        //Funcion para Obtener Precio con IVA
        getPrecioConIva(detalle, tipoPrecio, aplicarIva, ivaProducto) {
            const prod = this.productos.find(p => p.id == detalle.producto_id);
            let precio = detalle.precio_unitario || this.getPrecio(detalle, tipoPrecio);
            if (aplicarIva && prod && ivaProducto) {
                precio = precio * (1 + ivaProducto / 100);
            }
            return Math.round(precio * 100) / 100;
        },
        //Funcion para Obtener Subtotal de los productos
        getSubtotal(detalle) {
            const prod = this.productos.find(p => p.id == detalle.producto_id);
            if (!prod) return 0;
            let precio = this.getPrecioConIva(detalle, this.tipoPrecio, detalle.aplicar_iva, detalle.iva);
            return Math.round(precio * detalle.cantidad * 100) / 100;
        },
        //Funcion para Obtener Total General
        getTotal() {
            const total = this.pedido.detalles.reduce((acc, detalle) => acc + this.getSubtotal(detalle), 0);
            this.pedido.subtotal = total;
            return total;
        },
        //Funcion para Obtener Total a Pagar
        getTotalAPagar() {
            const subtotal = this.getTotal();
            const flete = parseFloat(this.pedido.flete) || 0;
            const totalAPagar = subtotal + flete;
            this.pedido.total_a_pagar = totalAPagar;
            return totalAPagar;
        },
        //Funcion para obtener el saldo PENDIENTE
        getSaldoPendiente() {
            const totalAPagar = this.getTotalAPagar();
            const abono = parseFloat(this.pedido.abono) || 0;
            const saldoPendiente = totalAPagar - abono;
            this.pedido.saldo_pendiente = saldoPendiente;
            return saldoPendiente;
        },

        //Funcion para obtener el StockDisponible
        getStockDisponible(idProducto, idBodega = this.empresa.bodega_id) {
            const stockEntry = this.stockBodegas.find(entry =>
                entry.bodega_id === idBodega && entry.producto_id === idProducto
            );
            return stockEntry ? stockEntry.stock : 0;
        },
        //Funcion para obtener el Stock Total (incluye descuentos visuales)
        getStockTotal(idProducto, idBodega = this.empresa.bodega_id, cantidad, accion) {
            let stockInicial = Number(this.getStockDisponible(idProducto, idBodega));
            let stockTotal = stockInicial;
            cantidad = Number(cantidad) || 0;
            if (accion === 'agregar') {
                stockTotal = stockInicial - cantidad;
            } else if (accion === 'remover') {
                stockTotal = stockInicial;
            } else if (accion === 'actualizar') {
                // Si tienes un valor especial para actualizar, pon la lógica aquí
                stockTotal = stockInicial - cantidad; // O la lógica que necesites
            }
            return stockTotal;
        },
        // Función para resetear todos los datos del pedido
        resetPedido() {
            this.pedido = {
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
                tipo_venta: 'ELECTRONICA',
                estado_pago: 'EN_CARTERA',
                estado_cartera: 'CARTERA_AL_DIA',
                estado_venta: 'VENTA',
                estado_vencimiento: 'AL_DIA',
                bodega_id: this.bodegaSeleccionada ?? (this.empresa ? this.empresa.bodega_id : null),
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
            };
            localStorage.removeItem('pedidoPOS');
            this.totalCantidadProductos = 0;
            // Emitir evento para sincronizar Tom Select
            window.dispatchEvent(new CustomEvent('reset-tomselect-cliente'));
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
        // --- Paginación de productos (Alpine.js) ---
        paginaProductos: 1,
        productosPorPagina: 10,
        get totalPaginasProductos() {
            return Math.ceil(this.productos.length / this.productosPorPagina);
        },
        get productosPaginados() {
            const inicio = (this.paginaProductos - 1) * this.productosPorPagina;
            return this.productos.slice(inicio, inicio + this.productosPorPagina);
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
            if (!this.search) return this.productos;
            // Permite búsqueda por palabras clave separadas por espacio
            const palabras = this.search.toLowerCase().split(/\s+/).filter(Boolean);
            return this.productos.filter(p => {
                // Unir todos los campos relevantes en un solo string
                const textoProducto = [
                    p.nombre_producto,
                    p.codigo_producto,
                    p.concatenar_codigo_nombre
                ].filter(Boolean).join(' ').toLowerCase();
                // Cada palabra debe estar presente en el string
                return palabras.every(palabra => textoProducto.includes(palabra));
            });
        },
        get productosFiltradosPaginados() {
            const inicio = (this.paginaProductos - 1) * this.productosPorPagina;
            return this.productosFiltrados.slice(inicio, inicio + this.productosPorPagina);
        },
        get totalPaginasProductos() {
            return Math.ceil(this.productosFiltrados.length / this.productosPorPagina);
        },
        // Puedes usar productosFiltrados en lugar de productos para mostrar la lista filtrada
        get clienteSeleccionado() {
            return this.clientes.find(c => c.id == this.pedido.cliente_id) || {};
        },

        productoSeleccionado: null,
        cantidadSeleccionada: 1,

        guardarPedidoEnMemoria() {
            localStorage.setItem('pedidoPOS', JSON.stringify(this.pedido));
        }
    }
}
</script>
