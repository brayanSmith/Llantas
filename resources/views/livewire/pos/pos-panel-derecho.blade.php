<div x-data="{ open: false }" class="relative">
    <!-- Trigger / botón flotante -->
    <button @click.prevent="open = true"
        class="rounded-full flex items-center gap-2 px-4 py-2 fixed bottom-8 right-8 z-50 shadow-lg bg-info-600 text-white hover:bg-info-700 focus:outline-none focus:ring-2 focus:ring-info-500"
        style="min-width: 64px; min-height: 48px;">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 9m5-9v9m6-9v9m2-9l2 9" />
        </svg>
        <span x-text="totalCantidadProductos"></span>
    </button>

    <!-- Modal -->
    <div x-show="open" x-transition class="fixed inset-0 z-50 flex items-start justify-end" style="display: none;">
        <!-- backdrop -->
        <div @click.prevent="open = false"
            class="absolute inset-0 bg-white/40 dark:bg-neutral-900/40 backdrop-blur-sm transition-all">
        </div>

        <!-- panel slide-over -->
        <div
            class="relative bg-white dark:bg-neutral-800 rounded-l-2xl shadow-2xl w-full max-w-md h-full overflow-auto p-6 z-50">
            <!-- Close button arriba -->
            <div class="flex justify-end">
                <button @click.prevent="open = false"
                    class="text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-white text-2xl p-1"
                    aria-label="Cerrar">&times;</button>
            </div>

            {{-- Modal content --}}
            <div class="space-y-6 max-w-full ">
                <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4">
                    Productos agregados:
                </h2>
                {{-- Productos en el carrito --}}
                @include('livewire.pos.componentes-panel-derecho.productos-carrito')
                {{-- Checkear carrito--}}
                @include('livewire.pos.componentes-panel-derecho.formulario-carrito')
                {{-- Botones de acción --}}
                @include('livewire.pos.componentes-panel-derecho.botones-carrito')
            </div>
        </div>
    </div>
</div>
