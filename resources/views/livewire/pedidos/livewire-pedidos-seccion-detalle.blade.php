<div>
    <h2 class="text-xl font-bold mb-4">Detalle del Pedido</h2>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 rounded-lg overflow-hidden">
            <thead class="bg-gray-100 dark:bg-gray-800">
                <tr>
                    <th class="px-4 py-2 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 w-180">
                        Producto
                    </th>
                    <th class="px-4 py-2 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 w-24">
                        Cantidad
                    </th>
                    <th class="px-4 py-2 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 w-32">Precio
                        Unitario</th>
                    <th class="px-4 py-2 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 w-32">
                        Subtotal
                    </th>
                    <th class="px-4 py-2"></th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-900">
                <tr
                    class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                    <td class="px-4">
                        <div>
                            <x-select-tom-select :options="$productos" idKey="id" textKey="concatenar_codigo_nombre"
                                selectId="select-producto"
                                @change="productoSeleccionado = getPrecioIndividual($event.target.value, tipoPrecio)" />

                        </div>
                    </td>
                    <td>
                        <input type="number" min="1" class="input-table w-20 text-center"
                            x-model.number="cantidad" />
                    </td>

                    <td>
                    <td>
                        <input type="text" class="input-table w-24 text-right"
                            :value="Number(productoSeleccionado).toLocaleString('es-CO', {
                                style: 'currency',
                                currency: 'COP',
                                minimumFractionDigits: 2
                            })" />
                    </td>

                    <td>
                        <input type="text" readonly class="input-table w-24 text-right font-semibold"
                            :value="(subtotal).toLocaleString('es-CO', {
                                style: 'currency',
                                currency: 'COP',
                                minimumFractionDigits: 2
                            })" />
                    </td>
                    <td>
                        <button type="button" @click="agregarDetalle"
                            class="bg-green-600 hover:bg-green-700 text-white px-2 py-1 rounded shadow transition">+
                            Agregar
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>

    </div>
</div>



<div>
    <h2 class="text-xl font-bold mb-4">Detalle del Pedido</h2>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 rounded-lg overflow-hidden">
            <thead class="bg-gray-100 dark:bg-gray-800">
                <tr>
                    <th class="px-4 py-2 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 w-180">
                        Producto
                    </th>
                    <th class="px-4 py-2 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 w-24">
                        Cantidad
                    </th>
                    <th class="px-4 py-2 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 w-32">Precio
                        Unitario</th>
                    <th class="px-4 py-2 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 w-32">
                        Subtotal
                    </th>
                    <th class="px-4 py-2"></th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-900">
                <template x-for="(detalle, index) in pedido.detalles" :key="index">
                    <tr
                        class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                        <td class="px-4">
                            <div>
                                <x-select-dinamico label="Producto" placeholder="Seleccione un producto"
                                    model="detalle.producto_id" :options="$productos" idKey="id"
                                    textKey="concatenar_codigo_nombre" selectId="select-producto"
                                    @change="actualizarValoresDetalle(detalle)" />

                            </div>
                        </td>
                        <td>
                            <input type="number" min="1" x-model.number="detalle.cantidad"
                                class="input-table w-20 text-center" @change="actualizarValoresDetalle(detalle)" />
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
                            <button type="button" @click="removeDetalle(index)"
                                class="bg-red-600 hover:bg-red-700 text-white px-2 py-1 rounded shadow transition">Eliminar</button>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
</div>
