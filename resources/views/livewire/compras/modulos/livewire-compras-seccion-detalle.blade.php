<div>
    <h4 class="text-md font-bold mb-4 text-center">Detalle del Pedido</h4>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 rounded-lg overflow-hidden">
            <thead class="bg-gray-100 dark:bg-gray-800">
                <tr>
                    <th class="px-4 py-2 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 w-[40%]">
                        Producto
                    </th>
                    <th x-show="compra.item_compra === 'GASTO'"
                        class="px-4 py-2 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 w-[25%]">
                        Descripción
                    </th>
                    <th class="px-4 py-2 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 w-[10%]">
                        Cantidad
                    </th>
                    <th class="px-4 py-2 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 w-[20%]">
                        Precio
                        Unitario</th>
                    <th class="px-4 py-2 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 w-[20%]">
                        Subtotal
                    </th>
                    <th class="px-4 py-2"></th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-900">
                <template x-for="(detalle, index) in compra.detalles_compra" :key="index">
                    <tr
                        class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                        <td class="px-4">
                            <div x-show="compra.item_compra === 'PRODUCTO'">
                                <input type="text" readonly class="input-table w-full"
                                    :value="productos.find(p => p.id == detalle.producto_id)?.concatenar_codigo_nombre ||
                                        'Producto no encontrado'" />
                            </div>
                            <div x-show="compra.item_compra === 'GASTO'">
                                <input type="text" readonly class="input-table w-full"
                                    :value="pucs.find(p => p.id == detalle.producto_id)?.concatenar_subcuenta_concepto ||
                                        'Producto no encontrado'" />
                            </div>
                        </td>
                        <td x-show="compra.item_compra === 'GASTO'">
                            <div>
                                <input type="text" class="input-table w-20 text-center" readonly
                                    x-model="detalle.descripcion_item" />
                            </div>
                        </td>
                        <td>
                            <input type="number" min="1" readonly x-model.number="detalle.cantidad"
                                class="input-table w-20 text-center" />
                        </td>
                        <td>
                            <input type="text" readonly class="input-table w-24 text-right"
                                :value="Number(detalle.precio_unitario).toLocaleString('es-CO', {
                                    style: 'currency',
                                    currency: 'COP',
                                    minimumFractionDigits: 2
                                })" />
                        </td>
                        <td>
                            <input type="text" readonly class="input-table w-24 text-right font-semibold"
                                :value="Number(detalle.subtotal).toLocaleString('es-CO', {
                                    style: 'currency',
                                    currency: 'COP',
                                    minimumFractionDigits: 2
                                })" />
                        </td>
                        <td>
                            <button type="button" @click="traerDetalle(index)"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-2 py-1 rounded shadow transition ml-2">Editar
                            </button>
                        </td>
                        <td>
                            <button type="button" @click="removeDetalle(index)"
                                class="bg-red-600 hover:bg-red-700 text-white px-2 py-1 rounded shadow transition">Eliminar
                            </button>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
</div>
