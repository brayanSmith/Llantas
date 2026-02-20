<div x-data="abonoComprasForm({
    proveedores: @js($proveedores),
    formasPago: @js($formasPago),
    users: @js($users),
    abonos: @js($abonos),

    proveedorAbonoIngresado: null,
    usuarioAbonoIngresado: null,
    valorAbonoIngresado: null,
    fechaAbonoIngresada: null,
    formaDePagoAbonoIngresada: null,
    descripcionAbonoIngresada: null,

})" x-init="init()" class="space-y-4">
    <div class="bg-white dark:bg-gray-900 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 p-6 mb-6">
        @include('livewire.abonosCompras.livewire-abono-compras-buscador')
    </div>
    <div class="bg-white dark:bg-gray-900 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 p-6 mb-6">
        @include('livewire.abonosCompras.livewire-abono-compras-lista')
    </div>
    <div>
        @include('livewire.abonosCompras.livewire-abono-compras-boton-guardar')
    </div>
    <div>
        @include('livewire.abonosCompras.componentes.compras-modal-abonos')
    </div>

    <script>
        function abonoComprasForm({
            proveedores,
            formasPago,
            users,
            abonos,
            proveedorAbonoIngresado,
            usuarioAbonoIngresado,
            valorAbonoIngresado,
            fechaAbonoIngresada,
            formaDePagoAbonoIngresada,
            descripcionAbonoIngresada,
        }) {
            return {
                proveedores,
                formasPago,
                users,
                abonos,
                proveedorAbonoIngresado,
                usuarioAbonoIngresado,
                valorAbonoIngresado,
                fechaAbonoIngresada,
                formaDePagoAbonoIngresada,
                descripcionAbonoIngresada,
                comprasEnCartera: [],
                comprasSeleccionadas: [],
                isLoading: false,

                init() {
                    console.log('Componente de abonos inicializado');
                    // Watcher para seleccionar automáticamente al cambiar valorAbonoIngresado
                    this.$watch('valorAbonoIngresado', () => {
                        if (this.valorAbonoIngresado && parseFloat(this.valorAbonoIngresado) > 0) {
                            this.seleccionarAutomaticamente();
                        }
                    });
                },

                buscarComprasEnCartera() {
                    if (!this.proveedorAbonoIngresado) {
                        alert('Debe seleccionar un proveedor');
                        return;
                    }

                    this.isLoading = true;
                    this.comprasSeleccionadas = []; // Limpiar selección
                    console.log('Buscando compras del proveedor:', this.proveedorAbonoIngresado);

                    this.$wire.buscarComprasEnCartera(this.proveedorAbonoIngresado)
                        .then((compras) => {
                            this.comprasEnCartera = compras;
                            this.isLoading = false;
                            this.comprasSeleccionadas = []; // Limpiar selección después de cargar
                            console.log('Compras encontradas:', compras);
                        })
                        .catch((error) => {
                            this.isLoading = false;
                            console.error('Error al buscar compras:', error);
                            alert('Error al buscar compras');
                        });
                },

                toggleCompra(compra) {
                    const index = this.comprasSeleccionadas.findIndex(c => c.id === compra.id);
                    if (index > -1) {
                        this.comprasSeleccionadas.splice(index, 1);
                    } else {
                        this.comprasSeleccionadas.push(compra);
                    }
                    console.log('Compras seleccionadas:', this.comprasSeleccionadas);
                },

                // Computed: Total a pagar de todas las compras seleccionadas
                get totalAPagarSeleccionado() {
                    return this.comprasSeleccionadas.reduce((acc, compra) => acc + parseFloat(compra.saldo_pendiente ||
                        0), 0);
                },

                // Computed: Cantidad de compras seleccionadas
                get cantidadComprasSeleccionadas() {
                    return this.comprasSeleccionadas.length;
                },

                // Computed: Restante después de ingresar el valor del abono
                get restante() {
                    const valorAbono = parseFloat(this.valorAbonoIngresado) || 0;
                    return valorAbono - this.totalAPagarSeleccionado;
                },

                seleccionarAutomaticamente() {
                    const valorAbono = parseFloat(this.valorAbonoIngresado) || 0;
                    if (valorAbono <= 0 || this.comprasEnCartera.length === 0) return;

                    // Ordenar compras por saldo_pendiente de menor a mayor
                    const comprasOrdenadas = [...this.comprasEnCartera].sort((a, b) =>
                        parseFloat(a.saldo_pendiente || 0) - parseFloat(b.saldo_pendiente || 0)
                    );

                    // Limpiar selección anterior
                    this.comprasSeleccionadas = [];
                    let acumulado = 0;

                    // Seleccionar compras hasta alcanzar o superar el valor del abono
                    for (let compra of comprasOrdenadas) {
                        const montoCompra = parseFloat(compra.saldo_pendiente || 0);
                        if (acumulado + montoCompra <= valorAbono) {
                            this.comprasSeleccionadas.push(compra);
                            acumulado += montoCompra;
                        } else if (acumulado < valorAbono) {
                            // Si falta poco, incluir esta compra también
                            if (valorAbono - acumulado >= montoCompra * 0.5) {
                                this.comprasSeleccionadas.push(compra);
                                acumulado += montoCompra;
                            }
                            break;
                        } else {
                            break;
                        }
                    }

                    console.log('Compras seleccionadas automáticamente:', this.comprasSeleccionadas, 'Total: ', acumulado);
                },

                generarAbonos() {
                    if (this.comprasSeleccionadas.length === 0) {
                        alert('Debe seleccionar al menos una compra');
                        return;
                    }
                    if (!this.valorAbonoIngresado || !this.fechaAbonoIngresada ||
                        !this.formaDePagoAbonoIngresada || !this.usuarioAbonoIngresado) {
                        alert('Debe completar todos los campos requeridos');
                        return;
                    }

                    this.isLoading = true;

                    const payload = {
                        compras: this.comprasSeleccionadas.map(compra => ({
                            id: compra.id,
                        })),
                        abono: {
                            fecha_abono_compra: this.fechaAbonoIngresada,
                            forma_pago_abono_compra: this.formaDePagoAbonoIngresada,
                            descripcion_abono_compra: this.descripcionAbonoIngresada,
                            imagen_abono_compra: null,
                            user_id: this.usuarioAbonoIngresado,
                        },
                    };
                    console.log('Payload para generar abonos:', payload);

                    this.$wire.createAbonos(payload)
                        .then(() => {
                            this.isLoading = false;
                            //alert('Abonos guardados correctamente');
                        })
                        .catch((error) => {
                            this.isLoading = false;
                            console.error('Error al guardar abonos:', error);
                            alert('Error al guardar abonos');
                        });
                }
            }
        }
    </script>

</div>
