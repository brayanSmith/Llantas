

<div class="flex flex-col items-end space-y-2 w-full pr-2">
    <div class="flex items-center gap-2">
        <label class="text-sm font-medium w-24 text-right">Subtotal:</label>
        <input type="text" :value="Number(getTotal(compra)).toLocaleString('es-CO', { style: 'currency', currency: 'COP', minimumFractionDigits: 2 })" class="input-table w-32 text-right font-semibold bg-white dark:bg-gray-900" readonly />
    </div>
    <div class="flex items-center gap-2">
        <label class="text-sm font-medium w-24 text-right">Descuento:</label>
        <input type="number" x-model.number="compra.descuento" class="input-table w-32 text-right bg-white dark:bg-gray-900" />
    </div>
    <hr class="w-56 border-t border-gray-300 dark:border-gray-700 my-2">
    <div class="flex items-center gap-2">
        <label class="text-sm font-bold w-24 text-right">Total:</label>
        <input type="text" :value="Number(getTotalFinal(compra)).toLocaleString('es-CO', { style: 'currency', currency: 'COP', minimumFractionDigits: 2 })" class="input-table w-32 text-right font-bold bg-white dark:bg-gray-900" readonly />
    </div>
    <div class="flex items-center gap-2">
        <label class="text-sm font-medium w-24 text-right">Abono:</label>
        <input type="text" :value="Number(compra.abono).toLocaleString('es-CO', { style: 'currency', currency: 'COP', minimumFractionDigits: 2 })" class="input-table w-32 text-right bg-white dark:bg-gray-900 text-green-600" readonly />
    </div>
    <div class="flex items-center gap-2">
        <label class="text-sm font-medium w-24 text-right">Saldo:</label>
        <input type="text" :value="Number(getTotalFinal(compra) - compra.abono).toLocaleString('es-CO', { style: 'currency', currency: 'COP', minimumFractionDigits: 2 })" class="input-table w-32 text-right bg-white dark:bg-gray-900" :class="Number(getTotalFinal(compra) - compra.abono) > 0 ? 'text-red-600' : 'text-green-600'" readonly />
    </div>
    <div class="flex justify-end w-full pt-2">
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
