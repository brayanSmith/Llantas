<div class="bg-white dark:bg-gray-800 rounded overflow-hidden shadow-sm">
    <!-- Encabezado de la Tabla -->
    <div class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 px-4 py-2">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 flex items-center">
            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Compras en Cartera
        </h3>
    </div>

    <!-- Tabla -->
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                <tr>
                    <th class="px-3 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Factura</th>
                    <th class="px-3 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Item Compra</th>
                    <th class="px-3 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Categoría</th>
                    <th class="px-3 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Total a Pagar</th>
                    <th class="px-3 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Saldo</th>
                    <th class="px-3 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Seleccionar</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="(compra, index) in comprasEnCartera" :key="compra.id">
                    <tr class="hover:bg-blue-50 dark:hover:bg-gray-700 transition-colors duration-150"
                        :class="index % 2 === 0 ? 'bg-white dark:bg-gray-800' : 'bg-gray-50 dark:bg-gray-900'">
                        <!-- Factura -->
                        <td class="px-3 py-3 text-sm text-gray-900 dark:text-gray-100 font-medium">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200"
                                  x-text="compra.factura"></span>
                        </td>

                        <!-- Item Compra -->
                        <td class="px-3 py-3 text-sm text-gray-900 dark:text-gray-100" x-text="compra.item_compra"></td>

                        <!-- Categoría -->
                        <td class="px-3 py-3 text-sm text-gray-900 dark:text-gray-100" x-text="compra.categoria_compra"></td>

                        <!-- Total a Pagar -->
                        <td class="px-3 py-3 text-sm text-right font-semibold text-gray-900 dark:text-gray-100">
                            <span x-text="parseFloat(compra.total_a_pagar).toLocaleString('es-CO', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span>
                        </td>

                        <!-- Saldo Pendiente -->
                        <td class="px-3 py-3 text-sm text-right font-semibold"
                            :class="compra.saldo_pendiente > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-500 dark:text-gray-400'">
                            <span x-text="parseFloat(compra.saldo_pendiente).toLocaleString('es-CO', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span>
                        </td>

                        <!-- Checkbox -->
                        <td class="px-3 py-3 text-center">
                            <input type="checkbox"
                                   :checked="comprasSeleccionadas.some(c => c.id === compra.id)"
                                   @change="toggleCompra(compra)"
                                   class="w-4 h-4 text-green-600 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded focus:ring-2 focus:ring-green-500 cursor-pointer transition-all duration-150 checked:bg-green-500 dark:checked:bg-green-600 checked:border-green-600 dark:checked:border-green-500">
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>

        <!-- Mensaje cuando no hay compras -->
        <template x-if="comprasEnCartera.length === 0">
            <div class="w-full px-4 py-8 text-center bg-white dark:bg-gray-800">
                <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                </svg>
                <p class="text-gray-500 dark:text-gray-400 text-sm">Seleccione un proveedor para ver las compras en cartera</p>
            </div>
        </template>
    </div>

    <!-- Pie de Tabla (Resumen) -->
    <template x-if="comprasEnCartera.length > 0">
        <div class="bg-gray-50 dark:bg-gray-900 px-4 py-2">
            <div class="flex items-center justify-between text-sm">
                <span class="text-gray-600 dark:text-gray-400">
                    Total de compras: <span class="font-semibold text-gray-900 dark:text-white" x-text="comprasEnCartera.length"></span>
                </span>
                <span class="text-gray-600 dark:text-gray-400">
                    Seleccionadas: <span class="font-semibold text-blue-600 dark:text-blue-400" x-text="cantidadComprasSeleccionadas"></span>
                </span>
            </div>
        </div>
    </template>
</div>
