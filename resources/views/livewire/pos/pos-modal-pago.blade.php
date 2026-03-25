<div x-show="mostrarModalPago" style="display: none;"
    class="fixed inset-0 flex items-center justify-center z-50 bg-black/40 dark:bg-black/60 backdrop-blur-sm">
    <div class="bg-white dark:bg-neutral-900 p-6 rounded shadow-lg w-full max-w-md mx-2">
        {{-- Puc --}}
        <div class="flex justify-between items-center gap-3">
            <x-select-dinamico label="Medio de Pago" placeholder="Seleccione un medio de pago..." model="pedido.puc_id"
                :options="$pucs" idKey="id" textKey="concatenar_subcuenta_concepto" selectId="select-puc" />
        </div>
        {{-- Descuento --}}
        <div class="flex justify-between items-center gap-3 mt-4">
            <label for="descuento"
                class="text-sm font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">Descuento (COP):</label>
            <input type="number" id="descuento" x-model="pedido.descuento" min="0" step="0.01"
                @input.debounce.300ms="pedido.total_a_pagar = getTotalAPagar(); pedido.saldo_pendiente = getSaldoPendiente(); guardarPedidoEnMemoria()"
                class="w-32 px-3 py-2 border border-gray-300 rounded-lg text-right focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-neutral-800 dark:border-neutral-700 dark:text-gray-100"
                placeholder="0" />
        </div>

        {{-- flete --}}
        <div class="flex justify-between items-center gap-3 mt-4">
            <label for="flete" class="text-sm font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">Flete
                (COP):</label>
            <input type="number" id="flete" x-model="pedido.flete" min="0" step="0.01"
                @input.debounce.300ms="pedido.total_a_pagar = getTotalAPagar(); pedido.saldo_pendiente = getSaldoPendiente(); guardarPedidoEnMemoria()"
                class="w-32 px-3 py-2 border border-gray-300 rounded-lg text-right focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-neutral-800 dark:border-neutral-700 dark:text-gray-100"
                placeholder="0" />
        </div>

        {{-- Total a pagar --}}
        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-neutral-700">
            <div class="flex justify-between items-center mb-2 text-lg font-bold">
                <span>Total a Pagar:</span>
                <span>COP:
                    <span
                        x-text="getTotalAPagar().toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 2 })">
                    </span>
                </span>
            </div>
        </div>

        {{-- Con cuanto paga --}}
        <div class="flex justify-between items-center gap-3 mt-4">
            <label for="con-cuanto-paga"
                class="text-sm font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">Con cuánto paga
                (COP):</label>
            <input type="number" id="con-cuanto-paga" x-model="conCuantoPaga" min="0" step="0.01"
                @input.debounce.300ms="pedido.saldo_pendiente = getSaldoPendiente(); guardarPedidoEnMemoria()"
                class="w-32 px-3 py-2 border border-gray-300 rounded-lg text-right focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-neutral-800 dark:border-neutral-700 dark:text-gray-100"
                placeholder="0" />
        </div>



        <div class="flex justify-end mt-6">
            <button @click="mostrarModalPago = false" type="button"
                class="px-4 py-2 rounded bg-gray-200 dark:bg-neutral-700 text-gray-800 dark:text-gray-100 hover:bg-gray-300 dark:hover:bg-neutral-600 transition">Cerrar</button>
        </div>
    </div>
</div>
