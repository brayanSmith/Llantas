<div class="mt-6 col-span-2">
    <h3 class="font-bold mb-2">Detalles del Pedido</h3>
    <template x-for="(detalle, i) in pedido.detalles" :key="i">
        <div class="border p-3 mb-2 rounded grid grid-cols-4 gap-2">
            <div>
                <label>Producto ID</label>
                <input type="number" x-model="detalle.producto_id" class="border rounded w-full" />
            </div>
            <div>
                <label>Cantidad</label>
                <input type="number" x-model="detalle.cantidad" class="border rounded w-full" />
            </div>
            <div>
                <label>Precio Unitario</label>
                <input type="number" x-model="detalle.precio_unitario" class="border rounded w-full" />
            </div>
            <div>
                <label>IVA</label>
                <input type="number" x-model="detalle.iva" class="border rounded w-full" />
            </div>
        </div>
    </template>
    <button type="button" @click="agregarDetalle()" class="mt-2 bg-green-600 text-white px-3 py-1 rounded">Agregar Detalle</button>
</div>
