<div>
    <!-- Encabezado -->
    <div class="mb-6">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2">
            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Abonos Registrados
        </h3>
    </div>

    <!-- Tabla de Abonos -->
    <template x-if="compra.abono_compra && compra.abono_compra.length > 0">
        <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
            <table class="w-full">
                <thead>
                    <tr class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-900/50 dark:to-gray-800/50 border-b border-gray-200 dark:border-gray-700">
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Comprobante</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Método de Pago</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Observaciones</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Monto</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Fecha</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800/50">
                    <template x-for="(abono, index) in compra.abono_compra" :key="index">
                        <tr class="border-b border-gray-100 dark:border-gray-700/50 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            <!-- Comprobante -->
                            <td class="px-6 py-4">
                                <template x-if="abono.imagen_abono_compra">
                                    <div class="flex justify-center">
                                        <a :href="abono.imagen_abono_compra.startsWith('http') ? abono.imagen_abono_compra : '/storage/' + abono.imagen_abono_compra" target="_blank" class="group relative inline-block">
                                            <img
                                                :src="abono.imagen_abono_compra.startsWith('http') ? abono.imagen_abono_compra : '/storage/' + abono.imagen_abono_compra"
                                                alt="Comprobante"
                                                class="w-12 h-12 object-cover rounded border border-gray-200 dark:border-gray-600 hover:opacity-90 transition-opacity" />
                                            <div class="absolute inset-0 flex items-center justify-center rounded opacity-0 group-hover:opacity-100 transition-opacity bg-black/30">
                                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                                </svg>
                                            </div>
                                        </a>
                                    </div>
                                </template>
                                <template x-if="!abono.imagen_abono_compra">
                                    <span class="text-xs text-gray-400 italic">Sin comprobante</span>
                                </template>
                            </td>

                            <!-- Método de Pago -->
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h10m4 0a1 1 0 11-2 0 1 1 0 012 0zM7 15a1 1 0 11-2 0 1 1 0 012 0z"></path>
                                    </svg>
                                    <span class="text-sm text-gray-700 dark:text-gray-300" x-text="abono.forma_pago_abono_compra?.concatenar_subcuenta_concepto || 'Sin especificar'"></span>
                                </div>
                            </td>

                            <!-- Observaciones -->
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-600 dark:text-gray-400" x-text="abono.descripcion_abono_compra || '-'"></span>
                            </td>

                            <!-- Monto -->
                            <td class="px-6 py-4">
                                <span class="text-sm font-bold text-green-600 dark:text-green-400 text-right block" x-text="Number(abono.monto_abono_compra).toLocaleString('es-CO', { style: 'currency', currency: 'COP', minimumFractionDigits: 2 })"></span>
                            </td>

                            <!-- Fecha -->
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-700 dark:text-gray-300 font-medium" x-text="new Date(abono.fecha_abono_compra).toLocaleDateString('es-CO', { year: 'numeric', month: 'short', day: 'numeric' })"></span>
                            </td>
                              <!-- Acciones -->
                            <td class="px-6 py-4 text-center">
                                <div class="flex gap-2 justify-center">
                                    <button type="button" @click="selectAbonoCompra(abono)" class="text-blue-600 hover:text-blue-800 dark:hover:text-blue-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <button type="button" @click.prevent="removeAbono(index)" class="text-red-600 hover:text-red-800">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </template>

    <!-- Sin abonos -->
    <template x-if="!compra.abono_compra || compra.abono_compra.length === 0">
        <div class="text-center py-12 bg-gray-50 dark:bg-gray-800/50 rounded-lg border border-gray-200 dark:border-gray-700">
            <svg class="w-16 h-16 text-gray-300 dark:text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
            </svg>
            <p class="text-gray-500 dark:text-gray-400 text-lg font-medium">No hay abonos registrados</p>
            <p class="text-gray-400 dark:text-gray-500 text-sm">Registra un nuevo abono para que aparezca aquí</p>
        </div>
    </template>
</div>
