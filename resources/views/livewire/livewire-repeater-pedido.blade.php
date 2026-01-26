<div x-data="{
    items: @entangle('items').defer || [],
    productos: @js($productos ?? []),
    tipoPrecio: 'DETAL',
    addItem() {
        this.items.push({ producto_id: '', cantidad: 1, precio_manual: null, incluir_iva: true });
    },
    removeItem(index) {
        this.items.splice(index, 1);
    },
    getPrecio(item, tipoPrecio) {
        const prod = this.productos.find(p => p.id == item.producto_id);
        if (!prod) return 0;
        switch (tipoPrecio) {
            case 'MAYORISTA': return prod.valorMayorista ?? 0;
            case 'FERRETERO': return prod.valorFerretero ?? 0;
            default: return prod.valorDetal ?? 0;
        }
    },
    getSubtotal(item) {
        const prod = this.productos.find(p => p.id == item.producto_id);
        let precio = this.getPrecio(item, this.tipoPrecio);
        if (item.incluir_iva && prod && prod.iva) {
            precio = precio * (1 + prod.iva / 100);
        }
        return Math.round((precio * (item.cantidad ?? 1)) * 100) / 100;
    },
    getTotal() {
        return this.items.reduce((acc, item) => acc + this.getSubtotal(item), 0);
    }
}">
    <h1 class="text-2xl font-bold mb-4">Detalle de productos (Alpine + Livewire)</h1>
    <button type="button" @click="addItem" class="btn btn-primary mb-4">Agregar ítem</button>
    <table class="table-auto w-full mb-4">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio</th>
                <th>IVA</th>
                <th>Subtotal</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <template x-for="(item, index) in items" :key="index">
                <tr>
                    <td>
                        <select x-model="item.producto_id" class="input">
                            <option value="">Seleccione producto</option>
                            <template x-for="prod in productos" :key="prod.id">
                                <option :value="prod.id" x-text="prod.concatenarCodigoNombre"></option>
                            </template>
                        </select>
                    </td>
                    <td>
                        <input type="number" min="1" x-model.number="item.cantidad" class="input w-20" />
                    </td>
                    <td>
                        <input type="number" min="0" x-model.number="item.precio_manual" class="input w-24" :placeholder="getPrecio({ ...item, precio_manual: null }, tipoPrecio)" />
                    </td>
                    <td>
                        <input type="checkbox" x-model="item.incluir_iva" />
                    </td>
                    <td x-text="getSubtotal(item)"></td>
                    <td>
                        <button type="button" @click="removeItem(index)" class="btn btn-danger">Eliminar</button>
                    </td>
                </tr>
            </template>
        </tbody>
    </table>
    <div class="font-bold">Total: <span x-text="getTotal()"></span></div>
    <button type="button" class="btn btn-success mt-4" @click="$wire.set('items', items)">Guardar</button>
</div>

