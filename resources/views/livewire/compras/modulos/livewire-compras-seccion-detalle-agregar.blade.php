<div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 rounded-lg overflow-hidden">
            <thead class="bg-gray-100 dark:bg-gray-800">
                <tr>
                    <th  class="px-4 py-2 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 w-[40%]">
                        Producto
                    </th>
                    <th x-show="compra.item_compra === 'GASTO'" class="px-4 py-2 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 w-[25%]">
                        Descripción
                    </th>
                    <th class="px-4 py-2 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 w-[10%]">
                        Cantidad
                    </th>
                    <th class="px-4 py-2 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 w-[20%]">Precio
                        Unitario</th>
                    <th class="px-4 py-2 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 w-[20%]">
                        Subtotal
                    </th>
                    <th class="px-4 py-2"></th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-900">
                <tr
                    class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                    <td class="px-4">
                        <div x-show="compra.item_compra === 'PRODUCTO'">
                            <x-select-tom-select
                                :options="$productos"
                                idKey="id"
                                textKey="concatenar_codigo_nombre"
                                selectId="select-producto" placeholder="Seleccione un producto..."
                                x-model="productoIngresado" />
                        </div>
                        <div x-show="compra.item_compra === 'GASTO'">
                            <x-select-tom-select :options="$pucs" idKey="id"
                                textKey="concatenar_subcuenta_concepto" selectId="select-gasto"
                                placeholder="Seleccione un gasto..." x-model="productoIngresado" />
                        </div>
                    </td>
                    <td x-show="compra.item_compra === 'GASTO'">
                        <div>
                            <input type="text" class="input-table w-20 text-center" x-model="descripcionIngresada" />
                        </div>
                    </td>
                    <td>
                        <input type="number" min="1" class="input-table w-20 text-center"
                            x-model.number="cantidadIngresada" />
                    </td>

                    <td>
                        <input type="number" step="0.01" min="0" class="input-table w-24 text-right"
                            x-model.number="valorIngresado" placeholder="0.00" />
                    </td>
                    <td>
                        <input type="text" readonly class="input-table w-24 text-right font-semibold"
                            :value="(cantidadIngresada * valorIngresado).toLocaleString('es-CO', {
                                style: 'currency',
                                currency: 'COP',
                                minimumFractionDigits: 2
                            })" />
                    </td>
                    <td>
                        <button type="button"
                            @click="detalleEditandoIndex !== null ? (actualizarValoresDetalle(detalleEditandoIndex, productoIngresado, cantidadIngresada, valorIngresado)) : agregarDetalle(productoIngresado, cantidadIngresada, valorIngresado)"
                            :class="detalleEditandoIndex !== null ? 'bg-blue-600 hover:bg-blue-700' :
                                'bg-green-600 hover:bg-green-700'"
                            class="text-white px-2 py-1 rounded shadow transition">
                            <span x-text="detalleEditandoIndex !== null ? 'Actualizar' : '+ Agregar'"></span>
                        </button>
                    </td>
                    <td>
                        <button type="button"
                            @click="detalleEditandoIndex = null, productoIngresado = null, cantidadIngresada = 1, valorIngresado = 0; setTimeout(() => { const sel = document.getElementById('select-producto'); if(sel && sel.tomselect) sel.tomselect.clear(); }, 50)"
                            x-show="detalleEditandoIndex !== null"
                            class="bg-gray-500 hover:bg-gray-600 text-white px-2 py-1 rounded shadow transition">
                            Cancelar
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
