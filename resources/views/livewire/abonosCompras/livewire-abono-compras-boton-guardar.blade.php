<div>
        <button type="button" @click="generarAbonos()"
            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow transition flex items-center justify-center"
            :disabled="isLoading || comprasSeleccionadas.length === 0">
            <template x-if="isLoading">
                <svg class="animate-spin h-5 w-5 mr-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
            </template>
            <span x-text="isLoading ? 'Generando...' : 'Generar Abonos'"></span>
        </button>
    </div>
