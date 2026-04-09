

<div class="w-full px-4 py-2" x-data="{ showAbonoModal: false }">
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 items-end mb-3">
        <div class="flex flex-col border-b border-gray-200/70 pb-2 dark:border-gray-700">
            <span class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-1">Subtotal</span>
            <span class="text-sm font-semibold text-gray-900 dark:text-gray-100"
                x-text="Number(getTotal(pedido)).toLocaleString('es-CO', { style: 'currency', currency: 'COP', minimumFractionDigits: 2 })"></span>
        </div>
        <div class="flex flex-col border-b border-gray-200/70 pb-2 dark:border-gray-700">
            <label class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-1">Flete</label>
            <input type="number" x-model.number="pedido.flete" class="input-table text-right bg-transparent border-0 border-b border-gray-300/60 rounded-none focus:ring-0 focus:border-gray-400 dark:border-gray-600 px-1" />
        </div>
        <div class="flex flex-col border-b border-gray-200/70 pb-2 dark:border-gray-700">
            <label class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-1">Descuento</label>
            <input type="number" x-model.number="pedido.descuento" class="input-table text-right bg-transparent border-0 border-b border-gray-300/60 rounded-none focus:ring-0 focus:border-gray-400 dark:border-gray-600 px-1" />
        </div>
        <div class="flex flex-col rounded-md border border-emerald-200 bg-emerald-50 px-3 py-2 dark:border-emerald-500/30 dark:bg-emerald-500/10">
            <span class="text-xs font-semibold uppercase tracking-wide text-emerald-700 dark:text-emerald-300 mb-1">Total</span>
            <span class="text-sm font-bold text-emerald-700 dark:text-emerald-200"
                x-text="Number(getTotalFinal(pedido)).toLocaleString('es-CO', { style: 'currency', currency: 'COP', minimumFractionDigits: 2 })"></span>
        </div>
        <div class="flex flex-col border-b border-gray-200/70 pb-2 dark:border-gray-700">
            <span class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-1">Abono</span>
            <span class="text-sm font-semibold text-green-600"
                x-text="Number(pedido.abono).toLocaleString('es-CO', { style: 'currency', currency: 'COP', minimumFractionDigits: 2 })"></span>
        </div>
        <div class="flex flex-col border-b border-gray-200/70 pb-2 dark:border-gray-700">
            <span class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-1">Saldo</span>
            <span class="text-sm font-semibold"
                :class="Number(getTotalFinal(pedido) - pedido.abono) > 0 ? 'text-red-600' : 'text-green-600'"
                x-text="Number(getTotalFinal(pedido) - pedido.abono).toLocaleString('es-CO', { style: 'currency', currency: 'COP', minimumFractionDigits: 2 })"></span>
        </div>
    </div>
    <div x-show="!soloLectura" class="flex justify-end w-full gap-2">
        <button @click="mostrarModalPago = true" type="button" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded shadow transition flex items-center justify-center">
            <span>Registrar Pago</span>
        </button>


        <button @click="enviar()" type="button" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow transition flex items-center justify-center" :disabled="isLoading">
            <template x-if="isLoading">
                <svg class="animate-spin h-5 w-5 mr-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
            </template>
            <span x-text="isLoading ? 'Guardando...' : 'Guardar Pedido'"></span>
        </button>
    </div>
</div>
