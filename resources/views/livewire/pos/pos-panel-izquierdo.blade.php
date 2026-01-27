<div class="flex h-screen bg-gray-100 dark:bg-neutral-900 font-sans antialiased text-gray-800 dark:text-gray-100">

        <!-- Panel izquierdo -->
        <div class="w-full px-1 py-6 md:px-6 flex flex-col">

            <h2 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-6">
                Productos

                <!-- Indicador de datos guardados -->
                @if(count($this->cart) > 0 || $cliente_id || $primer_comentario || $segundo_comentario)
                    <span class="ml-3 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        Datos guardados
                    </span>
                @endif
            </h2>

            <!-- Buscador -->
            <div class="flex-shrink-0 mb-4">
                <input wire:model.live="search" type="text" placeholder="Buscar productos por nombre o SKU..."
                    class="w-full px-5 py-3 border focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors
                       dark:bg-neutral-800 dark:border-blue-700 dark:text-gray-100">

                @if (session()->has('error'))
                    <div
                        class="mt-2 p-4 bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300 rounded-lg shadow-md">
                        {{ session('error') }}
                    </div>
                @endif

                @if (session()->has('success'))
                    <div
                        class="mt-2 p-4 bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 rounded-lg shadow-md">
                        {{ session('success') }}
                    </div>
                @endif
            </div>

            <!-- Listado de productos -->
            <div class="flex-grow overflow-y-auto pr-1">
                @php($products = $this->filteredProducts) {{-- paginator --}}
                <div class="grid grid-cols-1 gap-2 md:gap-6">
                    @forelse($products as $product)
                        <div wire:key="prod-{{ $product->id }}"
                            class="bg-grey-100 dark:bg-neutral-800 rounded-2xl shadow-lg">
                            <div class="bg-white dark:bg-neutral-800 rounded-2xl shadow-lg overflow-hidden transition-all duration-200 transform hover:scale-105 hover:shadow-xl p-2 md:p-4">

                                <!-- Encabezado: Nombre del producto -->
                                <div class="w-full mb-2">
                                    {{--<p class="font-semibold text-gray-900 dark:text-gray-100 break-words text-sm md:text-base w-full">--}}
                                         <p class="text-xs md:text-sm text-gray-700 dark:text-gray-300 mt-1 font-bold">
                                        {{ $product->nombre_producto }}
                                    </p>
                                </div>

                                <!-- Flex fila en móvil, grid en desktop -->
                                <div class="flex flex-row items-center gap-3 md:grid md:grid-cols-12 md:gap-4">
                                    <!-- Imagen -->
                                    <div class="flex-shrink-0 flex items-center justify-center w-16 h-16 md:w-24 md:h-24 bg-gray-200 dark:bg-neutral-700 rounded-lg overflow-hidden md:col-span-3">
                                        @if ($product->imagen_producto)
                                            <img src="{{ asset('storage/' . $product->imagen_producto) }}"
                                                alt="{{ $product->nombre_producto }}"
                                                class="object-cover w-full h-full md:object-contain" />
                                        @else
                                            <span class="text-xs md:text-sm text-gray-500 dark:text-gray-400">Sin imagen</span>
                                        @endif
                                    </div>

                                    <!-- Info -->
                                    <div class="flex-1 md:col-span-6 ml-2 md:ml-0">
                                        <p class="text-[10px] md:text-xs text-gray-500 dark:text-gray-400 mt-1">SKU:
                                            {{ $product->codigo_producto }}</p>
                                        {{--<p class="text-[10px] md:text-xs text-gray-800 dark:text-gray-100 mt-1">DETAL:
                                            {{ number_format($product->valor_detal_producto * (($product->iva_producto / 100) + 1), 0) }}</p>--}}
                                        <p class="text-[10px] md:text-xs text-gray-800 dark:text-gray-100 mt-1">FERRETERO:
                                            {{ number_format((is_numeric($product->valor_ferretero_producto) ? (float)$product->valor_ferretero_producto : 0) * ((is_numeric($product->iva_producto) ? (float)$product->iva_producto : 0) / 100 + 1), 0) }}</p>
                                        <!-- Vamos a poner el Stock -->
                                        @php($availableStock = $this->getAvailableStock($product->id))
                                        <p class="text-[10px] md:text-xs mt-1 font-semibold
                                            {{ $availableStock > 10 ? 'text-green-600 dark:text-green-400' : ($availableStock > 0 ? 'text-orange-600 dark:text-orange-400' : 'text-red-600 dark:text-red-400') }}">Stock disponible:
                                            {{ $availableStock }}
                                        </p>
                                    </div>

                                    <!-- Botón -->
                                    <div class="flex items-center justify-center md:col-span-3 mt-0 w-auto">
                                        <div x-data="{ open: false, cantidad: 1 }" class="w-full">
                                            <x-filament::button @click="open = true; cantidad = 1"
                                                class="w-full py-2 px-4 font-bold rounded-lg text-xs md:text-base flex items-center justify-center"
                                                color="primary"
                                                size="md">
                                                <!-- Ícono "+" solo en móvil -->
                                                <svg class="w-5 h-5 md:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                </svg>
                                                <!-- Texto solo en escritorio -->
                                                <span class="hidden md:block">+ Agregar</span>
                                            </x-filament::button>

                                            <!-- Modal de cantidad -->
                                            @include('livewire.pos.pos-modal-agregar-producto')
                                        </div> <!-- x-data -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="col-span-full text-center text-gray-500 dark:text-gray-400 mt-8">No products found.
                        </p>
                    @endforelse
                </div>


            </div>
            <!-- Paginación Filament -->
                <div class="mt-6">
                    <x-filament::pagination :paginator="$products" :page-options="[5, 10, 20, 50, 'all']" current-page-option-property="perPage"
                        {{-- ← sin dos puntos --}} extreme-links />

                </div>
        </div>
    </div>
