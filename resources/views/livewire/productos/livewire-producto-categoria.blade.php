<div class="flex items-center justify-center gap-4 bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6 border border-gray-200 dark:border-gray-700">
    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-0 text-center whitespace-nowrap">Categoría</label>
    <div class="flex flex-wrap gap-3">
        <template x-for="cat in categorias" :key="cat.id">
            <label class="inline-flex items-center cursor-pointer">
                <input type="radio" x-model.number="producto.categoria_id" :value="cat.id"
                    @change="cargarAtributosLocales()"
                    class="form-radio text-blue-600 dark:bg-gray-700">
                <span class="ml-2 text-gray-700 dark:text-gray-300" x-text="cat.nombre_categoria"></span>
            </label>
        </template>
    </div>
</div>
