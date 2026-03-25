<div x-data="compraForm({

    categoriaSeleccionada: null,
    productoIngresado: null,
    cantidadIngresada: 1,
    valorIngresado: 0,
    subTotalIngresado: 0,
    fechaIngresada: null,

    compra: @js($compraEncontrada),
    proveedores: @js($proveedores),
    bodegas: @js($bodegas),
    productos: @js($productos),
    detalles_compra: @js($detalles_compra),
    esEdicion: @js($esEdicion),
    categorias: @js($categorias),

})" x-init="init()" class="space-y-4">
    <div>
        @include('livewire.compras.modulos.livewire-compras-seccion-general-producto')
    </div>

    <div class="sticky top-16 ">
        @include('livewire.compras.modulos.livewire-compras-seccion-detalle-agregar')
    </div>

    <div>
        @include('livewire.compras.modulos.livewire-compras-seccion-detalle')
    </div>

    <div
        class="sticky bottom-0 left-0 w-full z-30 bg-white/80 dark:bg-gray-900/80 backdrop-blur border-t border-gray-200 dark:border-gray-700 shadow-lg flex flex-col items-center py-4 space-y-2 transition-colors duration-300">
        @include('livewire.compras.modulos.livewire-compras-seccion-resumen')
    </div>
    @include('livewire.compras.componentes.compras-modal-venta')
</div>


{{-- <script src="{{ asset('js/compras.js') }}"></script>
<script src="{{ asset('js/comprasCalculos.js') }}"></script> --}}

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
        detalles_compra,
        esEdicion,
        cantidadIngresada = 1,
        valorIngresado = 0,
        subTotalIngresado = 0,
        productoIngresado = null,
        categoriaSeleccionada = null,
        categorias = [],

        fechaIngresada = null,
    }) {
        return {
            compra,
            proveedores,
            bodegas,
            productos,
            detalles_compra,
            esEdicion,
            cantidadIngresada,
            valorIngresado,
            subTotalIngresado,
            productoIngresado,
            detalleEditandoIndex: null,
            detalleEditandoIndices: [],
            formatDateForInput, // disponible en Alpine
            productoSeleccionado: null,
            cantidadSeleccionada: 1,
            isLoading: false,
            cantidadesPorBodega: {},
            categorias,
            categoriaSeleccionada,
            init() {
                //muestra en consola los datos de la compra para verificar que se están cargando correctamente
                console.log('Datos de la compra:', this.compra);
                console.log('esEdicion:', this.esEdicion);

                // Normalizar descuento a numero para calculos iniciales
                this.compra.descuento = parseFloat(this.compra.descuento) || 0;

                // Guardar los detalles originales para recalculo de stock
                this.detallesOriginales = detalles_compra.map(detalle => ({
                    producto_id: String(detalle.producto_id),
                    bodega_id: detalle.bodega_id,
                    cantidad: detalle.cantidad,
                    precio_unitario: detalle.precio_unitario,
                    subtotal: (detalle.cantidad || 0) * (detalle.precio_unitario || 0),
                }));

                // Forzar actualización de selects después de inicializar datos
                this.$nextTick(() => {
                    // Proveedor
                    const selectProveedores = document.querySelectorAll(
                    'select[x-model="compra.proveedor_id"]');
                    if (selectProveedores.length && this.compra.proveedor_id !== undefined && this.compra
                        .proveedor_id !== null) {
                        selectProveedores.forEach(select => {
                            select.value = this.compra.proveedor_id;
                        });
                    }

                    // Obtener los Detalles
                    this.compra.detalles_compra = detalles_compra.map(detalle => ({
                        id: detalle.id, // ID del detalle para edición
                        producto_id: String(detalle.producto_id),
                        bodega_id: detalle.bodega_id,
                        cantidad: detalle.cantidad,
                        precio_unitario: detalle.precio_unitario,
                        subtotal: (detalle.cantidad || 0) * (detalle.precio_unitario || 0),
                    }));
                });
            },

            // Computed: detalles agrupados por producto
            get detallesAgrupadosPorProducto() {
                if (!this.compra.detalles_compra || !Array.isArray(this.compra.detalles_compra)) return [];

                const grupos = {};

                this.compra.detalles_compra.forEach((detalle, index) => {
                    const productoId = detalle.producto_id;

                    if (!grupos[productoId]) {
                        grupos[productoId] = {
                            producto_id: productoId,
                            precio_unitario: detalle.precio_unitario,
                            subtotal: 0,
                            cantidadesPorBodega: {},
                            indices: [] // Para poder editar/eliminar
                        };
                    }

                    // Agregar cantidad a la bodega correspondiente
                    grupos[productoId].cantidadesPorBodega[detalle.bodega_id] = detalle.cantidad;
                    grupos[productoId].subtotal += detalle.subtotal;
                    grupos[productoId].indices.push(index);
                });

                return Object.values(grupos);
            },

            // Obtener el tipo de precio seleccionado
            get tipoPrecio() {
                return this.compra.tipo_precio;
            },
            // Funciones para manejar los detalles de la compra
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
                    cantidad: cantidadNum,
                    precio_unitario: valorUnitarioNum,
                    subtotal: subTotalCalculado,
                });
                // Actualizar subtotal y total_a_pagar de la compra
                this.compra.subtotal = this.getTotal(this.compra);
                this.compra.total_a_pagar = this.getTotalFinal(this.compra);

                // Limpiar campos de ingreso
                this.productoIngresado = null;
                this.cantidadIngresada = 1;
                this.valorIngresado = 0;
                this.subTotalIngresado = 0;

                    console.log('Detalle agregado. Estado del objeto compra:', this.compra);
            },

            // Función para obtener la cantidad total de todas las bodegas
            getCantidadTotal() {
                return Object.values(this.cantidadesPorBodega).reduce((sum, cant) => sum + (parseFloat(cant) || 0), 0);
            },

            // Función para agregar detalles por bodegas
            agregarDetallePorBodegas(productoId, cantidadesPorBodega, valorUnitario) {
                if (!productoId) {
                    alert('Debe seleccionar un producto');
                    return;
                }

                const cantidadTotal = this.getCantidadTotal();
                if (cantidadTotal <= 0) {
                    alert('Debe ingresar al menos una cantidad en alguna bodega');
                    return;
                }

                // Agregar un detalle por cada bodega con cantidad > 0
                Object.entries(cantidadesPorBodega).forEach(([bodegaId, cantidad]) => {
                    const cantidadNum = parseFloat(cantidad) || 0;
                    if (cantidadNum > 0) {
                        // Verificar si ya existe el producto en esta bodega
                        const detalleExistente = this.compra.detalles_compra.find(
                            detalle => detalle.producto_id === String(productoId) && detalle.bodega_id === parseInt(bodegaId)
                        );

                        if (detalleExistente) {
                            alert(`El producto ya está agregado en la bodega ${this.bodegas.find(b => b.id == bodegaId)?.nombre_bodega || bodegaId}`);
                            return;
                        }

                        const valorUnitarioNum = parseFloat(valorUnitario) || 0;
                        const subTotalCalculado = Math.round(cantidadNum * valorUnitarioNum * 100) / 100;

                        this.compra.detalles_compra.push({
                            producto_id: productoId,
                            bodega_id: parseInt(bodegaId),
                            cantidad: cantidadNum,
                            precio_unitario: valorUnitarioNum,
                            subtotal: subTotalCalculado,
                        });
                    }
                });

                // Actualizar totales
                this.compra.subtotal = this.getTotal(this.compra);
                this.compra.total_a_pagar = this.getTotalFinal(this.compra);

                // Limpiar campos
                this.limpiarCampos();

                console.log('Detalles agregados por bodega. Estado del objeto compra:', this.compra);
            },

            // Función para limpiar campos de ingreso
            limpiarCampos() {
                this.productoIngresado = null;
                this.cantidadesPorBodega = {};
                this.valorIngresado = 0;
                this.detalleEditandoIndex = null;
                this.detalleEditandoIndices = [];
            },

            // Funcion para Remover Algun Detalle
            removeDetalle(index) {
                this.compra.detalles_compra.splice(index, 1);
                // Actualizar subtotal y total_a_pagar de la compra
                this.compra.subtotal = this.getTotal(this.compra);
                this.compra.total_a_pagar = this.getTotalFinal(this.compra);

                console.log('Detalle removido. Estado del objeto compra:', this.compra);
            },
            // Funcion para Traer Detalle a los campos de entrada para editar
            traerDetalle(index) {
                const detalle = this.compra.detalles_compra[index];
                if (detalle) {
                    this.productoIngresado = detalle.producto_id;
                    this.valorIngresado = detalle.precio_unitario;

                    // Buscar todos los detalles con el mismo producto_id
                    const detallesMismoProducto = this.compra.detalles_compra
                        .map((d, i) => ({ detalle: d, index: i }))
                        .filter(item => item.detalle.producto_id === detalle.producto_id);

                    // Cargar cantidades por bodega
                    this.cantidadesPorBodega = {};
                    detallesMismoProducto.forEach(item => {
                        if (item.detalle.bodega_id) {
                            this.cantidadesPorBodega[item.detalle.bodega_id] = item.detalle.cantidad;
                        }
                    });

                    // Guardar los índices para poder actualizarlos/eliminarlos
                    this.detalleEditandoIndices = detallesMismoProducto.map(item => item.index);
                    this.detalleEditandoIndex = index; // Mantener por compatibilidad

                    console.log('Detalle(s) cargado(s) para editar:', detallesMismoProducto);
                }
            },
            //Funcion para Actualizar Valores del Detalle
            actualizarValoresDetalle(index, productoId, cantidadesPorBodega, valorUnitario) {
                if (!productoId) {
                    alert('Debe seleccionar un producto');
                    return;
                }

                const cantidadTotal = this.getCantidadTotal();
                if (cantidadTotal <= 0) {
                    alert('Debe ingresar al menos una cantidad en alguna bodega');
                    return;
                }

                // Guardar los IDs de los detalles antes de eliminarlos
                const detallesAntiguos = {};
                if (this.detalleEditandoIndices && this.detalleEditandoIndices.length > 0) {
                    this.detalleEditandoIndices.forEach(i => {
                        const detalle = this.compra.detalles_compra[i];
                        if (detalle && detalle.bodega_id && detalle.id) {
                            detallesAntiguos[detalle.bodega_id] = detalle.id;
                        }
                    });

                    // Ordenar de mayor a menor para no desacomodar índices
                    this.detalleEditandoIndices.sort((a, b) => b - a).forEach(i => {
                        this.compra.detalles_compra.splice(i, 1);
                    });
                }

                // Agregar nuevos detalles por cada bodega con cantidad > 0
                const valorUnitarioNum = parseFloat(valorUnitario) || 0;

                Object.entries(cantidadesPorBodega).forEach(([bodegaId, cantidad]) => {
                    const cantidadNum = parseFloat(cantidad) || 0;
                    if (cantidadNum > 0) {
                        const subTotalCalculado = Math.round(cantidadNum * valorUnitarioNum * 100) / 100;
                        const bodegaIdNum = parseInt(bodegaId);

                        this.compra.detalles_compra.push({
                            id: detallesAntiguos[bodegaIdNum] || null, // Preservar ID si existía
                            producto_id: productoId,
                            bodega_id: bodegaIdNum,
                            cantidad: cantidadNum,
                            precio_unitario: valorUnitarioNum,
                            subtotal: subTotalCalculado,
                        });
                    }
                });

                //Actualizar subtotal y total_a_pagar de la compra
                this.compra.subtotal = this.getTotal(this.compra);
                this.compra.total_a_pagar = this.getTotalFinal(this.compra);

                // Limpiar campos
                this.limpiarCampos();

                console.log('Detalle(s) actualizado(s). Estado del objeto compra:', this.compra);
            },
            //Funcion para Obtener Subtotal
            getSubtotal(detalle) {
                const precioUnitario = detalle.precio_unitario || 0;
                const cantidad = detalle.cantidad || 0;
                return Math.round(precioUnitario * cantidad * 100) / 100;
            },
            actualizarTodosLosDetalles(tipoPrecio) {
                if (tipoPrecio) {
                    this.compra.tipo_precio = tipoPrecio;
                }
                if (Array.isArray(this.compra.detalles_compra)) {
                    this.compra.detalles_compra.forEach((detalle, index) => {
                        // Actualizar precios según el tipo de precio
                        detalle.precio_unitario = detalle.precio_unitario;
                        detalle.subtotal = this.getSubtotal(detalle);
                    });
                }
            },
            //Funcion para Obtener Total General
            getTotal(compra) {
                if (!compra || !Array.isArray(compra.detalles_compra)) return 0;
                return compra.detalles_compra.reduce((acc, detalle) => acc + (detalle.subtotal || 0), 0);
            },

            getTotalFinal(compra) {
                if (!compra || !Array.isArray(compra.detalles_compra)) return 0;
                // tu lógica aquí, por ejemplo:
                return this.getTotal(compra) + (compra.flete || 0) - (compra.descuento || 0);
            },

            validarFormulario() {
                if (!this.compra.categoria_compra && this.compra.item_compra === 'PRODUCTO') {
                    alert('Debe seleccionar una categoría de compra');
                    return false;
                }
                if (!this.compra.factura) {
                    alert('El campo Factura es obligatorio');
                    return false;
                }
                if (!this.compra.proveedor_id) {
                    alert('Debe seleccionar un proveedor');
                    return false;
                }
                if (!this.compra.fecha) {
                    alert('El campo Fecha es obligatorio');
                    return false;
                }
                if (!this.compra.estado) {
                    alert('Debe seleccionar un estado');
                    return false;
                }
                return true;
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

            enviar() {
                console.log('=== INICIO ENVIAR ===');
                console.log('esEdicion:', this.esEdicion);
                console.log('compra.id:', this.compra.id);
                console.log('detalles_compra con IDs:', this.compra.detalles_compra.map(d => ({
                    id: d.id,
                    producto_id: d.producto_id,
                    bodega_id: d.bodega_id,
                    cantidad: d.cantidad
                })));

                if (!this.validarFormulario()) {
                    return;
                }
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
                        // pero siempre recalculamos subtotal
                        detalle.precio_unitario = detalle.precio_unitario;
                        detalle.subtotal = this.getSubtotal(detalle);
                    });
                }
                // Actualizar subtotal y total_a_pagar de la compra
                this.compra.categoria_compra = this.compra.categoria_compra || null;
                this.compra.proveedor_id = this.compra.proveedor_id || null;
                this.compra.subtotal = this.getTotal(this.compra);
                this.compra.total_a_pagar = this.getTotalFinal(this.compra);
                this.compra.fecha = this.formatDateForInput(this.compra.fecha);
                this.compra.descuento = parseFloat(this.compra.descuento) || 0;
                this.compra.flete = parseFloat(this.compra.flete) || 0;

                const detallesPayload = (this.compra.detalles_compra || []).map(detalle => ({
                    id: detalle.id || null,
                    producto_id: detalle.producto_id,
                    bodega_id: detalle.bodega_id,
                    cantidad: detalle.cantidad,
                    precio_unitario: detalle.precio_unitario,
                    subtotal: detalle.subtotal,
                    accion: detalle.id ? 'update' : 'create'
                }));

                console.log('=== DETALLES PAYLOAD CON ACCION ===');
                console.log('detallesPayload:', JSON.stringify(detallesPayload, null, 2));

                const payloadCreacion = {
                    compra: {
                        factura: this.compra.factura,
                        proveedor_id: this.compra.proveedor_id,
                        fecha: this.compra.fecha,
                        estado: this.compra.estado,
                        observaciones: this.compra.observaciones,
                        subtotal: this.compra.subtotal,
                        descuento: this.compra.descuento,
                        total_a_pagar: this.compra.total_a_pagar,
                        detallesCompra: detallesPayload
                    }
                };

                const payloadEdicion = {
                    compra_id: this.compra.id || this.compra.compra_id || null,
                    factura: this.compra.factura,
                    proveedor_id: this.compra.proveedor_id,
                    fecha: this.compra.fecha,
                    estado: this.compra.estado,
                    observaciones: this.compra.observaciones,
                    subtotal: this.compra.subtotal,
                    descuento: this.compra.descuento,
                    total_a_pagar: this.compra.total_a_pagar,
                    detallesCompra: detallesPayload
                };

                console.log('=== PAYLOADS GENERADOS ===');
                console.log('payloadCreacion:', JSON.stringify(payloadCreacion, null, 2));
                console.log('payloadEdicion:', JSON.stringify(payloadEdicion, null, 2));
                console.log('Modo esEdicion:', this.esEdicion);
                console.log('Payload que se enviará:', this.esEdicion ? 'payloadEdicion' : 'payloadCreacion');

                console.log('JSON generado para enviar:', JSON.stringify(this.compra, null, 2));
                console.log('Llamando a método Livewire: editarCompra');
                console.log('Enviando petición editarCompra...');

                if(this.esEdicion) {

                const compraId = this.compra.id || this.compra.compra_id;
                if (!compraId) {
                    this.isLoading = false;
                    console.log('No se puede editar: compra_id no está definido');
                    return;
                }

                this.$wire.editarCompra(compraId, payloadEdicion).then(() => {
                        this.isLoading = false;
                        // Aquí puedes agregar el fetch para recalcular el stock

                            // Construir el payload con los detalles normalizados
                            const productos = [
                                ...this.compra.detalles_compra.map(detalle => ({
                                    producto_id: detalle.producto_id,
                                    bodega_id: detalle.bodega_id
                                })),
                                ...this.detallesOriginales.map(detalle => ({
                                    producto_id: detalle.producto_id,
                                    bodega_id: detalle.bodega_id
                                }))
                            ].filter(item => item.producto_id && item.bodega_id !== undefined && item.bodega_id !== null);

                            const payload = {
                                productos,

                            };
                            console.log('Payload enviado a /api/recalcular-stock:', payload);

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
                } if (!this.esEdicion) {
                    this.$wire.crearCompra(payloadCreacion).then(() => {
                        this.isLoading = false;
                        if(this.compra.item_compra === 'PRODUCTO'){
                            const payload = {
                                productos: this.compra.detalles_compra.map(detalle => ({
                                    producto_id: detalle.producto_id,
                                    bodega_id: detalle.bodega_id
                                })).filter(item => item.producto_id && item.bodega_id !== undefined && item.bodega_id !== null),

                            };
                            console.log('Payload enviado a /api/recalcular-stock:', payload);
                            fetch('/api/recalcular-stock', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                                },
                                body: JSON.stringify(payload) // payload debe estar definido antes
                            });
                            console.log('Petición /api/recalcular-stock terminada (éxito)');
                        }

                        console.log('Petición crearCompra terminada (éxito)');
                    }).catch(() => {
                        this.isLoading = false;
                        console.log('Petición crearCompra terminada (error)');
                    });
                }
            }
        }
    }
</script>
