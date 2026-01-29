<div class="flex h-screen bg-gray-100 dark:bg-neutral-900 font-sans antialiased text-gray-800 dark:text-gray-100">

        <!-- Panel izquierdo -->
        <div class="w-full px-1 py-6 md:px-6 flex flex-col">

            <h2 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-6">
                Productos

                <!-- Indicador de datos guardados -->
                {{--@if(count($this->cart) > 0 || $cliente_id || $primer_comentario || $segundo_comentario)
                    <span class="ml-3 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        Datos guardados
                    </span>
                @endif--}}
            </h2>

            <!-- Buscador -->
            @include('livewire.pos.componentes-panel-izquierdo.buscador-productos')
            <!-- Listado de productos -->
             @include('livewire.pos.componentes-panel-izquierdo.galeria-productos')
            <!-- Paginación Alpine.js -->
                <div class="mt-6 flex gap-2 justify-center">
                    <button type="button" @click="cambiarPaginaProductos(paginaProductos - 1)" :disabled="paginaProductos === 1" class="px-3 py-1 rounded bg-gray-200 dark:bg-neutral-700">Anterior</button>
                    <span x-text="paginaProductos + ' / ' + totalPaginasProductos"></span>
                    <button type="button" @click="cambiarPaginaProductos(paginaProductos + 1)" :disabled="paginaProductos === totalPaginasProductos" class="px-3 py-1 rounded bg-gray-200 dark:bg-neutral-700">Siguiente</button>
                </div>
        </div>
    </div>
