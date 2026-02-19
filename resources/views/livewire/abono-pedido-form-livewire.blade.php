<div x-data="abonoPedidosForm({
    clientes: @js($clientes),
    formasPago: @js($formasPago),
    users: @js($users),
    abonos: @js($abonos),

    clienteAbonoIngresado: null,
    usuarioAbonoIngresado: null,
    valorAbonoIngresado: null,
    fechaAbonoIngresada: null,
    formaDePagoAbonoIngresada: null,
    descripcionAbonoIngresada: null,

})" x-init="init()" class="space-y-4">
    <div class="bg-white dark:bg-gray-900 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 p-6 mb-6">
        @include('livewire.abonosPedidos.livewire-abono-pedidos-buscador')
    </div>
    <div class="bg-white dark:bg-gray-900 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 p-6 mb-6">
        @include('livewire.abonosPedidos.livewire-abono-pedidos-lista')
    </div>
    <div>
        @include('livewire.abonosPedidos.livewire-abono-pedidos-boton-guardar')
    </div>
    <div>
        @include('livewire.abonosPedidos.componentes.pedidos-modal-abonos')
    </div>

    <script>
        function abonoPedidosForm({
            clientes,
            formasPago,
            users,
            abonos,
            clienteAbonoIngresado,
            usuarioAbonoIngresado,
            valorAbonoIngresado,
            fechaAbonoIngresada,
            formaDePagoAbonoIngresada,
            descripcionAbonoIngresada,
        }) {
            return {
                clientes,
                formasPago,
                users,
                abonos,
                clienteAbonoIngresado,
                usuarioAbonoIngresado,
                valorAbonoIngresado,
                fechaAbonoIngresada,
                formaDePagoAbonoIngresada,
                descripcionAbonoIngresada,
                pedidosEnCartera: [],
                pedidosSeleccionados: [],
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

                buscarPedidosEnCartera() {
                    if (!this.clienteAbonoIngresado) {
                        alert('Debe seleccionar un cliente');
                        return;
                    }

                    this.isLoading = true;
                    this.pedidosSeleccionados = []; // Limpiar selección
                    console.log('Buscando pedidos del cliente:', this.clienteAbonoIngresado);

                    this.$wire.buscarPedidosEnCartera(this.clienteAbonoIngresado)
                        .then((pedidos) => {
                            this.pedidosEnCartera = pedidos;
                            this.isLoading = false;
                            this.pedidosSeleccionados = []; // Limpiar selección después de cargar
                            console.log('Pedidos encontrados:', pedidos);
                        })
                        .catch((error) => {
                            this.isLoading = false;
                            console.error('Error al buscar pedidos:', error);
                            alert('Error al buscar pedidos');
                        });
                },

                togglePedido(pedido) {
                    const index = this.pedidosSeleccionados.findIndex(c => c.id === pedido.id);
                    if (index > -1) {
                        this.pedidosSeleccionados.splice(index, 1);
                    } else {
                        this.pedidosSeleccionados.push(pedido);
                    }
                    console.log('Pedidos seleccionados:', this.pedidosSeleccionados);
                },

                // Computed: Total a pagar de todos los pedidos seleccionados
                get totalAPagarSeleccionado() {
                    return this.pedidosSeleccionados.reduce((acc, pedido) => acc + parseFloat(pedido.total_a_pagar ||
                        0), 0);
                },

                // Computed: Cantidad de pedidos seleccionados
                get cantidadPedidosSeleccionados() {
                    return this.pedidosSeleccionados.length;
                },

                // Computed: Restante después de ingresar el valor del abono
                get restante() {
                    const valorAbono = parseFloat(this.valorAbonoIngresado) || 0;
                    return valorAbono - this.totalAPagarSeleccionado;
                },

                seleccionarAutomaticamente() {
                    const valorAbono = parseFloat(this.valorAbonoIngresado) || 0;
                    if (valorAbono <= 0 || this.pedidosEnCartera.length === 0) return;

                    // Ordenar pedidos por total_a_pagar de menor a mayor
                    const pedidosOrdenados = [...this.pedidosEnCartera].sort((a, b) =>
                        parseFloat(a.total_a_pagar || 0) - parseFloat(b.total_a_pagar || 0)
                    );

                    // Limpiar selección anterior
                    this.pedidosSeleccionados = [];
                    let acumulado = 0;

                    // Seleccionar pedidos hasta alcanzar o superar el valor del abono
                    for (let pedido of pedidosOrdenados) {
                        const montoPedido = parseFloat(pedido.total_a_pagar || 0);
                        if (acumulado + montoPedido <= valorAbono) {
                            this.pedidosSeleccionados.push(pedido);
                            acumulado += montoPedido;
                        } else if (acumulado < valorAbono) {
                            // Si falta poco, incluir este pedido también
                            if (valorAbono - acumulado >= montoPedido * 0.5) {
                                this.pedidosSeleccionados.push(pedido);
                                acumulado += montoPedido;
                            }
                            break;
                        } else {
                            break;
                        }
                    }

                    console.log('Pedidos seleccionados automáticamente:', this.pedidosSeleccionados, 'Total: ', acumulado);
                },

                generarAbonos() {
                    if (this.pedidosSeleccionados.length === 0) {
                        alert('Debe seleccionar al menos un pedido');
                        return;
                    }
                    if (!this.valorAbonoIngresado || !this.fechaAbonoIngresada ||
                        !this.formaDePagoAbonoIngresada || !this.usuarioAbonoIngresado) {
                        alert('Debe completar todos los campos requeridos');
                        return;
                    }

                    this.isLoading = true;

                    const payload = {
                        pedidos: this.pedidosSeleccionados.map(pedido => ({
                            id: pedido.id,
                        })),
                        abono: {
                            fecha: this.fechaAbonoIngresada,
                            forma_pago: this.formaDePagoAbonoIngresada,
                            descripcion: this.descripcionAbonoIngresada,
                            imagen: null,
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
