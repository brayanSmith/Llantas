<div x-data="{
    init() {
        if (window.Alpine && Alpine.store('pedido')) {
            Alpine.store('pedido').productos = @js($productos);
        } else {
            document.addEventListener('alpine:init', () => {
                Alpine.store('pedido').productos = @js($productos);
            });
        }
    },
    get tipoPrecio() {
        return this.pedido.tipo_precio;
    },
    getSubTotalGeneral() {
        return (window.Alpine && Alpine.store('pedido'))
            ? Alpine.store('pedido').getSubTotalGeneral(this.pedido.detalles, this.tipoPrecio)
            : 0;
    }
}"
 x-init="init()"
>
    <div>
        <label>Subtotal</label>
        <input type="number" :value="getSubTotalGeneral()" class="border rounded w-full" readonly />
    </div>
    <div>
        <label>IVA</label>
        <input type="number" x-model="pedido.iva" class="border rounded w-full" readonly />
    </div>
    <div>
        <label>Total a Pagar</label>
        <input type="number" x-model="pedido.total_a_pagar" class="border rounded w-full" readonly />
    </div>
    <div>
        <label>Saldo Pendiente</label>
        <input type="number" x-model="pedido.saldo_pendiente" class="border rounded w-full" readonly />
    </div>
</div>
