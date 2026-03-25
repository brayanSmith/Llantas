<div class="flex h-screen bg-gray-100 dark:bg-neutral-900 font-sans antialiased text-gray-800 dark:text-gray-100">

    <!-- Panel izquierdo -->
    <div class="w-full px-1 py-6 md:px-6 flex flex-col">

        <h2 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-6">
            Productos

            <!-- Indicador de datos guardados -->
            {{-- @if (count($this->cart) > 0 || $cliente_id || $primer_comentario || $segundo_comentario)
                    <span class="ml-3 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        Datos guardados
                    </span>
                @endif --}}
        </h2>


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
