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
            metodo_pago: 'CREDITO',
            tipo_precio: 'FERRETERO',
            tipo_venta: 'ELECTRONICA',
            estado_pago: 'EN_CARTERA',
            estado_cartera: 'CARTERA_AL_DIA',
            estado_venta: 'VENTA',
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
        // Obtener el tipo de precio seleccionado
        get tipoPrecio() {
            return this.pedido.tipo_precio;
        },
        // Funciones para manejar los detalles del pedido
        agregarDetalle() {
            this.pedido.detalles.push({
                producto_id: null,
                cantidad: 1,
                precio_unitario: null,
                aplicar_iva: true
            });
        },
        // Funcion para Remover Algun Detalle
        removeDetalle(index) {
            this.pedido.detalles.splice(index, 1);
        },
        //Funcion para Obtener Precio Segun Tipo
        getPrecio(detalle, tipoPrecio) {
            const prod = this.productos.find(p => p.id == detalle.producto_id);
            if (!prod) return 0;
            switch (this.tipoPrecio) {
                case 'MAYORISTA': return prod.valor_mayorista_producto ?? 0;
                case 'FERRETERO': return prod.valor_ferretero_producto ?? 0;
                default: return prod.valor_detal_producto ?? 0;
            }
        },
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
            let precio = this.getPrecioConIva(detalle, this.tipoPrecio);
            return Math.round(precio * detalle.cantidad * 100) / 100;
        },
        //Funcion para Obtener Total General
        getTotal() {
            return this.pedido.detalles.reduce((acc, detalle) => acc + this.getSubtotal(detalle), 0);
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
                /*if (detalle.precio_unitario === null || detalle.precio_unitario === '' || isNaN(detalle.precio_unitario)) {
                    errores.push(`Debe ingresar el precio unitario en la fila ${idx + 1}`);
                }*/
            });
            return errores;
        },
        enviar() {
            const errores = this.validarDetalles();
            if (errores.length > 0) {
                alert(errores.join('\n'));
                return;
            }
            console.log('JSON generado para enviar:', JSON.stringify(this.pedido, null, 2));
            console.log('Llamando a método Livewire: guardarPedido');
            this.$wire.guardarPedido(this.pedido);
        }
    }
}
</script>
