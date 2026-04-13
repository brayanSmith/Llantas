<div class="w-full bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl shadow p-6 space-y-4">
    <div class="flex flex-wrap items-end gap-4">
        <div class="flex-1 min-w-[200px]">
            <input
                type="text"
                x-model="referenciaIngresada"
                placeholder="Ingrese la referencia del producto"
                class="input-pedido"
            >
        </div>

        <div class="min-w-[180px]">
            <select x-model="tipoPrecioSeleccionado" class="input-pedido-select">
                <option value="DETAL">Precio Detal</option>
                <option value="MAYORISTA">Precio Mayorista</option>
            </select>
        </div>

        <button
            @click="buscarProducto(referenciaIngresada, tipoPrecioSeleccionado)"
            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z" />
            </svg>
            Buscar Producto
        </button>
        <button
            @click="copiarSalida()"
            :class="copiado ? 'bg-gray-500 hover:bg-gray-600 focus:ring-gray-500' : 'bg-green-500 hover:bg-green-600 focus:ring-green-500'"
            class="inline-flex items-center gap-2 px-4 py-2 text-white rounded-md focus:outline-none focus:ring-2 transition"
        >
            <template x-if="!copiado">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
            </template>
            <template x-if="copiado">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
            </template>
            <span x-text="copiado ? '¡Copiado!' : 'Copiar'"></span>
        </button>
    </div>

    <div
        x-show="salida"
        class="bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow p-4 whitespace-pre-line text-gray-800 dark:text-gray-100"
        style="min-height: 120px;"
    >
        <span x-text="salida"></span>
    </div>
</div>
