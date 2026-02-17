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
        <button type="button" @click="generarAbonos()"
            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow transition flex items-center justify-center" :disabled="isLoading || comprasSeleccionadas.length === 0">
            <template x-if="isLoading">
                <svg class="animate-spin h-5 w-5 mr-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
            </template>
            <span x-text="isLoading ? 'Generando...' : 'Generar Abonos'"></span>
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
                    return this.comprasSeleccionadas.reduce((acc, compra) => acc + parseFloat(compra.total_a_pagar ||
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
                            alert('Abonos guardados correctamente');
                        })
                        .catch((error) => {
                            console.error('Error al guardar abonos:', error);
                            alert('Error al guardar abonos');
                        });
                }
            }
        }
    </script>

</div>
