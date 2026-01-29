<div class="flex-grow overflow-y-auto pr-1">
   {{-- @php($products = $this->filteredProducts)  paginator --}}
    <div class="grid grid-cols-1 gap-2 md:gap-6">
        {{-- @forelse($products as $product) --}}
        <template x-for="product in productosFiltradosPaginados" :key="product.id">
            <div class="bg-grey-100 dark:bg-neutral-800 rounded-2xl shadow-lg">
                <div
                    class="bg-white dark:bg-neutral-800 rounded-2xl shadow-lg overflow-hidden transition-all duration-200 transform hover:scale-105 hover:shadow-xl p-2 md:p-4">

                    <!-- Encabezado: Nombre del producto -->
                    <div class="w-full mb-2">
                        {{-- <p class="font-semibold text-gray-900 dark:text-gray-100 break-words text-sm md:text-base w-full"> --}}
                        <p x-text="product.nombre_producto" class="text-xs md:text-sm text-gray-700 dark:text-gray-300 mt-1 font-bold">
                            {{-- $product->nombre_producto --}}
                        </p>
                    </div>

                    <!-- Flex fila en móvil, grid en desktop -->
                    <div class="flex flex-row items-center gap-3 md:grid md:grid-cols-12 md:gap-4">
                        <!-- Imagen -->
                        <div
                            class="flex-shrink-0 flex items-center justify-center w-16 h-16 md:w-24 md:h-24 bg-gray-200 dark:bg-neutral-700 rounded-lg overflow-hidden md:col-span-3">
                            <template x-if="product.imagen_producto">
                                <img :src="`/storage/${product.imagen_producto}`"
                                    :alt="product.nombre_producto"
                                    class="object-cover w-full h-full md:object-contain" />
                            </template>
                            <template x-if="!product.imagen_producto">
                                <span class="text-xs md:text-sm text-gray-500 dark:text-gray-400">Sin imagen</span>
                            </template>
                        </div>

                        <!-- Info -->
                        <div class="flex-1 md:col-span-6 ml-2 md:ml-0">
                            <p class="text-[10px] md:text-xs text-gray-500 dark:text-gray-400 mt-1">SKU:
                                <strong x-text="product.codigo_producto"></strong></p>
                            <p class="text-[10px] md:text-xs text-gray-800 dark:text-gray-100 mt-1">FERRETERO:
                                <strong x-text="new Intl.NumberFormat().format((isNaN(product.valor_ferretero_producto) ? 0 : parseFloat(product.valor_ferretero_producto)))"></strong>
                            </p>
                            <p class="text-[10px] md:text-xs text-gray-800 dark:text-gray-100 mt-1">FERRETERO + IVA:
                                <strong x-text="new Intl.NumberFormat().format((isNaN(product.valor_ferretero_producto) ? 0 : parseFloat(product.valor_ferretero_producto)) * ((isNaN(product.iva_producto) ? 0 : parseFloat(product.iva_producto)) / 100 + 1))"></strong>
                            </p>

                            <!-- Vamos a poner el Stock -->
                            <p class="text-[10px] md:text-xs text-gray-800 dark:text-gray-100 mt-1">STOCK:
                                <strong x-text="getStockDisponible(product.id)"></strong></p>

                        </div>

                        <!-- Botón -->
                        <div class="flex items-center justify-center md:col-span-3 mt-0 w-auto">
                            <div x-data="{ open: false, cantidad: 1 }" class="w-full">
                                <x-filament::button
                                    @click.prevent="productoSeleccionado = product; cantidadSeleccionada = 1; open = true"
                                    class="w-full py-2 px-4 font-bold rounded-lg text-xs md:text-base flex items-center justify-center"
                                    color="primary" size="md">
                                    <!-- Ícono "+" solo en móvil -->
                                    <svg class="w-5 h-5 md:hidden" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4" />
                                    </svg>
                                    <!-- Texto solo en escritorio -->
                                    <span class="hidden md:block">+ Agregar</span>
                                </x-filament::button>

                                <!-- Modal de cantidad, solo uno fuera del x-for -->
                                @include('livewire.pos.pos-modal-agregar-producto')
                            </div> <!-- x-data -->
                        </div>
                    </div>
                </div>
            </div>
        {{-- @empty
            <p class="col-span-full text-center text-gray-500 dark:text-gray-400 mt-8">No products found.
            </p>
        @endforelse --}}
        </template>
    </div>


</div>
