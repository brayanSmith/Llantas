<div x-show="mostrarModalAgregarProducto" x-transition class="fixed inset-0 z-40 flex items-center justify-center"
    style="display: none;">
    <div @click.prevent="mostrarModalAgregarProducto = false"
        class="absolute inset-0 bg-white/40 dark:bg-neutral-900/40 backdrop-blur-sm transition-all">
    </div>
    <div class="relative bg-white dark:bg-neutral-800 rounded-2xl shadow-2xl w-full max-w-lg mx-auto p-6 z-50">
        <button @click.prevent="mostrarModalAgregarProducto = false"
            class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-white text-2xl">&times;</button>
        <h4 class="text-lg md:text-md font-bold text-gray-800 dark:text-gray-100 mb-4 text-center"
            x-text="productoSeleccionado ? productoSeleccionado.concatenar_codigo_nombre : ''"></h4>
        <div class="mb-4 flex flex-row items-end gap-2">
            <div class="flex-1">
                <label class="block text-xs md:text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cantidad</label>
                <input type="number" min="1" x-model.number="cantidadSeleccionada"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-gray-100 text-xs md:text-base" />

                <label class="block text-xs md:text-sm font-medium text-gray-700 dark:text-gray-300 mt-3 mb-1">Precio</label>
                <input type="number" min="0" x-model.number="precioSeleccionado"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-gray-100 text-xs md:text-base" />

            </div>
            <br>
            <!-- Eliminado el bloque de producto, ahora el nombre aparece como título -->
            <button x-show="accionModalAgregarProducto === 'agregar'"
                @click.prevent="
                agregarDetalle(productoSeleccionado, cantidadSeleccionada, precioSeleccionado);
                mostrarModalAgregarProducto = false; calcularTotales();
                "
                class="py-2 px-4 bg-indigo-600 text-white font-bold rounded-lg transition hover:bg-indigo-700 whitespace-nowrap text-xs md:text-base">
                Agregar
            </button>

            <button x-show="accionModalAgregarProducto === 'editar'"
                @click.prevent="
                editarDetalle(indiceDetalleSeleccionado, cantidadSeleccionada, precioSeleccionado);
                mostrarModalAgregarProducto = false; calcularTotales(); mostrarModalPanelDerecho = true;
                "
                class="py-2 px-4 bg-indigo-600 text-white font-bold rounded-lg transition hover:bg-indigo-700 whitespace-nowrap text-xs md:text-base">
                Editar
            </button>
        </div>
    </div>
</div>
