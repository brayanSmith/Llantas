<div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:shadow-none">
    <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4 dark:border-gray-800">
        <div class="flex items-center gap-3">
            <div class="h-9 w-9 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center dark:bg-indigo-500/10 dark:text-indigo-300">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 6h16" />
                    <path d="M4 12h16" />
                    <path d="M4 18h10" />
                </svg>
            </div>
            <div>
                <h4 class="text-base font-semibold text-gray-900 dark:text-gray-100">Detalle del Pedido</h4>
                <p class="text-xs text-gray-500 dark:text-gray-400">Productos agregados al pedido</p>
            </div>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 rounded-lg overflow-hidden">
            <thead class="bg-gray-100 dark:bg-gray-800">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 w-[40%]">
                        Producto
                    </th>
                    <th class="px-4 py-2 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 w-[10%]">
                        Cantidad
                    </th>
                    <th class="px-4 py-2 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 w-[15%]">Precio
                        Unitario</th>
                    <th class="px-4 py-2 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 w-[15%]">
                        Subtotal
                    </th>
                    <th class="px-4 py-2 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 w-[20%]">
                        Acciones
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-900">
                <template x-for="(detalle, index) in pedido.detalles" :key="index">
                    <tr
                        class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                        <td class="px-4">
                            <div>
                                <input type="text" readonly class="input-table w-full text-left"
                                    :value="productos.find(p => p.id == detalle.producto_id)?.concatenar_codigo_nombre || 'Producto no encontrado'" />
                            </div>
                        </td>
                        <td>
                            <input type="number" min="1" readonly x-model.number="detalle.cantidad"
                                class="input-table w-20 text-center" />
                        </td>
                        <td>
                            <input type="text" readonly class="input-table w-24 text-center"
                                :value="Number(detalle.precio_unitario).toLocaleString('es-CO', {
                                    style: 'currency',
                                    currency: 'COP',
                                    minimumFractionDigits: 2
                                })" />
                        </td>
                        <td>
                            <input type="text" readonly class="input-table w-24 text-center font-semibold"
                                :value="Number(detalle.subtotal).toLocaleString('es-CO', {
                                    style: 'currency',
                                    currency: 'COP',
                                    minimumFractionDigits: 2
                                })" />
                        </td>
                        <td class="px-4 py-2">
                            <div class="flex items-center justify-center gap-2">
                                <button type="button" @click="traerDetalle(index)"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded shadow transition text-sm">Editar
                                </button>
                                <button type="button" @click="removeDetalle(index)"
                                    class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded shadow transition text-sm">Eliminar
                                </button>
                            </div>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
    <div class="flex items-center justify-center px-6 py-3 border-t border-gray-100 dark:border-gray-800">
        <div class="text-xs font-semibold text-white bg-green-600 px-3 py-1 rounded-full">
            Productos: <span x-text="pedido.detalles ? pedido.detalles.length : 0"></span>
        </div>
    </div>
</div>
