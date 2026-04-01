<div class="flex-grow pr-2 overflow-y-auto max-h-96">
    <template x-for="(detalle, index) in pedido.detalles" :key="detalle.producto_id">
        <div @click="
            productoSeleccionado = productos.find(p => p.id == detalle.producto_id);
            cantidadSeleccionada = detalle.cantidad;
            precioSeleccionado = detalle.precio_unitario;
            indiceDetalleSeleccionado = index;
            mostrarModalAgregarProducto = true;
            accionModalAgregarProducto = 'editar';
            mostrarModalPanelDerecho = false;
        "
            class="flex items-center justify-between p-2 mb-2 bg-gray-50 dark:bg-neutral-700 rounded-xl shadow-sm">
            <div class="flex-1">
                <h4 class="text-xs font-semibold text-gray-800 dark:text-gray-100">
                    <span
                        x-text="productos.find(p => p.id == detalle.producto_id)?.concatenar_codigo_nombre || 'Sin nombre'"></span>
                </h4>
                <p class="text-xs text-gray-500 dark:text-gray-400">COP:
                    <span
                        x-text="Number(detalle.precio_unitario).toLocaleString('es-CO', { style: 'currency', currency: 'COP', minimumFractionDigits: 2 })"></span>

                <p class="text-xs text-gray-500 dark:text-gray-400 font-bold">TOTAL:
                    <span
                        x-text="Number(getSubtotal(detalle.precio_unitario, detalle.cantidad)).toLocaleString('es-CO', { style: 'currency', currency: 'COP', minimumFractionDigits: 2 })"></span>
                </p>
                </p>
            </div>
            <div class="flex items-center space-x-2">
                <input type="number" min="1" x-model.number="detalle.cantidad"
                    @input.debounce.300ms="actualizarCantidad(index); calcularTotales();"
                    class="py-2.5 sm:py-3 px-4 block w-20 border-gray-200 rounded-lg sm:text-sm
                        focus:border-blue-500 focus:ring-blue-500
                        dark:bg-neutral-900 dark:border-neutral-700
                        dark:text-neutral-400 dark:placeholder-neutral-500
                        dark:focus:ring-neutral-600">
                <button @click.prevent="removeDetalle(index)"
                    class="p-2 text-red-500 hover:text-red-700 dark:hover:text-red-400">
                    ✕
                </button>
            </div>
        </div>
    </template>
    <template x-if="pedido.detalles.length === 0">
        <p class="text-gray-500 dark:text-gray-400">Tu carrito está vacío.</p>
    </template>
</div>
