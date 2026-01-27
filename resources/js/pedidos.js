import Alpine from 'alpinejs';

document.addEventListener('alpine:init', () => {
    Alpine.store('pedido', {
        productos: [], // Se debe setear desde cada x-data
        getPrecio(detalle, tipoPrecio) {
            const prod = this.productos.find(p => p.id == detalle.producto_id);
            if (!prod) return 0;
            switch (tipoPrecio) {
                case 'MAYORISTA': return prod.valor_mayorista_producto ?? 0;
                case 'FERRETERO': return prod.valor_ferretero_producto ?? 0;
                default: return prod.valor_detal_producto ?? 0;
            }
        },
        getPrecioConIva(detalle, tipoPrecio) {
            const prod = this.productos.find(p => p.id == detalle.producto_id);
            let precio = detalle.precio_unitario || this.getPrecio(detalle, tipoPrecio);
            if (detalle.aplicar_iva && prod && prod.iva_producto) {
                precio = precio * (1 + prod.iva_producto / 100);
            }
            return Math.round(precio * 100) / 100;
        },
        getSubtotal(detalle, tipoPrecio) {
            const prod = this.productos.find(p => p.id == detalle.producto_id);
            if (!prod) return 0;
            let precio = this.getPrecioConIva(detalle, tipoPrecio);
            return Math.round(precio * detalle.cantidad * 100) / 100;
        },
        getSubTotalGeneral(detalles, tipoPrecio) {
            return detalles.reduce((acc, detalle) => acc + this.getSubtotal(detalle, tipoPrecio), 0);
        }
    });
});
