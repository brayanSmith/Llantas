<div
    class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:shadow-none">
    <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4 dark:border-gray-800">
        <div class="flex items-center gap-3">
            <div
                class="h-9 w-9 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center dark:bg-indigo-500/10 dark:text-indigo-300">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 6h16" />
                    <path d="M4 12h16" />
                    <path d="M4 18h10" />
                </svg>
            </div>
            <div>
                <h4 class="text-base font-semibold text-gray-900 dark:text-gray-100">Detalle de la Compra</h4>
                <p class="text-xs text-gray-500 dark:text-gray-400">Productos y gastos registrados</p>
            </div>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 rounded-lg overflow-hidden">
            <thead class="bg-gray-100 dark:bg-gray-800">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 w-[20%]">
                        Producto
                    </th>
                    <!-- Columnas dinámicas por bodega -->
                    <template x-for="bodega in bodegas" :key="bodega.id">
                        <th class="px-4 py-2 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 w-[8%]">
                            <span x-text="bodega.nombre_bodega"></span>
                        </th>
                    </template>
                    <th class="px-4 py-2 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 w-[12%]">
                        Precio
                        Unitario</th>
                    <th class="px-4 py-2 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 w-[12%]">
                        Subtotal
                    </th>
                    <th class="px-4 py-2 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 w-[11%]">
                        Acciones
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-900">
                <template x-for="(grupo, grupoIndex) in detallesAgrupadosPorProducto" :key="grupoIndex">
                    <tr
                        class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                        <td class="px-4">
                            <input type="text" readonly class="input-table w-full text-left"
                                :value="productos.find(p => p.id == grupo.producto_id)?.concatenar_codigo_nombre ||
                                    'Producto no encontrado'" />
                        </td>
                        <!-- Columnas dinámicas de cantidad por bodega -->
                        <template x-for="bodega in bodegas" :key="bodega.id">
                            <td>
                                <input type="number" min="0" readonly
                                    class="input-table w-20 text-center"
                                    :value="grupo.cantidadesPorBodega[bodega.id] || 0" />
                            </td>
                        </template>
                        <td>
                            <input type="text" readonly class="input-table w-24 text-center"
                                :value="Number(grupo.precio_unitario).toLocaleString('es-CO', {
                                    style: 'currency',
                                    currency: 'COP',
                                    minimumFractionDigits: 2
                                })" />
                        </td>
                        <td>
                            <input type="text" readonly class="input-table w-24 text-center font-semibold"
                                :value="Number(grupo.subtotal).toLocaleString('es-CO', {
                                    style: 'currency',
                                    currency: 'COP',
                                    minimumFractionDigits: 2
                                })" />
                        </td>
                        <td class="px-4 py-2">
                            <div class="flex items-center justify-center gap-2">
                                <button type="button" @click="traerDetalle(grupo.indices[0])"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded shadow transition text-sm">Editar
                                </button>
                                <button type="button"
                                    @click="grupo.indices.sort((a, b) => b - a).forEach(i => removeDetalle(i))"
                                    class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded shadow transition text-sm">Eliminar
                                </button>
                            </div>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>

        <div class="flex items-center justify-center px-6 py-3 border-t border-gray-100 dark:border-gray-800">
            <div class="text-xs font-semibold text-white bg-green-600 px-3 py-1 rounded-full">
                Items agregados: <span x-text="compra.detalles_compra ? compra.detalles_compra.length : 0"></span>
            </div>
        </div>
    </div>
</div>
