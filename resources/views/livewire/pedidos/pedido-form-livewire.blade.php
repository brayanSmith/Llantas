<div
    x-data="pedidoForm(
        @js($clientes),
        @js($alistadores),
        @js($bodegas),
        @js($productos),
        @js($users)
    )"
    x-init="init()"
    class="space-y-4"
>
    @include('livewire.pedidos.livewire-pedidos-seccion-general')
    @include('livewire.pedidos.livewire-pedidos-seccion-detalle')
    @include('livewire.pedidos.livewire-pedidos-seccion-resumen')
    <br>
    <button @click="enviar()" type="button" class="bg-blue-600 text-white px-4 py-2 rounded">Guardar Pedido</button>
</div>
<script>
function pedidoForm(clientes = [], alistadores = [], bodegas = [], productos = [], users = []) {
    return {
        clientes: clientes,
        alistadores: alistadores,
        bodegas: bodegas,
        productos: productos,
        users: users,
        pedido: {
            codigo: '',
            fe: '',
            cliente_id: null,
            fecha: '',
            dias_plazo_vencimiento: null,
            fecha_vencimiento: '',
            ciudad: '',
            estado: 'PENDIENTE',
            stock_retirado: false,
            en_cartera: false,
            metodo_pago: '',
            tipo_precio: '',
            tipo_venta: '',
            estado_pago: 'EN_CARTERA',
            estado_cartera: 'CARTERA_AL_DIA',
            estado_venta: '',
            estado_vencimiento: 'AL_DIA',
            bodega_id: "1",
            primer_comentario: '',
            subtotal: 0,
            abono: 0,
            descuento: 0,
            flete: 0,
            total_a_pagar: 0,
            saldo_pendiente: 0,
            user_id: null,
            alistador_id: null,
            detalles: [],
            created_at: '',
            updated_at: '',
            iva: 0
        },
        init() {
            // Inicialización si necesitas
        },
        agregarDetalle() {
            this.pedido.detalles.push({
                producto_id: null,
                cantidad: 1,
                precio_unitario: 0,
                aplicar_iva: true,
                iva: 0,
                precio_con_iva: 0,
                subtotal: 0
            });
        },
        enviar() {
            console.log('JSON generado para enviar:', JSON.stringify(this.pedido, null, 2));
            console.log('Llamando a método Livewire: guardarPedido');
            this.$wire.guardarPedido(this.pedido);
        }
    }
}
</script>
