<div x-data="{
    pedido: @entangle('pedido').defer,
    productos: @js($productos),
    get tipoPrecio() {
        return this.pedido.tipo_precio;
    },
    getSubTotalGeneral() {
        if (!this.pedido || !Array.isArray(this.pedido.detalles)) return 0;
        return this.pedido.detalles.reduce((acc, detalle) => {
            if (!detalle) return acc;
            const prod = this.productos.find(p => p.id == detalle.producto_id);
            if (!prod) return acc;
            let precio = detalle.precio_unitario || (
                prod.valor_mayorista_producto ??
                prod.valor_ferretero_producto ??
                prod.valor_detal_producto ?? 0
            );
            if (detalle.aplicar_iva && prod.iva_producto) {
                precio = precio * (1 + prod.iva_producto / 100);
            }
            return acc + Math.round((precio * (detalle.cantidad ?? 0)) * 100) / 100;
        }, 0);
    },
}">
    <div>
        <label>Subtotal</label>
        <input type="number" :value="getSubTotalGeneral()" class="border rounded w-full" readonly />
    </div>
</div>
