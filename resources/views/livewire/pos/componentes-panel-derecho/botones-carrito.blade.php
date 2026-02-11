<div class="flex-shrink-0 mt-6">

    {{-- flete --}}
        <div class="flex justify-between items-center gap-3">
            <label for="flete" class="text-sm font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">Flete (COP):</label>
            <input type="number" id="flete" x-model="pedido.flete" min="0" step="0.01"
                @input.debounce.300ms="pedido.total_a_pagar = getTotalAPagar(); pedido.saldo_pendiente = getSaldoPendiente(); guardarPedidoEnMemoria()"
                class="w-32 px-3 py-2 border border-gray-300 rounded-lg text-right focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-gray-100"
                placeholder="0" />
        </div>


    {{-- Total a pagar --}}
    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-neutral-700">
        <div class="flex justify-between items-center mb-2 text-lg font-bold">
            <span>Total a Pagar:</span>
            <span>COP:
                <span x-text="getTotalAPagar().toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 2 })">
                </span>
            </span>
        </div>
    </div>

    <button @click="enviar()" wire:loading.attr="disabled"
        class="w-full py-4 bg-green-600 text-white font-bold text-lg rounded-lg
                       transition-colors duration-200 hover:bg-green-700
                       disabled:opacity-50 disabled:cursor-not-allowed shadow-lg mb-3">
        Finalizar Venta
    </button>

    <!-- Botón para limpiar carrito -->
    <button @click.prevent="resetPedido()"
        class="w-full py-2 bg-red-500 text-white font-medium text-sm rounded-lg
                       transition-colors duration-200 hover:bg-red-600 shadow-md"
        onclick="return confirm('¿Estás seguro de que quieres limpiar el carrito? Esta acción no se puede deshacer.')">
        Limpiar Carrito
    </button>
</div>
