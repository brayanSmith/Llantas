<div class="flex-shrink-0 mt-6 space-y-4">
    <div class="space-y-2">
        <label for="cliente" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            Cliente
        </label>

        @include('livewire.partials.searchable-client-select')
    </div>

    {{-- ...Metodo de Pago... --}}
    <div class="mt-4">
        <span class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Método de Pago:</span>
        <div class="inline-flex rounded-base shadow-xs -space-x-px" role="group">
            <button type="button"
                :class="pedido.metodo_pago === 'APARTADO' ?
                    'bg-blue-600 text-white border-blue-600' :
                    'text-body bg-neutral-primary-soft border border-default hover:bg-neutral-secondary-medium hover:text-heading'"
                class="font-medium leading-5 rounded-s-base text-sm px-3 py-2 focus:outline-none"
                @click="pedido.metodo_pago = 'APARTADO'">
                APARTADO
            </button>
            <button type="button"
                :class="pedido.metodo_pago === 'CONTADO' ?
                    'bg-blue-600 text-white border-blue-600' :
                    'text-body bg-neutral-primary-soft border border-default hover:bg-neutral-secondary-medium hover:text-heading'"
                class="font-medium leading-5 rounded-e-base text-sm px-3 py-2 focus:outline-none"
                @click="pedido.metodo_pago = 'CONTADO'">
                CONTADO
            </button>
            <button type="button"
                :class="pedido.metodo_pago === 'NO_APLICA' ?
                    'bg-blue-600 text-white border-blue-600' :
                    'text-body bg-neutral-primary-soft border border-default hover:bg-neutral-secondary-medium hover:text-heading'"
                class="font-medium leading-5 rounded-e-base text-sm px-3 py-2 focus:outline-none"
                @click="pedido.metodo_pago = 'NO_APLICA'">
                NO APLICA
            </button>
        </div>
    </div>

    {{-- tipo_precio --}}
    <div class="mt-4">
        <span class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tipo de Precio:</span>
        <div class="inline-flex rounded-base shadow-xs -space-x-px" role="group">
            <button type="button"
                :class="pedido.tipo_precio === 'DETAL' ?
                    'bg-blue-600 text-white border-blue-600' :
                    'text-body bg-neutral-primary-soft border border-default hover:bg-neutral-secondary-medium hover:text-heading'"
                class="font-medium leading-5 rounded-s-base text-sm px-3 py-2 focus:outline-none"
                @click="pedido.tipo_precio = 'DETAL'">
                DETAL
            </button>
            <button type="button"
                :class="pedido.tipo_precio === 'MAYORISTA' ?
                    'bg-blue-600 text-white border-blue-600' :
                    'text-body bg-neutral-primary-soft border border-default hover:bg-neutral-secondary-medium hover:text-heading'"
                class="font-medium leading-5 text-sm px-3 py-2 focus:outline-none"
                @click="pedido.tipo_precio = 'MAYORISTA'">
                MAYORISTA
            </button>
            <button type="button"
                :class="pedido.tipo_precio === 'OTRO' ?
                    'bg-blue-600 text-white border-blue-600' :
                    'text-body bg-neutral-primary-soft border border-default hover:bg-neutral-secondary-medium hover:text-heading'"
                class="font-medium leading-5 rounded-e-base text-sm px-3 py-2 focus:outline-none"
                @click="pedido.tipo_precio = 'OTRO'">
                OTRO
            </button>
        </div>
    </div>

    {{-- Comentarios --}}

    <div class="mt-4">
        <label for="primer_comentario" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Primer
            comentario</label>
        <textarea id="primer_comentario" x-model="pedido.primer_comentario" rows="2"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-gray-100 mb-2"
            placeholder="Escribe el primer comentario..."></textarea>
    </div>
    <div class="mt-2">
        <label for="segundo_comentario" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Segundo
            comentario</label>
        <textarea id="segundo_comentario" x-model="pedido.segundo_comentario" rows="2"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-gray-100"
            placeholder="Escribe el segundo comentario..."></textarea>
    </div>

</div>
