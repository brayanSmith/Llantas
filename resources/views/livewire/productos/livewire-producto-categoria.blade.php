<div
    class="flex items-center justify-center gap-8 bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6 border border-gray-200 dark:border-gray-700">
    <!-- Categoría -->
    <div class="flex items-center gap-3">
        <span class="text-sm font-semibold text-gray-700 dark:text-gray-300 whitespace-nowrap">Categoría</span>
        <div class="flex flex-wrap gap-3">
            <label class="inline-flex items-center cursor-pointer">
                <input type="radio" x-model="producto.categoria" value="LLANTA">
                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Llantas</span>
            </label>
            <label class="inline-flex items-center cursor-pointer">
                <input type="radio" x-model="producto.categoria" value="RIN">
                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Rines</span>
            </label>
            <label class="inline-flex items-center cursor-pointer">
                <input type="radio" x-model="producto.categoria" value="SERVICIO">
                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Servicios</span>
            </label>
            <label class="inline-flex items-center cursor-pointer">
                <input type="radio" x-model="producto.categoria" value="OTRO">
                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Otros</span>
            </label>
        </div>
    </div>

    <div x-show="producto.categoria !== 'SERVICIO'" x-cloak class="flex items-center gap-3">
        <!-- Separador vertical -->
        <div class="w-px h-8 bg-gray-300 dark:bg-gray-600"></div>
        <!-- Tipo -->
        <div class="flex items-center gap-3">
            <span class="text-sm font-semibold text-gray-700 dark:text-gray-300 whitespace-nowrap">Tipo</span>
            <div class="flex flex-wrap gap-3">
                <label class="inline-flex items-center cursor-pointer">
                    <input type="radio" x-model="producto.tipo" value="NUEVO">
                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Nuevo</span>
                </label>
                <label class="inline-flex items-center cursor-pointer">
                    <input type="radio" x-model="producto.tipo" value="USADO">
                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Usado</span>
                </label>
            </div>
        </div>
    </div>

    <div x-show="producto.categoria !== 'SERVICIO'" x-cloak class="flex items-center gap-3">
        <!-- Separador vertical -->
        <div class="w-px h-8 bg-gray-300 dark:bg-gray-600"></div>
        <!-- Inventariable -->
        <div class="flex items-center gap-3">
            <span class="text-sm font-semibold text-gray-700 dark:text-gray-300 whitespace-nowrap">Inventariable</span>
            <label class="inline-flex items-center cursor-pointer">
                <div class="relative">
                    <input
                        type="checkbox"
                        class="sr-only"
                        :checked="Number(producto.inventariable) === 1"
                        @change="producto.inventariable = $event.target.checked ? 1 : 0">
                    <div
                        class="w-11 h-6 rounded-full transition-colors duration-200"
                        :class="Number(producto.inventariable) === 1
                            ? 'bg-blue-600 dark:bg-blue-500'
                            : 'bg-gray-200 dark:bg-gray-700'">
                    </div>
                    <div
                        class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform duration-200"
                        :class="Number(producto.inventariable) === 1 ? 'translate-x-5' : ''">
                    </div>
                </div>
                <span class="ml-3 text-sm text-gray-700 dark:text-gray-300"
                    x-text="Number(producto.inventariable) === 1 ? 'Sí' : 'No'"></span>
            </label>
        </div>
    </div>
</div>
