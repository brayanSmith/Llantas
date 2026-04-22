<div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Atributos de la Categoría</h3>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Descripción</label>
            <input type="text" x-model="producto.descripcion_producto" @input="actualizarConcatenacion()"
                class="input-pedido" />
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Concatenación</label>
            <input type="text" x-model="producto.concatenar_codigo_nombre" class="input-pedido" readonly />
        </div>
    </div>
</div>

<!-- Sección: Precios y Porcentajes -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700 mt-6">
        <div>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Precios</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Costo</label>
                    <input type="number" x-model="producto.costo_producto" step="0.01" class="input-pedido" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Valor Detal</label>
                    <input type="number" x-model="producto.valor_detal" step="0.01" class="input-pedido" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Valor Mayorista</label>
                    <input type="number" x-model="producto.valor_mayorista" step="0.01" class="input-pedido" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Valor Sin Instalación</label>
                    <input type="number" x-model="producto.valor_sin_instalacion" step="0.01" class="input-pedido" />
                </div>
            </div>
        </div>
    </div>
