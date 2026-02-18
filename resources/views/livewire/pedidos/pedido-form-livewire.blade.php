<div x-data="pedidoForm({

    productoIngresado: null,
    cantidadIngresada: 1,
    valorIngresado: 0,
    subTotalIngresado: 0,

    pedido: @js($pedidoEncontrado),
    clientes: @js($clientes),
    bodegas: @js($bodegas),
    alistadores: @js($alistadores),
    users: @js($users),
    productos: @js($productos),
    detalles: @js($detalles)
})" x-init="init()" class="space-y-4">

    <div
        class="flex items-center justify-center gap-4 bg-white dark:bg-gray-900 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 p-6 mb-6">
        <label class="block text-sm font-semibold text-gray-700 mb-0 text-center whitespace-nowrap">Estado Venta</label>
        <div class="flex gap-4">
            <label class="inline-flex items-center cursor-pointer">
                <input type="radio" x-model="pedido.estado_venta" value="COTIZACION"
                    class="form-radio text-blue-600 focus:ring-blue-400" />
                <span class="ml-2">COTIZACION</span>
            </label>
            <label class="inline-flex items-center cursor-pointer">
                <input type="radio" x-model="pedido.estado_venta" value="VENTA"
                    class="form-radio text-blue-600 focus:ring-blue-400" />
                <span class="ml-2">VENTA</span>
            </label>
        </div>
    </div>

    <div x-show="pedido.estado_venta !== 'COTIZACION'">
        @include('livewire.pedidos.livewire-pedidos-seccion-general')
    </div>

    <div x-show="pedido.estado_venta === 'COTIZACION'">
        @include('livewire.pedidos.livewire-pedidos-seccion-general-cotizacion')
    </div>

    <div class="sticky top-16 z-10">
        @include('livewire.pedidos.livewire-pedidos-seccion-detalle-agregar')
    </div>

    <div>
        @include('livewire.pedidos.livewire-pedidos-seccion-detalle')
    </div>

    <div
        class="sticky bottom-0 left-0 w-full z-30 bg-white/80 dark:bg-gray-900/80 backdrop-blur border-t border-gray-200 dark:border-gray-700 shadow-lg flex flex-col items-center py-4 space-y-2 transition-colors duration-300">
        @include('livewire.pedidos.livewire-pedidos-seccion-resumen')
    </div>
    @include('livewire.pedidos.pedido-modal-venta')
</div>


<script src="{{ asset('js/pedidos.js') }}"></script>
<script src="{{ asset('js/pedidosCalculos.js') }}"></script>

<script>
    function formatDateForInput(fecha) {
        if (!fecha) return '';
        // Si la fecha tiene formato ISO con zona horaria, recorta a yyyy-MM-ddTHH:mm
        // Ejemplo: "2025-01-05T14:00:00.000000Z" => "2025-01-05T14:00"
        if (typeof fecha === 'string' && fecha.length >= 16) {
            return fecha.slice(0, 16);
        }
        return fecha;
    }

    function pedidoForm({
        pedido,
        clientes,
        bodegas,
        alistadores,
        users,
        productos,
        detalles,
        cantidadIngresada = 1,
        valorIngresado = 0,
        subTotalIngresado = 0,
        productoIngresado = null
    }) {
        return {
            pedido,
            clientes,
            bodegas,
            alistadores,
            users,
            productos,
            detalles,
            cantidadIngresada,
            valorIngresado,
            subTotalIngresado,
            productoIngresado,
            detalleEditandoIndex: null,
            formatDateForInput, // disponible en Alpine
            productoSeleccionado: null,
            cantidadSeleccionada: 1,
            isLoading: false,
            init() {
                console.log('Datos del pedido:', this.pedido);

                // Asegurar que flete y descuento sean números
                this.pedido.flete = parseFloat(this.pedido.flete) || 0;
                this.pedido.descuento = parseFloat(this.pedido.descuento) || 0;

                // Guardar los detalles originales para recalculo de stock
                this.detallesOriginales = detalles.map(detalle => ({
                    producto_id: String(detalle.producto_id),
                    cantidad: detalle.cantidad,
                    precio_unitario: detalle.precio_unitario,
                    aplicar_iva: false,
                    precio_con_iva: detalle.precio_unitario,
                    subtotal: detalle.cantidad * detalle.precio_unitario
                }));

                // Forzar actualización de selects después de inicializar datos
                this.$nextTick(() => {
                    // Cliente
                    const selectCliente = document.querySelector('select[x-model="pedido.cliente_id"]');
                    if (selectCliente && this.pedido.cliente_id !== undefined && this.pedido.cliente_id !==
                        null) {
                        selectCliente.value = this.pedido.cliente_id;
                    }
                    // Bodega
                    const selectBodega = document.querySelector('select[x-model="pedido.bodega_id"]');
                    if (selectBodega && this.pedido.bodega_id !== undefined && this.pedido.bodega_id !== null) {
                        selectBodega.value = this.pedido.bodega_id;
                    }
                    // Alistador
                    const selectAlistador = document.querySelector('select[x-model="pedido.alistador_id"]');
                    if (selectAlistador && this.pedido.alistador_id !== undefined && this.pedido
                        .alistador_id !== null) {
                        selectAlistador.value = this.pedido.alistador_id;
                    }
                    // Usuario
                    const selectUsuario = document.querySelector('select[x-model="pedido.user_id"]');
                    if (selectUsuario && this.pedido.user_id !== undefined && this.pedido.user_id !== null) {
                        selectUsuario.value = this.pedido.user_id;
                    }
                    // Obtener los Detalles (normalizar producto_id a string)
                    this.pedido.detalles = detalles.map(detalle => ({
                        producto_id: String(detalle.producto_id),
                        cantidad: detalle.cantidad,
                        precio_unitario: detalle.precio_unitario,
                        aplicar_iva: false,
                        precio_con_iva: detalle.precio_unitario,
                        subtotal: detalle.cantidad * detalle.precio_unitario
                    }));
                });
            },
            // Obtener el tipo de precio seleccionado
            get tipoPrecio() {
                return this.pedido.tipo_precio;
            },
            // Funciones para manejar los detalles del pedido
            agregarDetalle(productoId, cantidad, valorUnitario) {
                if (!productoId) {
                    alert('Debe seleccionar un producto');
                    return;
                }
                if (!cantidad || cantidad < 1) {
                    alert('La cantidad debe ser mayor a 0');
                    return;
                }
                // en caso de que el producto ya exista en el pedido, alertar y no agregar
                const detalleExistente = this.pedido.detalles.find(detalle => detalle.producto_id === String(
                    productoId));
                if (detalleExistente) {
                    alert('El producto ya está agregado al pedido');
                    return;
                }
                // Calcular subtotal
                const cantidadNum = parseFloat(cantidad) || 0;
                const valorUnitarioNum = parseFloat(valorUnitario) || 0;
                const subTotalCalculado = Math.round(cantidadNum * valorUnitarioNum * 100) / 100;

                this.pedido.detalles.push({
                    producto_id: productoId,
                    cantidad: cantidadNum,
                    precio_unitario: valorUnitarioNum,
                    aplicar_iva: false,
                    subtotal: subTotalCalculado
                });
                // Limpiar campos de ingreso
                this.productoIngresado = null;
                this.cantidadIngresada = 1;
                this.valorIngresado = 0;
                this.subTotalIngresado = 0;

                // Limpiar Tom Select
                setTimeout(() => {
                    const selectProducto = document.getElementById('select-producto');
                    if (selectProducto && selectProducto.tomselect) {
                        selectProducto.tomselect.clear();
                    }
                }, 50);
            },

            // Funcion para Remover Algun Detalle
            removeDetalle(index) {
                this.pedido.detalles.splice(index, 1);
            },
            // Funcion para Traer Detalle a los campos de entrada para editar
            traerDetalle(index) {
                const detalle = this.pedido.detalles[index];
                if (detalle) {
                    this.productoIngresado = detalle.producto_id;
                    this.cantidadIngresada = detalle.cantidad;
                    this.valorIngresado = detalle.precio_unitario;
                    this.detalleEditandoIndex = index;
                    // Actualizar Tom Select manualmente
                    this.$nextTick(() => {
                        const selectProducto = document.querySelector('select[id="select-producto"]');
                        if (selectProducto && selectProducto.tomselect) {
                            selectProducto.tomselect.setValue(detalle.producto_id);
                        }
                    });

                    console.log('Detalle cargado para editar:', detalle);
                }
            },
            //Funcion para Actualizar Valores del Detalle
            actualizarValoresDetalle(index, productoId, cantidad, valorUnitario) {
                const detalle = this.pedido.detalles[index];
                if (!productoId) {
                    alert('Debe seleccionar un producto');
                    return;
                }
                if (!cantidad || cantidad < 1) {
                    alert('La cantidad debe ser mayor a 0');
                    return;
                }
                //En caso de que el producto ya exista en el pedido (y no sea el mismo detalle que se está editando), alertar y no actualizar
                const detalleExistente = this.pedido.detalles.find((d, i) => d.producto_id === String(productoId) &&
                    i !== index);
                if (detalleExistente) {
                    alert('El producto ya está agregado al pedido');
                    return;
                }
                // Calcular subtotal
                const cantidadNum = parseFloat(cantidad) || 0;
                const valorUnitarioNum = parseFloat(valorUnitario) || 0;
                const subTotalCalculado = Math.round(cantidadNum * valorUnitarioNum * 100) / 100;

                detalle.producto_id = productoId;
                detalle.cantidad = parseFloat(cantidad) || 0;
                detalle.precio_unitario = parseFloat(valorUnitario) || 0;
                detalle.subtotal = subTotalCalculado;
                // Limpiar campos de ingreso
                this.productoIngresado = null;
                this.cantidadIngresada = 1;
                this.valorIngresado = 0;
                this.subTotalIngresado = 0;

                // Limpiar Tom Select
                setTimeout(() => {
                    const selectProducto = document.getElementById('select-producto');
                    if (selectProducto && selectProducto.tomselect) {
                        selectProducto.tomselect.clear();
                    }
                }, 50);
            },

            //Funcion para Obtener Precio Segun Tipo
            getPrecio(detalle, tipoPrecio) {
                const prod = this.productos.find(p => p.id == detalle.producto_id);
                if (!prod) return 0;
                switch (this.tipoPrecio) {
                    case 'MAYORISTA':
                        return prod.valor_mayorista_producto ?? 0;
                    case 'FERRETERO':
                        return prod.valor_ferretero_producto ?? 0;
                    default:
                        return prod.valor_detal_producto ?? 0;
                }
            },
            //PRECIOS PARA EL AGREGADOR PRINCIPAL------------------------------------------
            getPrecioIndividual(productoId, tipoPrecio) {
                const prod = this.productos.find(p => p.id == productoId);
                let precioUnitario = 0;
                if (!prod) return 0;
                switch (tipoPrecio) {
                    case 'MAYORISTA':
                        precioUnitario = prod.valor_mayorista_producto ?? 0;
                        break;
                    case 'FERRETERO':
                        precioUnitario = prod.valor_ferretero_producto ?? 0;
                        break;
                    default:
                        precioUnitario = prod.valor_detal_producto ?? 0;
                }
                //console.log('Precio unitario obtenido para productoId', productoId, 'tipoPrecio', tipoPrecio, ':', precioUnitario);
                return precioUnitario;
            },
            //----------------------------------------------------------------------------

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
                let precio = this.getPrecio(detalle, this.tipoPrecio);
                return Math.round(precio * detalle.cantidad * 100) / 100;
            },

            actualizarTodosLosDetalles(tipoPrecio) {
                if (tipoPrecio) {
                    this.pedido.tipo_precio = tipoPrecio;
                }
                if (Array.isArray(this.pedido.detalles)) {
                    this.pedido.detalles.forEach((detalle, index) => {
                        // Actualizar precios según el tipo de precio
                        detalle.precio_unitario = this.getPrecio(detalle, this.tipoPrecio);
                        detalle.precio_con_iva = this.getPrecio(detalle, this.tipoPrecio);
                        detalle.subtotal = this.getSubtotal(detalle);
                    });
                }
            },
            //Funcion para Obtener Total General
            getTotal(pedido) {
                return pedido.detalles.reduce((acc, detalle) => acc + (detalle.subtotal || 0), 0);
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

            //funcion para calcular la fecha de vencimiento
            calcularFechaVencimiento(fecha, plazo) {
                if (!this.pedido.fecha || !this.pedido.plazo) return '';
                const fechaBase = new Date(this.pedido.fecha);
                fechaBase.setDate(fechaBase.getDate() + parseInt(this.pedido.plazo));
                const year = fechaBase.getFullYear();
                const month = String(fechaBase.getMonth() + 1).padStart(2, '0');
                const day = String(fechaBase.getDate()).padStart(2, '0');
                const hours = String(fechaBase.getHours()).padStart(2, '0');
                const minutes = String(fechaBase.getMinutes()).padStart(2, '0');
                return `${year}-${month}-${day}T${hours}:${minutes}`;
            },

            enviar() {
                const errores = this.validarDetalles();
                if (errores.length > 0) {
                    alert(errores.join('\n'));
                    return;
                }
                this.isLoading = true;
                // Recalcular y sincronizar todos los detalles antes de enviar
                if (Array.isArray(this.pedido.detalles)) {
                    this.pedido.detalles.forEach((detalle, idx) => {
                        // Si el usuario modificó manualmente el precio_unitario, se respeta
                        // pero siempre recalculamos precio_con_iva y subtotal
                        detalle.precio_unitario = detalle.precio_unitario ?? this.getPrecio(detalle, this
                            .tipoPrecio);
                        detalle.precio_con_iva = detalle.precio_unitario ?? this.getPrecio(detalle, this
                            .tipoPrecio);
                        detalle.subtotal = this.getSubtotal(detalle);
                    });
                }
                // Actualizar subtotal y total_a_pagar del pedido
                this.pedido.cliente_id = this.pedido.cliente_id || null;
                this.pedido.subtotal = this.getTotal(this.pedido);
                this.pedido.total_a_pagar = this.getTotalFinal(this.pedido);
                this.pedido.fecha = this.formatDateForInput(this.pedido.fecha);
                this.pedido.fecha_vencimiento = this.calcularFechaVencimiento(this.pedido.fecha, this.pedido
                    .dias_plazo_vencimiento);
                console.log('JSON generado para enviar:', JSON.stringify(this.pedido, null, 2));
                console.log('Llamando a método Livewire: editarPedido');
                console.log('Enviando petición editarPedido...');

                // Construir el payload con los detalles normalizados
                const productos = [
                    ...this.pedido.detalles.map(detalle => ({
                        producto_id: detalle.producto_id,
                        bodega_id: this.pedido.bodega_id
                    })),
                    ...this.detallesOriginales.map(detalle => ({
                        producto_id: detalle.producto_id,
                        bodega_id: this.pedido.bodega_id
                    }))
                ];

                const payload = {
                    productos,
                    bodega_id: this.pedido.bodega_id
                };
                console.log('Payload enviado a /api/recalcular-stock:', payload);

                this.$wire.editarPedido(this.pedido)
                    .then(() => {
                        this.isLoading = false;
                        // Aquí puedes agregar el fetch para recalcular el stock
                        fetch('/api/recalcular-stock', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                            },
                            body: JSON.stringify(payload) // payload debe estar definido antes
                        });
                        console.log('Petición editarPedido terminada (éxito)');
                    })
                    .catch(() => {
                        this.isLoading = false;
                        console.log('Petición editarPedido terminada (error)');
                    });
            }
        }
    }
</script>
