<div x-data="{
    {{-- Función para agregar un nuevo detalle al pedido --}}
    // Datos enlazados con Livewire
    detalles: @entangle('pedido.detalles').defer || [],
    productos: @js($productos),
    //obtener el tipo de precio seleccionado
    get tipoPrecio() {
        return this.pedido.tipo_precio;
    },
    //funcion para agregar un nuevo detalle
    agregarDetalle() {
        this.pedido.detalles.push({ producto_id: null, cantidad: 1, precio_unitario: null, aplicar_iva: true });
    },
    //funcion para eliminar un detalle
    removeDetalle(index) {
        this.pedido.detalles.splice(index, 1);
    },
    //funcion para obtener el precio segun el tipo de precio
    getPrecio(detalle, tipoPrecio) {
        const prod = this.productos.find(p => p.id == detalle.producto_id);
        if (!prod) return 0;
        switch (this.tipoPrecio) {
            case 'MAYORISTA': return prod.valor_mayorista_producto ?? 0;
            case 'FERRETERO': return prod.valor_ferretero_producto ?? 0;
            default: return prod.valor_detal_producto ?? 0;
        }
    },
    //funcion para obtener el precio con iva
    getPrecioConIva(detalle, tipoPrecio) {
        const prod = this.productos.find(p => p.id == detalle.producto_id);
        let precio = detalle.precio_unitario || this.getPrecio(detalle, tipoPrecio);
        if (detalle.aplicar_iva && prod && prod.iva_producto) {
            precio = precio * (1 + prod.iva_producto / 100);
        }
        return Math.round(precio * 100) / 100;
    },
    //funcion para obtener el subtotal del detalle
    getSubtotal(detalle) {
        const prod = this.productos.find(p => p.id == detalle.producto_id);
        if (!prod) return 0;
        let precio = this.getPrecioConIva(detalle, this.tipoPrecio);
        return Math.round(precio * detalle.cantidad * 100) / 100;
    },
    //funcion para obtener el total del pedido
    getTotal() {
        return this.pedido.detalles.reduce((acc, detalle) => acc + this.getSubtotal(detalle), 0);
    }
}">

    <h2 class="text-xl font-bold mb-4">Detalle del Pedido</h2>
    <button type="button" @click="agregarDetalle" class="bg-green-600 text-white px-4 py-2 rounded mb-4">Agregar Detalle</button>
    <table class="table-auto w-full mb-4">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
                <th>IVA</th>
                <th>Precio + IVA</th>
                <th>Subtotal</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <template x-for="(detalle, index) in pedido.detalles" :key="index">
                <tr>
                    <td>
                        <select x-model="detalle.producto_id" class="form-select form-select-sm">
                            <option value="">Seleccione producto</option>
                            <template x-for="prod in productos" :key="prod.id">
                                <option :value="prod.id" x-text="prod.concatenar_codigo_nombre"></option>
                            </template>
                        </select>
                    </td>
                    <td>
                        <input type="number" min="1" x-model.number="detalle.cantidad" class="form-control form-control-sm w-20" />
                    </td>
                    <td>
                        <input type="number" min="0" x-model.number="detalle.precio_unitario" class="form-control form-control-sm w-24" :value="getPrecio(detalle, tipoPrecio)" />
                    </td>
                    <td>
                        <input type="checkbox" x-model="detalle.aplicar_iva" class="form-check-input" />
                    </td>
                        <input type="number" readonly x-model="detalle.precio_con_iva" :value="getPrecioConIva(detalle, tipoPrecio)" class="form-control form-control-sm w-24 bg-light" />
                    </td>
                    <td>
                        <input type="number" readonly x-model="detalle.subtotal" :value="getSubtotal(detalle)" class="form-control form-control-sm w-24 bg-light" />
                    </td>
                    <td>
                        <button type="button" @click="removeDetalle(index)" class="btn btn-danger btn-sm">Eliminar</button>
                    </td>
                </tr>
            </template>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="text-right font-bold">Total:</td>
                <input type="number" readonly :value="getTotal()" x-model="pedido.subtotal" class="form-control w-32 bg-light font-weight-bold" />
                <td></td>
            </tr>
        </tfoot>
    </table>


</div>
