<div x-data="pedidoForm({

    productoIngresado: null,
    cantidadIngresada: 1,
    valorIngresado: 0,
    subTotalIngresado: 0,
    nuevoMontoAbono: 0,
    abonoSeleccionado: null,

    pedido: @js($pedidoEncontrado),
    //abonos: @js($abonos),
    clientes: @js($clientes),
    bodegas: @js($bodegas),
    users: @js($users),
    productos: @js($productos),
    detalles: @js($detalles),
    pucs: @js($pucs),
    soloLectura: @js($soloLectura),

})" x-init="init()" class="space-y-4">

    <div>
        @include('livewire.pedidos.livewire-pedidos-seccion-general')
    </div>

    <div x-show="pedido.abonos && pedido.abonos.length > 0" class="space-y-4">
        @include('livewire.pedidos.livewire-pedidos-seccion-abonos')
    </div>

    <div x-show="!soloLectura" class="sticky top-16 z-10">
        @include('livewire.pedidos.livewire-pedidos-seccion-detalle-agregar')
    </div>

    <div>
        @include('livewire.pedidos.livewire-pedidos-seccion-detalle')
    </div>

    <div
        class="sticky bottom-0 left-0 w-full z-30 bg-white/80 dark:bg-gray-900/80 backdrop-blur border-t border-gray-200 dark:border-gray-700 shadow-lg flex flex-col items-center py-4 space-y-2 transition-colors duration-300">
        @include('livewire.pedidos.livewire-pedidos-seccion-resumen')
    </div>
    @include('livewire.pedidos.livewire-pedidos-modal-pago')
    @include('livewire.pedidos.componentes.livewire-pedidos-modal-abonos')
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
        //abonos,
        users,
        productos,
        detalles,
        cantidadIngresada = 1,
        valorIngresado = 0,
        subTotalIngresado = 0,
        productoIngresado = null,
        nuevoMontoAbono = 0,
        abonoSeleccionado = null,
        pucs,
        soloLectura = false,
        mostrarModalPago = false,

    }) {
        return {
            pedido,
            //abonos,
            clientes,
            bodegas,
            users,
            productos,
            detalles,
            cantidadIngresada,
            valorIngresado,
            subTotalIngresado,
            productoIngresado,
            abonoSeleccionado,
            nuevoMontoAbono,
            detalleEditandoIndex: null,
            formatDateForInput, // disponible en Alpine
            productoSeleccionado: null,
            cantidadSeleccionada: 1,
            isLoading: false,
            ultimaObservacionPagoAutogenerada: '',
            pucs,
            soloLectura,
            mostrarModalPago: mostrarModalPago,
            init() {
                console.log('soloLectura:', this.soloLectura);
                console.log('Datos del pedido:', this.pedido);
                //console.log('Abonos del pedido:', this.pedido.abonos);
                /*console.log('Total de abonos:', this.pedido.abonos?.reduce((acc, abono) => acc + parseFloat(abono
                    .monto || 0), 0));*/

                // Recalcular el abono total basándose en los abonos reales
                /*if (this.pedido.abonos && Array.isArray(this.pedido.abonos)) {
                    this.pedido.abono = this.pedido.abonos.reduce((acc, abono) => acc + parseFloat(abono.monto || 0),
                    0);
                    console.log('Abono recalculado:', this.pedido.abono);
                }*/

                this.$watch('pedido.descuento', () => {
                    this.calcularTotales();
                });

                this.$watch('pedido.flete', () => {
                    this.calcularTotales();
                });

                // Asegurar que flete y descuento sean números
                this.pedido.flete = parseFloat(this.pedido.flete) || 0;
                this.pedido.descuento = parseFloat(this.pedido.descuento) || 0;
                this.pedido.abono_puc_id = this.pedido.abono_puc_id ?? this.pedido.id_puc ?? null;
                this.pedido.con_cuanto_paga = Number(this.pedido.con_cuanto_paga) || 0;
                this.pedido.descripcion_abono = this.pedido.descripcion_abono ?? '';
                this.pedido.observacion_pago = this.pedido.observacion_pago ?? '';
                this.pedido.detalles = Array.isArray(this.pedido.detalles) ? this.pedido.detalles : [];
                this.pedido.abonos = this.normalizarAbonos(this.pedido.abonos);
                this.sincronizarObservacionPagoConAbonos();

                // Recalcular saldo pendiente con el abono correcto
                this.pedido.abono = this.getTotalAbonos();
                this.pedido.saldo_pendiente = this.getTotalFinal(this.pedido) - this.pedido.abono;
                this.pedido.estado_pago = this.pedido.saldo_pendiente <= 0 ? 'SALDADO' : 'EN_CARTERA';

                // Guardar los detalles originales para recalculo de stock
                this.detallesOriginales = detalles.map(detalle => ({
                    producto_id: String(detalle.producto_id),
                    cantidad: detalle.cantidad,
                    precio_unitario: detalle.precio_unitario,
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
                        subtotal: detalle.cantidad * detalle.precio_unitario
                    }));
                });

                this.calcularTotales();
            },
            normalizarAbonos(abonos) {
                if (!Array.isArray(abonos)) {
                    return [];
                }

                return abonos.map(abono => ({
                    ...abono,
                    puc_id: abono.puc_id ?? abono.forma_pago_id ?? abono.forma_pago?.id ?? null,
                    puc_nombre: abono.puc_nombre ?? abono.forma_pago?.concatenar_subcuenta_concepto ?? null,
                    monto: parseFloat(abono.monto) || 0,
                    fecha: abono.fecha ? this.formatDateForInput(abono.fecha).slice(0, 10) : '',
                    descripcion: abono.descripcion || '',
                }));
            },
            getTotalAbonos() {
                return (this.pedido.abonos || []).reduce((acc, abono) => acc + (parseFloat(abono.monto) || 0), 0);
            },
            getPucIdFromAbono(abono) {
                return abono?.puc_id ?? abono?.forma_pago_id ?? abono?.forma_pago?.id ?? null;
            },
            getNombrePuc(pucId) {
                const listaPucs = Array.isArray(this.pucs) ? this.pucs : [];
                const puc = listaPucs.find(item => String(item.id) === String(pucId));
                return puc ? puc.concatenar_subcuenta_concepto : 'Medio de pago';
            },
            generarObservacionPagoDesdeAbonos() {
                return (this.pedido.abonos || [])
                    .map(abono => (abono.descripcion || '').trim())
                    .filter(Boolean)
                    .join('\n');
            },
            sincronizarObservacionPagoConAbonos() {
                const observacionGenerada = this.generarObservacionPagoDesdeAbonos();
                const observacionActual = (this.pedido.observacion_pago || '').trim();
                const ultimaObservacion = (this.ultimaObservacionPagoAutogenerada || '').trim();

                if (!observacionActual || observacionActual === ultimaObservacion) {
                    this.pedido.observacion_pago = observacionGenerada;
                }

                this.ultimaObservacionPagoAutogenerada = observacionGenerada;
            },
            agregarAbono() {
                const pucId = this.pedido.abono_puc_id;
                const monto = Number(this.pedido.con_cuanto_paga) || 0;
                const listaPucs = Array.isArray(this.pucs) ? this.pucs : [];
                const pucSeleccionado = listaPucs.find(item => String(item.id) === String(pucId));

                if (!pucId || monto <= 0) {
                    alert('Debe seleccionar un medio de pago e ingresar un monto válido para el abono.');
                    return false;
                }

                this.pedido.abonos.push({
                    puc_id: Number(pucId),
                    puc_nombre: pucSeleccionado?.concatenar_subcuenta_concepto || null,
                    monto,
                    fecha: new Date().toISOString().slice(0, 10),
                    descripcion: this.pedido.descripcion_abono || '',
                });

                this.sincronizarObservacionPagoConAbonos();
                this.pedido.abono_puc_id = null;
                this.pedido.con_cuanto_paga = 0;
                this.pedido.descripcion_abono = '';
                this.calcularTotales();

                return true;
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
                    subtotal: subTotalCalculado
                });

                // Actualizar subtotal y total_a_pagar del pedido
                this.pedido.subtotal = this.getTotal(this.pedido);
                this.pedido.total_a_pagar = this.getTotalFinal(this.pedido);
                this.pedido.saldo_pendiente = this.getTotalFinal(this.pedido) - (this.pedido.abono || 0);
                this.pedido.estado_pago = this.pedido.saldo_pendiente <= 0 ? 'SALDADO' : 'EN_CARTERA';

                // Limpiar campos de ingreso
                this.productoIngresado = null;
                this.cantidadIngresada = 1;
                this.valorIngresado = 0;
                this.subTotalIngresado = 0;

                // Limpiar Tom Select
                setTimeout(() => {
                    const selectProducto = document.getElementById('select-producto-searchable');
                    if (selectProducto) {
                        selectProducto.value = '';
                        selectProducto.dispatchEvent(new Event('change', {
                            bubbles: true
                        }));
                    }
                }, 50);

                console.log('Estado del objeto pedido al agregar detalle:', this.pedido);
            },

            // Funcion para Remover Algun Detalle
            removeDetalle(index) {
                this.pedido.detalles.splice(index, 1);
                // Actualizar subtotal y total_a_pagar del pedido
                this.pedido.subtotal = this.getTotal(this.pedido);
                this.pedido.total_a_pagar = this.getTotalFinal(this.pedido);
                this.pedido.saldo_pendiente = this.getTotalFinal(this.pedido) - (this.pedido.abono || 0);
                this.pedido.estado_pago = this.pedido.saldo_pendiente <= 0 ? 'SALDADO' : 'EN_CARTERA';

                console.log('Estado del objeto pedido al remover detalle:', this.pedido);
            },
            //Funcion para Remover Algun Abono
            removeAbono(index) {
                this.pedido.abonos.splice(index, 1);
                this.sincronizarObservacionPagoConAbonos();
                this.calcularTotales();
                console.log('Estado del objeto pedido al remover abono:', this.pedido);
            },
            // Funcion para Seleccionar un Abono
            selectAbono(abono) {
                abono.puc_id = this.getPucIdFromAbono(abono);
                abono.puc_nombre = abono.puc_nombre || this.getNombrePuc(abono.puc_id);
                this.abonoSeleccionado = abono;
                console.log('Abono seleccionado:', this.abonoSeleccionado);
            },
            // Funcion para Guardar Cambios del Abono
            guardarAbono(abono) {
                try {
                    abono.puc_id = this.getPucIdFromAbono(abono);
                    abono.puc_nombre = this.getNombrePuc(abono.puc_id);
                    this.sincronizarObservacionPagoConAbonos();
                    this.calcularTotales();
                    this.pedido.estado_pago = this.pedido.saldo_pendiente <= 0 ? 'SALDADO' : 'EN_CARTERA';

                    // Cerrar modal
                    this.abonoSeleccionado = null;

                    console.log('Abono actualizado localmente. Se sincronizará al guardar el pedido.', abono);
                } catch (error) {
                    console.error('Error al guardar abono:', error);
                }
            },
            // Funcion para Traer Detalle a los campos de entrada para editar
            traerDetalle(index) {
                const detalle = this.pedido.detalles[index];
                if (detalle) {
                    this.productoIngresado = detalle.producto_id;
                    this.cantidadIngresada = detalle.cantidad;
                    this.valorIngresado = detalle.precio_unitario;
                    this.detalleEditandoIndex = index;
                    // Actualizar select manualmente
                    this.$nextTick(() => {
                        const selectProducto = document.getElementById('select-producto-searchable');
                        if (selectProducto) {
                            selectProducto.value = detalle.producto_id;
                            selectProducto.dispatchEvent(new Event('change', {
                                bubbles: true
                            }));
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

                // Actualizar subtotal y total_a_pagar del pedido
                this.pedido.subtotal = this.getTotal(this.pedido);
                this.pedido.total_a_pagar = this.getTotalFinal(this.pedido);
                this.pedido.saldo_pendiente = this.getTotalFinal(this.pedido) - (this.pedido.abono || 0);
                this.pedido.estado_pago = this.pedido.saldo_pendiente <= 0 ? 'SALDADO' : 'EN_CARTERA';

                // Limpiar campos de ingreso
                this.productoIngresado = null;
                this.cantidadIngresada = 1;
                this.valorIngresado = 0;
                this.subTotalIngresado = 0;

                // Limpiar select del producto
                setTimeout(() => {
                    const selectProducto = document.getElementById('select-producto-searchable');
                    if (selectProducto) {
                        selectProducto.value = '';
                        selectProducto.dispatchEvent(new Event('change', {
                            bubbles: true
                        }));
                    }
                }, 50);
                console.log('Estado del objeto pedido al actualizar detalle:', this.pedido);
            },
            //Funcion para Actualizar Valores del Abono
            /*actualizarValoresAbono(index, formaPagoId, monto, descripcion, fecha, imagen) {
                const abono = this.pedido.abonos[index];
                if (!formaPagoId) {
                    alert('Debe seleccionar una forma de pago');
                    return;
                }
                if (!monto || monto <= 0) {
                    alert('El monto debe ser mayor a 0');
                    return;
                }
                abono.forma_pago_id = formaPagoId;
                abono.monto = parseFloat(monto) || 0;
                abono.descripcion = descripcion || '';
                abono.fecha = fecha || new Date().toISOString();
                abono.imagen = imagen || null;

                // Recalcular el monto total de abonos
                const montoTotalAbonos = this.pedido.abonos.reduce((acc, abono) => acc + parseFloat(abono.monto || 0),
                    0);
                this.pedido.abono = montoTotalAbonos;

                // Recalcular saldo pendiente y estado de pago
                this.pedido.saldo_pendiente = this.pedido.total_a_pagar - this.pedido.abono;
                this.pedido.estado_pago = this.pedido.saldo_pendiente <= 0 ? 'SALDADO' : 'EN_CARTERA';

                console.log('Estado del objeto pedido al actualizar abono:', this.pedido);
            },*/

            //Funcion para Obtener Precio Segun Tipo
            getPrecio(detalle, tipoPrecio) {
                const prod = this.productos.find(p => p.id == detalle.producto_id);
                if (!prod) return 0;
                switch (this.tipoPrecio) {
                    case 'MAYORISTA':
                        return prod.valor_mayorista ?? 0;
                    case 'DETAL':
                        return prod.valor_detal ?? 0;
                    default:
                        return prod.valor_detal ?? 0;
                }
            },
            //PRECIOS PARA EL AGREGADOR PRINCIPAL------------------------------------------
            getPrecioIndividual(productoId, tipoPrecio) {
                const prod = this.productos.find(p => p.id == productoId);
                let precioUnitario = 0;
                if (!prod) return 0;
                switch (tipoPrecio) {
                    case 'MAYORISTA':
                        precioUnitario = prod.valor_mayorista ?? 0;
                        break;
                    case 'DETAL':
                        precioUnitario = prod.valor_detal ?? 0;
                        break;
                    default:
                        precioUnitario = prod.valor_detal ?? 0;
                }
                //console.log('Precio unitario obtenido para productoId', productoId, 'tipoPrecio', tipoPrecio, ':', precioUnitario);
                return precioUnitario;
            },
            //----------------------------------------------------------------------------


            //Funcion para Obtener Subtotal
            getSubtotal(detalle) {
                // Usar el precio_unitario que ya está en el detalle, no buscar uno nuevo
                const precio = detalle.precio_unitario ?? 0;
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
                        detalle.subtotal = this.getSubtotal(detalle);
                    });
                }
            },
            //Funcion para Obtener Total General
            getTotal(pedido) {
                const detalles = Array.isArray(pedido?.detalles) ? pedido.detalles : [];
                return detalles.reduce((acc, detalle) => acc + (detalle.subtotal || 0), 0);
            },

            getTotalFinal(pedido) {
                if (!pedido || !Array.isArray(pedido.detalles)) return 0;
                // tu lógica aquí, por ejemplo:
                return this.getTotal(pedido) + parseFloat(pedido.flete || 0) - parseFloat(pedido.descuento || 0);
            },

            getTotalAPagar() {
                return getTotalAPagar(this.pedido);
            },
            getSaldoPendiente() {
                return getSaldoPendiente(this.pedido);
            },

            getCambio(totalAPagar, totalAbonado) {
                const cambio = Number(totalAbonado) - Number(totalAPagar);
                return cambio >= 0 ? cambio : 0;
            },

            calcularTotales() {
                this.pedido.subtotal = this.getTotal(this.pedido);
                this.pedido.total_a_pagar = this.getTotalAPagar();
                this.pedido.abono = this.getTotalAbonos();
                this.pedido.saldo_pendiente = this.getSaldoPendiente();
                this.pedido.cambio = this.getCambio(this.pedido.total_a_pagar, this.pedido.abono);
                //this.pedido.estado_pago = this.pedido.saldo_pendiente <= 0 ? 'SALDADO' : 'EN_CARTERA';
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
                const tieneBorradorAbono = this.pedido.abono_puc_id || Number(this.pedido.con_cuanto_paga) > 0;
                if (tieneBorradorAbono && !this.agregarAbono()) {
                    return;
                }

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

                        detalle.precio_unitario = detalle.precio_unitario ?? this.getPrecio(detalle, this
                            .tipoPrecio);
                        detalle.subtotal = this.getSubtotal(detalle);
                    });
                }
                // Actualizar subtotal y total_a_pagar del pedido
                this.pedido.cliente_id = this.pedido.cliente_id || null;
                this.pedido.subtotal = this.getTotal(this.pedido);
                this.pedido.total_a_pagar = this.getTotalFinal(this.pedido);
                this.pedido.descuento = parseFloat(this.pedido.descuento) || 0;
                this.pedido.flete = parseFloat(this.pedido.flete) || 0;
                this.pedido.abono = this.getTotalAbonos();
                this.pedido.id_puc = this.pedido.abonos[0]?.puc_id ?? null;
                this.pedido.saldo_pendiente = this.getTotalFinal(this.pedido) - this.pedido.abono;
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
