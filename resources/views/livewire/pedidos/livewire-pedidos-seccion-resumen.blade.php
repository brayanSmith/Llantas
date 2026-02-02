

<div class="flex flex-col items-end space-y-2 w-full pr-2">
    <div class="flex items-center gap-2">
        <label class="text-sm font-medium w-24 text-right">Subtotal:</label>
        <input type="text" :value="Number(getTotal(pedido)).toLocaleString('es-CO', { style: 'currency', currency: 'COP', minimumFractionDigits: 2 })" class="input-table w-32 text-right font-semibold bg-white dark:bg-gray-900" readonly />
    </div>
    <div class="flex items-center gap-2">
        <label class="text-sm font-medium w-24 text-right">Flete:</label>
        <input type="number" x-model.number="pedido.flete" class="input-table w-32 text-right bg-white dark:bg-gray-900" />
    </div>
    <div class="flex items-center gap-2">
        <label class="text-sm font-medium w-24 text-right">Descuento:</label>
        <input type="number" x-model.number="pedido.descuento" class="input-table w-32 text-right bg-white dark:bg-gray-900" />
    </div>
    <hr class="w-56 border-t border-gray-300 dark:border-gray-700 my-2">
    <div class="flex items-center gap-2">
        <label class="text-sm font-bold w-24 text-right">Total:</label>
        <input type="text" :value="Number(getTotalFinal(pedido)).toLocaleString('es-CO', { style: 'currency', currency: 'COP', minimumFractionDigits: 2 })" class="input-table w-32 text-right font-bold bg-white dark:bg-gray-900" readonly />
    </div>
    <div class="flex justify-end w-full pt-2">
        <button @click="enviar()" type="button" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow transition">Guardar Pedido</button>
    </div>
</div>
