<div x-data="{ open: false }" class="relative">
        <!-- Trigger / botón flotante -->
        <button @click="open = true"
            class="rounded-full flex items-center gap-2 px-4 py-2 fixed bottom-8 right-8 z-50 shadow-lg bg-info-600 text-white hover:bg-info-700 focus:outline-none focus:ring-2 focus:ring-info-500"
            style="min-width: 64px; min-height: 48px;">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 9m5-9v9m6-9v9m2-9l2 9" />
            </svg>
            <span class="ml-1 font-bold">({{ collect($this->cart)->sum(fn($item) => is_numeric($item['cantidad']) ? (int)$item['cantidad'] : 0) }})</span>
        </button>

        <!-- Modal -->
        <div x-show="open" x-transition class="fixed inset-0 z-50 flex items-start justify-end" style="display: none;">
            <!-- backdrop -->
            <div @click="open = false" class="absolute inset-0 bg-black/40"></div>

            <!-- panel slide-over -->
            <div
                class="relative bg-white dark:bg-neutral-800 rounded-l-2xl shadow-2xl w-full max-w-md h-full overflow-auto p-6 z-50">
                <!-- Close button arriba -->
                <div class="flex justify-end">
                    <button @click="open = false"
                        class="text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-white text-2xl p-1"
                        aria-label="Cerrar">&times;</button>
                </div>

                {{-- Modal content --}}
                <div class="space-y-6 max-w-full ">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4">
                        Productos agregados: {{ collect($this->cart)->sum(fn($item) => is_numeric($item['cantidad']) ? (int)$item['cantidad'] : 0) }}
                    </h2>
                    {{-- Productos en el carrito --}}
                    <div class="flex-grow pr-2 overflow-y-auto max-h-96">
                        @forelse($this->cart as $cartProduct)
                            <div
                                class="flex items-center justify-between p-2 mb-2 bg-gray-50 dark:bg-neutral-700 rounded-xl shadow-sm">
                                <div class="flex-1">
                                    <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                                        {{ $cartProduct['nombre_producto'] }}
                                    </h4>
                                    {{--<p class="text-xs text-gray-500 dark:text-gray-400">
                                        SKU: {{ $cartProduct['codigo_producto'] }}
                                    </p> --}}
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        COP: {{ number_format($this->getPrecioProducto($cartProduct), 0) }}

                                        <span class="font-bold">| TOTAL:</span>
                                        {{ number_format($this->getPrecioProducto($cartProduct) * (is_numeric($cartProduct['cantidad']) ? (int)$cartProduct['cantidad'] : 0), 0) }}
                                    </p>

                                </div>

                                <div class="flex items-center space-x-2">
                                    @php($maxStock = \App\Models\StockBodega::where('producto_id', $cartProduct['id'])->where('bodega_id', $this->bodega ?? 1)->first()->stock ?? 1)
                                    <input type="number" min="1" max="{{ (int) $maxStock }}"
                                        wire:key="cart-{{ $cartProduct['id'] }}"
                                        wire:model.live="cart.{{ $cartProduct['id'] }}.cantidad"
                                        @change="if(this.value > {{ (int) $maxStock }}) this.value = {{ (int) $maxStock }}; else if(this.value < 1) this.value = 1"
                                        class="py-2.5 sm:py-3 px-4 block w-20 border-gray-200 rounded-lg sm:text-sm
                                       focus:border-blue-500 focus:ring-blue-500
                                       dark:bg-neutral-900 dark:border-neutral-700
                                       dark:text-neutral-400 dark:placeholder-neutral-500
                                       dark:focus:ring-neutral-600">

                                    <button wire:click="removeFromCart({{ $cartProduct['id'] }})"
                                        class="p-2 text-red-500 hover:text-red-700 dark:hover:text-red-400">
                                        ✕
                                    </button>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 dark:text-gray-400">Tu carrito está vacío.</p>
                        @endforelse
                    </div>

                    <!-- Checkear carrito -->
                    <div class="flex-shrink-0 mt-6 space-y-4">
                        <div class="space-y-2">
                            <label for="cliente" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Cliente
                            </label>

                             @include('livewire.partials.searchable-client-select', [
                                'cliente_id' => $cliente_id,
                                'ciudad' => $ciudad,
                                'clientes' => $clientes,
                            ])
                        </div>

                        {{-- ...Metodo de Pago... --}}
                        <div class="mt-4">
                            <span class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Método de
                                Pago:</span>
                            <div class="flex space-x-2">
                                <button type="button" wire:click="$set('metodo_pago', 'CREDITO')"
                                    class="px-4 py-2 rounded-full text-sm font-semibold border
                        transition
                        {{ $metodo_pago === 'CREDITO'
                            ? 'bg-blue-600 text-white border-blue-600'
                            : 'bg-gray-200 dark:bg-neutral-700 text-gray-700 dark:text-gray-200 border-gray-300 dark:border-neutral-600 hover:bg-blue-100 dark:hover:bg-blue-900' }}">
                                    CRÉDITO
                                </button>
                                <button type="button" wire:click="$set('metodo_pago', 'CONTADO')"
                                    class="px-4 py-2 rounded-full text-sm font-semibold border
                        transition
                        {{ $metodo_pago === 'CONTADO'
                            ? 'bg-blue-600 text-white border-blue-600'
                            : 'bg-gray-200 dark:bg-neutral-700 text-gray-700 dark:text-gray-200 border-gray-300 dark:border-neutral-600 hover:bg-blue-100 dark:hover:bg-blue-900' }}">
                                    CONTADO
                                </button>
                            </div>
                        </div>

                        <div class="mt-4">
                            <span class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Estado de
                                Venta:</span>
                            <div class="flex space-x-2">
                                <button type="button" wire:click="$set('estado_venta', 'COTIZACION')"
                                    class="px-4 py-2 rounded-full text-sm font-semibold border transition
                    {{ $estado_venta === 'COTIZACION'
                        ? 'bg-blue-600 text-white border-blue-600'
                        : 'bg-gray-200 dark:bg-neutral-700 text-gray-700 dark:text-gray-200 border-gray-300 dark:border-neutral-600 hover:bg-blue-100 dark:hover:bg-blue-900' }}">
                                    COTIZACION
                                </button>
                                <button type="button" wire:click="$set('estado_venta', 'VENTA')"
                                    class="px-4 py-2 rounded-full text-sm font-semibold border transition
                    {{ $estado_venta === 'VENTA'
                        ? 'bg-blue-600 text-white border-blue-600'
                        : 'bg-gray-200 dark:bg-neutral-700 text-gray-700 dark:text-gray-200 border-gray-300 dark:border-neutral-600 hover:bg-blue-100 dark:hover:bg-blue-900' }}">
                                    VENTA
                                </button>
                            </div>
                        </div>

                        {{-- ...Tipo de Venta... --}}
                        <div class="mt-4">
                            <span class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tipo de
                                Venta:</span>
                            <div class="flex space-x-2">
                                <button type="button" wire:click="$set('tipo_venta', 'ELECTRONICA')"
                                    class="px-4 py-2 rounded-full text-sm font-semibold border transition
                    {{ $tipo_venta === 'ELECTRONICA'
                        ? 'bg-blue-600 text-white border-blue-600'
                        : 'bg-gray-200 dark:bg-neutral-700 text-gray-700 dark:text-gray-200 border-gray-300 dark:border-neutral-600 hover:bg-blue-100 dark:hover:bg-blue-900' }}">
                                    ELECTRÓNICA
                                </button>
                                <button type="button" wire:click="$set('tipo_venta', 'REMISIONADA')"
                                    class="px-4 py-2 rounded-full text-sm font-semibold border transition
                    {{ $tipo_venta === 'REMISIONADA'
                        ? 'bg-blue-600 text-white border-blue-600'
                        : 'bg-gray-200 dark:bg-neutral-700 text-gray-700 dark:text-gray-200 border-gray-300 dark:border-neutral-600 hover:bg-blue-100 dark:hover:bg-blue-900' }}">
                                    REMISIONADA
                                </button>
                            </div>
                        </div>

                        {{-- Comentarios --}}

                        <div class="mt-4">
                            <label for="primer_comentario"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Primer
                                comentario</label>
                            <textarea id="primer_comentario" wire:model="primer_comentario" rows="2"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-gray-100 mb-2"
                                placeholder="Escribe el primer comentario..."></textarea>
                        </div>
                        <div class="mt-2">
                            <label for="segundo_comentario"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Segundo
                                comentario</label>
                            <textarea id="segundo_comentario" wire:model="segundo_comentario" rows="2"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-gray-100"
                                placeholder="Escribe el segundo comentario..."></textarea>
                        </div>

                        {{--flete--}}
                        <div class="mt-4">
                            <label for="flete"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Flete (COP)</label>
                            <input type="number" id="flete" wire:model.blur="flete"
                                min="0" step="0.01"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-gray-100"
                                placeholder="Ingresa el valor del flete..." />
                        </div>
                         {{-- Total a pagar --}}
                        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-neutral-700">

                            <div class="flex justify-between items-center mb-2 text-lg font-bold">
                                <span>Total a Pagar:</span>
                                <span>COP {{ number_format(num: (is_numeric($this->subtotal()) ? (float)$this->subtotal() : 0) + (is_numeric($this->flete) ? (float)$this->flete : 0), decimals: 0) }}</span>
                            </div>

                        </div>
                    </div>

                    <div class="flex-shrink-0 mt-6">

                        <button wire:click="checkout" wire:loading.attr="disabled"
                            class="w-full py-4 bg-green-600 text-white font-bold text-lg rounded-lg
                       transition-colors duration-200 hover:bg-green-700
                       disabled:opacity-50 disabled:cursor-not-allowed shadow-lg mb-3">
                            Finalizar Venta
                        </button>

                        <!-- Botón para limpiar carrito -->
                        <button wire:click="clearSession"
                            class="w-full py-2 bg-red-500 text-white font-medium text-sm rounded-lg
                       transition-colors duration-200 hover:bg-red-600 shadow-md"
                            onclick="return confirm('¿Estás seguro de que quieres limpiar el carrito? Esta acción no se puede deshacer.')">
                            Limpiar Carrito
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
