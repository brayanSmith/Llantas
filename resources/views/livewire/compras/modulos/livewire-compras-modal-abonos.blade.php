<!-- Modal para editar detalles del abono compra -->
<template x-if="abonoCompraSeleccionado">
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 dark:bg-black/70">
            <div class="bg-white dark:bg-gray-900 rounded-lg shadow-lg max-w-md w-full mx-auto p-6 space-y-4">
                <!-- Encabezado -->
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100">Editar Abono</h3>
                    <button type="button" @click="abonoCompraSeleccionado = null" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Contenido -->
                <div class="space-y-4">
                    <!-- Monto -->
                    <div>
                        <label class="text-xs font-semibold text-gray-600 dark:text-gray-400 mb-2 block">Monto</label>
                        <input type="number"
                               x-model.number="abonoCompraSeleccionado.monto_abono_compra"
                               step="0.01"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded dark:bg-gray-800 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    </div>

                    <!-- Fecha -->
                    <div>
                        <label class="text-xs font-semibold text-gray-600 dark:text-gray-400 mb-2 block">Fecha</label>
                        <input type="date"
                               x-model="abonoCompraSeleccionado.fecha_abono_compra"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded dark:bg-gray-800 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    </div>

                    <!-- Método de Pago -->
                    <div>
                        <label class="text-xs font-semibold text-gray-600 dark:text-gray-400 mb-2 block">Método de Pago</label>
                        <p class="text-sm text-gray-700 dark:text-gray-300 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded bg-gray-50 dark:bg-gray-800" x-text="abonoCompraSeleccionado.forma_pago_abono_compra?.concatenar_subcuenta_concepto || 'Sin especificar'"></p>
                    </div>

                    <!-- Observaciones -->
                    <div>
                        <label class="text-xs font-semibold text-gray-600 dark:text-gray-400 mb-2 block">Observaciones</label>
                        <textarea
                               x-model="abonoCompraSeleccionado.descripcion_abono_compra"
                               rows="3"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded dark:bg-gray-800 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>

                    <!-- Comprobante -->
                    <template x-if="abonoCompraSeleccionado.imagen_abono_compra">
                        <div class="border border-gray-200 dark:border-gray-700 rounded p-3">
                            <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 mb-2">Comprobante</p>
                            <img :src="abonoCompraSeleccionado.imagen_abono_compra.startsWith('http') ? abonoCompraSeleccionado.imagen_abono_compra : '/storage/' + abonoCompraSeleccionado.imagen_abono_compra"
                                 alt="Comprobante"
                                 class="w-full h-auto rounded" />
                        </div>
                    </template>
                </div>

                <!-- Botones -->
                <div class="flex gap-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" @click="abonoCompraSeleccionado = null" class="flex-1 px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded font-medium hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                        Cancelar
                    </button>
                    <button type="button" @click="guardarAbonoCompra(abonoCompraSeleccionado)" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded font-medium hover:bg-blue-700 transition">
                        Guardar
                    </button>
                </div>
            </div>
        </div>
    </template>
