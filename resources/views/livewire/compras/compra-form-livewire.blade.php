<div x-data="compraForm({

    productoIngresado: null,
    cantidadIngresada: 1,
    valorIngresado: 0,
    subTotalIngresado: 0,
    descripcionIngresada: '',

    fechaIngresada: null,
    plazoIngresado: null,
    fechaVencimientoCalculada: null,

    compra: @js($compraEncontrada),
    proveedores: @js($proveedores),
    bodegas: @js($bodegas),
    productos: @js($productos),
    pucs: @js($pucs),
    detalles_compra: @js($detalles_compra)
})" x-init="init()" class="space-y-4">

    <div
        class="flex items-center justify-center gap-4 bg-white dark:bg-gray-900 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 p-6 mb-6">
        <label class="block text-sm font-semibold text-gray-700 mb-0 text-center whitespace-nowrap">Estado Venta</label>
        <div class="flex gap-4">
            <label class="inline-flex items-center cursor-pointer">
                <input type="radio" x-model="compra.item_compra" value="PRODUCTO"
                    @input="compra.item_compra = $event.target.value"
                    class="form-radio text-blue-600 focus:ring-blue-400" />
                <span class="ml-2">PRODUCTO</span>
            </label>
            <label class="inline-flex items-center cursor-pointer">
                <input type="radio" x-model="compra.item_compra" value="GASTO"
                    @input="compra.item_compra = $event.target.value"
                    class="form-radio text-blue-600 focus:ring-blue-400" />
                <span class="ml-2">GASTO</span>
            </label>
        </div>
    </div>

    <div x-show="compra.item_compra === 'PRODUCTO'"
        class="bg-white dark:bg-gray-900 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 p-6 mb-6">
        @include('livewire.compras.modulos.livewire-compras-seccion-general')
    </div>

    <div x-show="compra.item_compra === 'GASTO'"
        class="bg-white dark:bg-gray-900 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 p-6 mb-6">
        @include('livewire.compras.modulos.livewire-compras-seccion-general-cotizacion')
    </div>

    <div
        class="sticky top-16 z-10 bg-white dark:bg-gray-900 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 p-6 mb-6">
        @include('livewire.compras.modulos.livewire-compras-seccion-detalle-agregar')
    </div>

     <div class="bg-white dark:bg-gray-900 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 p-6 mb-6">
        @include('livewire.compras.modulos.livewire-compras-seccion-detalle')
    </div>

    <div
        class="sticky bottom-0 left-0 w-full z-30 bg-white/80 dark:bg-gray-900/80 backdrop-blur border-t border-gray-200 dark:border-gray-700 shadow-lg flex flex-col items-center py-4 space-y-2 transition-colors duration-300">
        @include('livewire.compras.modulos.livewire-compras-seccion-resumen')
    </div>
    @include('livewire.compras.componentes.compras-modal-venta')
</div>


{{--<script src="{{ asset('js/compras.js') }}"></script>
<script src="{{ asset('js/comprasCalculos.js') }}"></script>--}}

<script>
    function formatDateForInput(fecha) {
        if (!fecha) return '';
        // Si la fecha tiene formato ISO con zona horaria, recorta a yyyy-MM-ddTHH:mm
        // Ejemplo: "2025-01-05T14:00:00.000000Z" => "2025-01-05T14:00"
        if (typeof fecha === 'string' && fecha.length >= 16) {
            return fecha.slice(0, 16);
        }
        if (typeof fecha === 'string' && fecha.length === 10) {
            return `${fecha}T00:00`;
        }
        return fecha;
    }

    function compraForm({
        compra,
        proveedores,
        bodegas,
        productos,
        pucs,
        detalles_compra,
        cantidadIngresada = 1,
        valorIngresado = 0,
        subTotalIngresado = 0,
        productoIngresado = null,
        descripcionIngresada = '',

        fechaIngresada = null,
        plazoIngresado = null,
        fechaVencimientoCalculada = null,
    }) {
        return {
            compra,
            proveedores,
            bodegas,
            productos,
            pucs,
            detalles_compra,
            cantidadIngresada,
            valorIngresado,
            subTotalIngresado,
            productoIngresado,
            descripcionIngresada,
            detalleEditandoIndex: null,
            formatDateForInput, // disponible en Alpine
            productoSeleccionado: null,
            cantidadSeleccionada: 1,
            isLoading: false,
            init() {
                //muestra en consola los datos de la compra para verificar que se están cargando correctamente
                console.log('Datos de la compra:', this.compra);

                // Normalizar descuento a numero para calculos iniciales
                this.compra.descuento = parseFloat(this.compra.descuento) || 0;

                // Guardar los detalles originales para recalculo de stock
                this.detallesOriginales = detalles_compra.map(detalle => ({
                    producto_id: String(detalle.item_id || detalle.producto_id),
                    cantidad: detalle.cantidad,
                    precio_unitario: detalle.precio_unitario,
                    aplicar_iva: detalle.aplicar_iva,
                    precio_con_iva: detalle.precio_con_iva,
                    subtotal: (detalle.cantidad || 0) * (detalle.precio_unitario || 0),
                    tipo_item: detalle.tipo_item,
                }));

                // Forzar actualización de selects después de inicializar datos
                this.$nextTick(() => {
                    // Proveedor
                    const selectProveedor = document.querySelector('select[x-model="compra.proveedor_id"]');
                    if (selectProveedor && this.compra.proveedor_id !== undefined && this.compra
                        .proveedor_id !==
                        null) {
                        selectProveedor.value = this.compra.proveedor_id;
                    }
                    // Bodega
                    const selectBodega = document.querySelector('select[x-model="compra.bodega_id"]');
                    if (selectBodega && this.compra.bodega_id !== undefined && this.compra.bodega_id !== null) {
                        selectBodega.value = this.compra.bodega_id;
                    }
                    // Alistador
                    const selectAlistador = document.querySelector('select[x-model="compra.alistador_id"]');
                    if (selectAlistador && this.compra.alistador_id !== undefined && this.compra
                        .alistador_id !== null) {
                        selectAlistador.value = this.compra.alistador_id;
                    }
                    // Usuario
                    const selectUsuario = document.querySelector('select[x-model="compra.user_id"]');
                    if (selectUsuario && this.compra.user_id !== undefined && this.compra.user_id !== null) {
                        selectUsuario.value = this.compra.user_id;
                    }
                    // Obtener los Detalles (normalizar item_id a producto_id)
                    this.compra.detalles_compra = detalles_compra.map(detalle => ({
                        producto_id: String(detalle.item_id || detalle.producto_id),
                        cantidad: detalle.cantidad,
                        precio_unitario: detalle.precio_unitario,
                        aplicar_iva: detalle.aplicar_iva,
                        precio_con_iva: detalle.precio_con_iva,
                        descripcion: detalle.descripcion,
                        subtotal: (detalle.cantidad || 0) * (detalle.precio_unitario || 0),
                        tipo_item: detalle.tipo_item,
                    }));

                    // Filtrar productos según categoría al cargar
                    this.actualizarTomSelect();
                });

                // Watch para actualizar TomSelect cuando cambie la categoría
                this.$watch('compra.categoria_compra', (newCat) => {
                    this.actualizarTomSelect();
                });
            },

            // Computed: productos filtrados según categoría
            get productosFiltrados() {
                if (!this.compra.categoria_compra) return this.productos;
                return this.productos.filter(p => p.categoria_producto === this.compra.categoria_compra);
            },

            // Actualizar opciones de TomSelect
            actualizarTomSelect() {
                this.$nextTick(() => {
                    const select = document.getElementById('select-producto');
                    if (select && select.tomselect) {
                        select.tomselect.clear();
                        select.tomselect.clearOptions();
                        this.productosFiltrados.forEach(p => {
                            select.tomselect.addOption({
                                value: p.id,
                                text: p.concatenar_codigo_nombre
                            });
                        });
                    }
                });
            },

            //Funcion para calcular fecha de vencimiento
            calcularFechaVencimiento(fecha, plazo) {
                if (!fecha || !plazo) return '';
                const fechaBase = new Date(fecha);
                fechaBase.setDate(fechaBase.getDate() + parseInt(plazo));
                const year = fechaBase.getFullYear();
                const month = String(fechaBase.getMonth() + 1).padStart(2, '0');
                const day = String(fechaBase.getDate()).padStart(2, '0');
                const hours = String(fechaBase.getHours()).padStart(2, '0');
                const minutes = String(fechaBase.getMinutes()).padStart(2, '0');
                return `${year}-${month}-${day}T${hours}:${minutes}`;
            },

            // Obtener el tipo de precio seleccionado
            get tipoPrecio() {
                return this.compra.tipo_precio;
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
                // en caso de que el producto ya exista en la compra, alertar y no agregar
                const detalleExistente = this.compra.detalles_compra.find(detalle => detalle.producto_id === String(
                    productoId));
                if (detalleExistente) {
                    alert('El producto ya está agregado a la compra');
                    return;
                }
                // Calcular subtotal
                const cantidadNum = parseFloat(cantidad) || 0;
                const valorUnitarioNum = parseFloat(valorUnitario) || 0;
                const subTotalCalculado = Math.round(cantidadNum * valorUnitarioNum * 100) / 100;

                this.compra.detalles_compra.push({
                    producto_id: productoId,
                    descripcion_item: this.descripcionIngresada || '',
                    cantidad: cantidadNum,
                    precio_unitario: valorUnitarioNum,
                    subtotal: subTotalCalculado,
                    tipo_item: this.compra.item_compra,
                });
                // Limpiar campos de ingreso
                this.productoIngresado = null;
                this.cantidadIngresada = 1;
                this.valorIngresado = 0;
                this.subTotalIngresado = 0;
                this.descripcionIngresada = '';

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
                this.compra.detalles_compra.splice(index, 1);
            },
            // Funcion para Traer Detalle a los campos de entrada para editar
            traerDetalle(index) {
                const detalle = this.compra.detalles_compra[index];
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
                const detalle = this.compra.detalles_compra[index];
                if (!productoId) {
                    alert('Debe seleccionar un producto');
                    return;
                }
                if (!cantidad || cantidad < 1) {
                    alert('La cantidad debe ser mayor a 0');
                    return;
                }
                //En caso de que el producto ya exista en la compra (y no sea el mismo detalle que se está editando), alertar y no actualizar
                const detalleExistente = this.compra.detalles_compra.find((d, i) => d.producto_id === String(productoId) &&
                    i !== index);
                if (detalleExistente) {
                    alert('El producto ya está agregado a la compra');
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
            //Funcion para Obtener Subtotal
            getSubtotal(detalle) {
                const precio = detalle.precio_unitario || 0;
                const cantidad = detalle.cantidad || 0;
                return Math.round(precio * cantidad * 100) / 100;
            },
            actualizarTodosLosDetalles(tipoPrecio) {
                if (tipoPrecio) {
                    this.compra.tipo_precio = tipoPrecio;
                }
                if (Array.isArray(this.compra.detalles_compra)) {
                    this.compra.detalles_compra.forEach((detalle, index) => {
                        // Actualizar precios según el tipo de precio
                        detalle.precio_unitario = detalle.precio_unitario;
                        detalle.precio_con_iva = detalle.precio_con_iva;
                        detalle.subtotal = this.getSubtotal(detalle);
                        detalle.tipo_item = this.compra.item_compra;
                    });
                }
            },
            //Funcion para Obtener Total General
            getTotal(compra) {
                return compra.detalles_compra.reduce((acc, detalle) => acc + (detalle.subtotal || 0), 0);
            },

            getTotalFinal(compra) {
                if (!compra || !Array.isArray(compra.detalles_compra)) return 0;
                // tu lógica aquí, por ejemplo:
                return this.getTotal(compra) + (compra.flete || 0) - (compra.descuento || 0);
            },

            //Funcion para Validar Detalles
            validarDetalles() {
                let errores = [];
                this.compra.detalles_compra.forEach((detalle, idx) => {
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
                if (!fecha || !plazo) return '';
                const fechaBase = new Date(fecha);
                fechaBase.setDate(fechaBase.getDate() + parseInt(plazo));
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
                if (Array.isArray(this.compra.detalles_compra)) {
                    this.compra.detalles_compra.forEach((detalle, idx) => {
                        // Si el usuario modificó manualmente el precio_unitario, se respeta
                        // pero siempre recalculamos precio_con_iva y subtotal
                        detalle.precio_unitario = detalle.precio_unitario ?? this.getPrecio(detalle, this
                            .tipoPrecio);
                        detalle.precio_con_iva = detalle.precio_unitario;
                        detalle.subtotal = this.getSubtotal(detalle);
                    });
                }
                // Actualizar subtotal y total_a_pagar de la compra
                this.compra.proveedor_id = this.compra.proveedor_id || null;
                this.compra.subtotal = this.getTotal(this.compra);
                this.compra.total_a_pagar = this.getTotalFinal(this.compra);
                this.compra.fecha = this.formatDateForInput(this.compra.fecha);
                this.compra.fecha_vencimiento = this.calcularFechaVencimiento(this.compra.fecha, this.compra
                    .dias_plazo_vencimiento);
                console.log('JSON generado para enviar:', JSON.stringify(this.compra, null, 2));
                console.log('Llamando a método Livewire: editarCompra');
                console.log('Enviando petición editarCompra...');

                // Construir el payload con los detalles normalizados
                const productos = [
                    ...this.compra.detalles_compra.map(detalle => ({
                        producto_id: detalle.producto_id,
                        bodega_id: this.compra.bodega_id
                    })),
                    ...this.detallesOriginales.map(detalle => ({
                        producto_id: detalle.producto_id,
                        bodega_id: this.compra.bodega_id
                    }))
                ];

                const payload = {
                    productos,
                    bodega_id: this.compra.bodega_id
                };
                console.log('Payload enviado a /api/recalcular-stock:', payload);

                this.$wire.editarCompra(this.compra)
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
                        console.log('Petición editarCompra terminada (éxito)');
                    })
                    .catch(() => {
                        this.isLoading = false;
                        console.log('Petición editarCompra terminada (error)');
                    });
            }
        }
    }
</script>
