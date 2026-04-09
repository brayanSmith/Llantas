<div x-show="mostrarModalPago" style="display: none;"
    class="fixed inset-0 flex items-center justify-center z-50 bg-black/40 dark:bg-black/60 backdrop-blur-sm">
    <div class="bg-white dark:bg-neutral-900 p-6 rounded shadow-lg w-full max-w-md mx-2">

        {{-- Puc --}}
        <div class="flex justify-between items-center gap-3">
            <x-select-dinamico label="Medio de Pago" placeholder="Seleccione un medio de pago..." model="pedido.id_puc"
                x-model="pedido.id_puc" :options="$pucs" idKey="id" textKey="concatenar_subcuenta_concepto"
                selectId="select-puc" />
        </div>

        {{-- Descuento --}}
        <div class="flex justify-between items-center gap-3 mt-4">
            <label for="descuento"
                class="text-sm font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">Descuento (COP):</label>
            <input type="number" id="descuento" x-model="pedido.descuento" min="0" step="0.01"
                class="w-32 px-3 py-2 border border-gray-300 rounded-lg text-right focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-neutral-800 dark:border-neutral-700 dark:text-gray-100"
                placeholder="0" />
        </div>

        {{-- flete --}}
        <div class="flex justify-between items-center gap-3 mt-4">
            <label for="flete" class="text-sm font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">Flete
                (COP):</label>
            <input type="number" id="flete" x-model="pedido.flete" min="0" step="0.01"
                class="w-32 px-3 py-2 border border-gray-300 rounded-lg text-right focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-neutral-800 dark:border-neutral-700 dark:text-gray-100"
                placeholder="0" />
        </div>

        {{-- Total a pagar --}}
        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-neutral-700">
            <div class="flex justify-between items-center mb-2 text-lg font-bold">
                <span>Total a Pagar:</span>
                <span>COP:
                    <span
                        x-text="Number(pedido.total_a_pagar).toLocaleString('es-CO', { style: 'currency', currency: 'COP', minimumFractionDigits: 2 })">
                    </span>
                </span>
            </div>
        </div>
            {{-- Con cuanto paga --}}
            <div class="flex justify-between items-center gap-3 mt-4">
                <label for="con-cuanto-paga"
                    class="text-sm font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">Con cuánto paga
                    (COP):</label>
                <input type="number" id="con-cuanto-paga" x-model.number="pedido.con_cuanto_paga" min="0"
                    step="0.01"
                    class="w-32 px-3 py-2 border border-gray-300 rounded-lg text-right focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-neutral-800 dark:border-neutral-700 dark:text-gray-100"
                    placeholder="0" />
            </div>

            {{-- Cambio --}}
            <div class="mt-4 pt-2">
                <div class="flex justify-between items-center mb-2 text-lg font-bold">
                    <span class="flex items-center gap-2">
                        Cambio:
                    </span>
                    <span class="text-blue-600 font-bold flex items-center gap-1">
                        COP:
                        <span
                            x-text="pedido.cambio !== undefined ? pedido.cambio.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) : '0.00'">
                        </span>
                    </span>
                </div>
            </div>


        {{-- Comentario Pago --}}
        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-neutral-700">
            <div class="mt-2">
                <label for="observacion_pago"
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Comentario Pago</label>
                <textarea id="observacion_pago" x-model="pedido.observacion_pago" rows="2"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-gray-100"
                    placeholder="Escribe un comentario sobre el pago..."></textarea>
            </div>
        </div>

        <div class="flex gap-3 mt-6">
            <button @click="enviar()" wire:loading.attr="disabled"
                class="flex-1 py-3 bg-green-600 text-white font-bold text-base rounded-lg shadow-lg
                       transition-colors duration-200 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-400
                       disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                <svg x-show="isLoading" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 24 24" style="display: none;"
                    :style="isLoading ? 'display:inline-block' : 'display:none'">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
                <span x-show="!isLoading" :style="isLoading ? 'display:none' : 'display:inline'">Finalizar Venta</span>
                <span x-show="isLoading" :style="isLoading ? 'display:inline' : 'display:none'">Procesando...</span>
            </button>
            <button @click="mostrarModalPago = false" type="button"
                class="flex-1 py-3 rounded-lg bg-gray-100 dark:bg-neutral-800 text-gray-700 dark:text-gray-200 font-semibold border border-gray-300 dark:border-neutral-700
                       hover:bg-gray-200 dark:hover:bg-neutral-700 transition-colors duration-200 shadow focus:outline-none focus:ring-2 focus:ring-gray-400">
                Cerrar
            </button>
        </div>
    </div>
</div>
