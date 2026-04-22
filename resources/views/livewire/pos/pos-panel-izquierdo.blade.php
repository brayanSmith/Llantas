<div class="flex h-screen bg-gray-100 dark:bg-neutral-900 font-sans antialiased text-gray-800 dark:text-gray-100">

    <!-- Panel izquierdo -->
    <div class="w-full px-1 py-6 md:px-6 flex flex-col">

        <h2 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-6">
            Productos
        </h2>
        <!-- Radio button con las bodegas -->
        <div x-init="
            const bodegaGuardada = localStorage.getItem('bodegaSeleccionada');

            if (bodegaGuardada !== null && bodegaGuardada !== '') {
                bodegaSeleccionada = Number(bodegaGuardada);
                pedido.bodega_id = Number(bodegaGuardada);
            } else if (bodegaSeleccionada !== null && bodegaSeleccionada !== '') {
                localStorage.setItem('bodegaSeleccionada', Number(bodegaSeleccionada));
            }

            $watch('bodegaSeleccionada', value => {
                const valorNormalizado = value !== null && value !== '' ? Number(value) : '';
                localStorage.setItem('bodegaSeleccionada', valorNormalizado);
                pedido.bodega_id = valorNormalizado;
            });
        ">
            <!-- Selector de bodega -->
            <div x-show="esAdmin" class="mb-4">
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Selecciona una bodega:</p>
            <template  x-for="bodega in bodegas" :key="bodega.id">
                <label>
                    <input type="radio" :value="bodega.id" x-model.number="bodegaSeleccionada"
                        :disabled="pedido.detalles && pedido.detalles.length > 0"
                        @change="
                            pedido.bodega_id = bodegaSeleccionada;
                            localStorage.setItem('bodegaSeleccionada', bodegaSeleccionada);
                            console.log('Bodega seleccionada:', bodegaSeleccionada);
                        "
                    >
                    <span x-text="bodega.nombre_bodega"></span>
                </label>
                            <template x-if="pedido.detalles && pedido.detalles.length > 0">
                                <p class="text-xs text-red-600 mb-2">No puedes cambiar de bodega mientras haya productos en el carrito.</p>
                            </template>
            </template>
            </div>

            <!-- Seleccionador de Bodega con alpine.js -->


            <!-- Buscador -->
            @include('livewire.pos.componentes-panel-izquierdo.buscador-productos')
            <!-- Listado de productos -->
            @include('livewire.pos.componentes-panel-izquierdo.galeria-productos')
            <!-- Paginación Numérica Alpine.js -->
            <div class="mt-6 flex gap-1 justify-center flex-wrap" x-show="totalPaginasProductos > 1">
                <!-- Botón Anterior -->
                <button type="button" @click="cambiarPaginaProductos(paginaProductos - 1)"
                    :disabled="paginaProductos === 1" class="px-2 py-1 rounded text-sm"
                    :class="paginaProductos === 1 ?
                        'bg-gray-100 dark:bg-neutral-800 text-gray-400 dark:text-gray-600 cursor-not-allowed' :
                        'bg-gray-200 dark:bg-neutral-700 hover:bg-gray-300 dark:hover:bg-neutral-600'">←
                    Anterior</button>

                <!-- Números de página -->
                <template x-for="pagina in paginasArray" :key="pagina">
                    <button type="button" @click="cambiarPaginaProductos(pagina)"
                        :class="paginaProductos === pagina ? 'bg-blue-500 text-white dark:bg-blue-600' :
                            'bg-gray-200 dark:bg-neutral-700 hover:bg-gray-300 dark:hover:bg-neutral-600'"
                        class="px-2 py-1 rounded text-sm transition-colors" x-text="pagina"></button>
                </template>

                <!-- Botón Siguiente -->
                <button type="button" @click="cambiarPaginaProductos(paginaProductos + 1)"
                    :disabled="paginaProductos === totalPaginasProductos" class="px-2 py-1 rounded text-sm"
                    :class="paginaProductos === totalPaginasProductos ?
                        'bg-gray-100 dark:bg-neutral-800 text-gray-400 dark:text-gray-600 cursor-not-allowed' :
                        'bg-gray-200 dark:bg-neutral-700 hover:bg-gray-300 dark:hover:bg-neutral-600'">Siguiente
                    →</button>
            </div>
        </div>
    </div>
</div>
