<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div
        x-data="{
            state: $wire.entangle(@js($getStatePath())),
            //state: $wire.entangle(@js($getStatePath())) ?? [],
            products: @js($getMeta('products') ?? []),
            tipo_precio: 'DETAL',
            getPrecio(producto_id, tipo_precio) {
                if (!producto_id) return '';
                const prod = this.products.find(p => p.id == producto_id);
                if (!prod) return '';
                switch (tipo_precio) {
                    case 'MAYORISTA': return prod.valorMayorista ?? 0;
                    case 'FERRETERO': return prod.valorFerretero ?? 0;
                    default: return prod.valorDetal ?? 0;
                }
            },
            addItem() {
                if (!Array.isArray(this.state)) this.state = [];
                this.state.push({ producto_id: '', cantidad: 1 });
            },
            removeItem(index) {
                if (!Array.isArray(this.state)) return;
                this.state.splice(index, 1);
            },
            setValorConIva(item) {
                if (!item.producto_id) return 0;
                const prod = this.products.find(p => p.id == item.producto_id);
                let precio = (item.precio_manual !== undefined && item.precio_manual !== null && !isNaN(item.precio_manual))
                    ? parseFloat(item.precio_manual)
                    : parseFloat(this.getPrecio(item.producto_id, this.tipo_precio)) || 0;
                if (item.incluir_iva && prod && prod.iva) {
                    precio = precio * (1 + prod.iva / 100);
                }
                return isNaN(precio) ? 0 : precio;
            },
            setSubTotal(item){
                let valorUnitario = this.setValorConIva(item);
                let cantidad = parseInt(item.cantidad) || 1;
                return cantidad * valorUnitario;
            }
        }"
        x-init="window.addEventListener('tipo-precio-changed', e => { tipo_precio = e.detail; }); console.log('STATE INIT:', state);"
        x-effect="console.log('STATE CHANGE:', JSON.parse(JSON.stringify(state)))"
        {{ $getExtraAttributeBag() }}
        class="space-y-2"
    >
        <template x-for="(item, index) in state" :key="index">
            <div class="flex gap-2 items-center">
                <select x-model="item.producto_id" class="input"
                    @change="if (item.incluir_iva === undefined) item.incluir_iva = true">
                    <option value="">Seleccione producto</option>
                    <template x-for="prod in products" :key="prod.id">
                        <option :value="prod.id" x-text="prod.concatenarCodigoNombre"></option>
                    </template>
                </select>
                <input type="number" class="input w-24" x-model.number="item.cantidad" placeholder="Cantidad" min="1" />
                <span class="ml-2">
                    <template x-if="item.producto_id">
                        <input type="number" class="input w-24" min="0"
                            x-model.number="item.precio_manual"
                            :placeholder="item.producto_id ? getPrecio(item.producto_id, tipo_precio) : ''"
                            @focus="if(item.precio_manual === undefined) item.precio_manual = getPrecio(item.producto_id, tipo_precio)"
                        />
                    </template>
                </span>
                <!--Radio button para poner si necesitamos poner el iva-->
                <input type="checkbox" x-model="item.incluir_iva" class="ml-4" checked/> Incluir IVA
                <input type="number" class="input w-32 ml-4"
                    :value="setValorConIva(item)"
                    readonly
                    placeholder="Valor con IVA" />
                <input type="number" class="input w-32 ml-4"
                    :value="setSubTotal(item)"
                    readonly
                    placeholder="Subtotal" />

                <button type="button" class="btn btn-danger" @click="removeItem(index)">Eliminar</button>
            </div>
        </template>
        <button type="button" class="btn btn-primary" @click="addItem">Agregar ítem</button>
    </div>
</x-dynamic-component>
