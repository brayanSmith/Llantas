<div class="flex-shrink-0 mt-6 space-y-4">
    <div class="space-y-2">
        <label for="cliente" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            Cliente
        </label>

        @include('livewire.partials.searchable-client-select')
    </div>

    {{-- Tipo de Precio y Aplica Turno juntos --}}
    <div class="mt-4 flex items-end gap-6 flex-wrap">
        <div x-show="esAdmin">
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
                {{--<button type="button"
                    :class="pedido.tipo_precio === 'OTRO' ?
                        'bg-blue-600 text-white border-blue-600' :
                        'text-body bg-neutral-primary-soft border border-default hover:bg-neutral-secondary-medium hover:text-heading'"
                    class="font-medium leading-5 rounded-e-base text-sm px-3 py-2 focus:outline-none"
                    @click="pedido.tipo_precio = 'OTRO'">
                    OTRO
                </button>--}}
            </div>
        </div>
        <div class="flex flex-col items-start">
            <label for="aplica_turno" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Aplica Turno</label>
            <button
                type="button"
                :class="pedido.aplica_turno ? 'bg-blue-600' : 'bg-gray-300 dark:bg-neutral-800'"
                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500"
                @click="pedido.aplica_turno = !pedido.aplica_turno"
                aria-pressed="pedido.aplica_turno"
            >
                <span
                    :class="pedido.aplica_turno ? 'translate-x-6' : 'translate-x-1'"
                    class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform shadow"
                ></span>
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

</div>
