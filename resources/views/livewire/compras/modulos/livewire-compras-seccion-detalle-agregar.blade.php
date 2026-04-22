<div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:shadow-none">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 rounded-lg overflow-hidden">
            <thead class="bg-gray-100 dark:bg-gray-800">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 w-[40%]">
                        Producto
                    </th>
                    <!-- Columnas dinámicas por bodega -->
                    <template x-for="bodega in bodegas" :key="bodega.id">
                        <th class="px-4 py-2 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 w-[10%]">
                            <span x-text="bodega.nombre_bodega"></span>
                        </th>
                    </template>
                    <th class="px-4 py-2 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 w-[13%]">
                        Precio
                        Unitario</th>
                    <th class="px-4 py-2 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 w-[13%]">
                        Subtotal
                    </th>
                    <th class="px-4 py-2 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 w-[11%]">
                        Acciones
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-900">
                <tr
                    class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                    <td class="px-4">
                        <x-select-searchable
                            :options="$productos"
                            idKey="id"
                            textKey="concatenar_codigo_nombre"
                            selectId="select-producto"
                            placeholder="Seleccione un producto..."
                            x-model="productoIngresado" />
                    </td>
                    <!-- Inputs dinámicos por bodega -->
                    <template x-for="bodega in bodegas" :key="bodega.id">
                        <td>
                            <input type="number" min="0" class="input-table w-20 text-center"
                                x-model.number="cantidadesPorBodega[bodega.id]"
                                placeholder="0" />
                        </td>
                    </template>

                    <td>
                        <input type="number" step="0.01" min="0" class="input-table w-24 text-center"
                            x-model.number="valorIngresado" placeholder="0.00" />
                    </td>
                    <td>
                        <input type="text" readonly class="input-table w-24 text-center font-semibold"
                            :value="(getCantidadTotal() * valorIngresado).toLocaleString('es-CO', {
                                style: 'currency',
                                currency: 'COP',
                                minimumFractionDigits: 2
                            })" />
                    </td>
                    <td class="px-4 py-2">
                        <div class="flex items-center justify-center gap-2">
                        <button type="button"
                            @click="detalleEditandoIndex !== null ? (actualizarValoresDetalle(detalleEditandoIndex, productoIngresado, cantidadesPorBodega, valorIngresado)) : agregarDetallePorBodegas(productoIngresado, cantidadesPorBodega, valorIngresado)"
                            :class="detalleEditandoIndex !== null ? 'bg-blue-600 hover:bg-blue-700' :
                                'bg-green-600 hover:bg-green-700'"
                            class="text-white px-3 py-1 rounded shadow transition text-sm">
                            <span x-text="detalleEditandoIndex !== null ? 'Actualizar' : '+ Agregar'"></span>
                        </button>
                        <button type="button"
                            @click="limpiarCampos()"
                            x-show="detalleEditandoIndex !== null"
                            class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-1 rounded shadow transition text-sm">
                            Cancelar
                        </button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
