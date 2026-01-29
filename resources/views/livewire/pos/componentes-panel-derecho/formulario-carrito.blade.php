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
                :class="pedido.metodo_pago === 'CREDITO' ?
                    'bg-blue-600 text-white border-blue-600' :
                    'text-body bg-neutral-primary-soft border border-default hover:bg-neutral-secondary-medium hover:text-heading'"
                class="font-medium leading-5 rounded-s-base text-sm px-3 py-2 focus:outline-none"
                @click="pedido.metodo_pago = 'CREDITO'">
                CRÉDITO
            </button>
            <button type="button"
                :class="pedido.metodo_pago === 'CONTADO' ?
                    'bg-blue-600 text-white border-blue-600' :
                    'text-body bg-neutral-primary-soft border border-default hover:bg-neutral-secondary-medium hover:text-heading'"
                class="font-medium leading-5 rounded-e-base text-sm px-3 py-2 focus:outline-none"
                @click="pedido.metodo_pago = 'CONTADO'">
                CONTADO
            </button>
        </div>
    </div>
    {{-- ...Estado de Venta... --}}
    <div class="mt-4">
        <span class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Estado de Venta:</span>
        <div class="inline-flex rounded-base shadow-xs -space-x-px" role="group">
            <button type="button"
                :class="pedido.estado_venta === 'COTIZACION' ?
                    'bg-blue-600 text-white border-blue-600' :
                    'text-body bg-neutral-primary-soft border border-default hover:bg-neutral-secondary-medium hover:text-heading'"
                class="font-medium leading-5 rounded-s-base text-sm px-3 py-2 focus:outline-none"
                @click="pedido.estado_venta = 'COTIZACION'">
                COTIZACION
            </button>
            <button type="button"
                :class="pedido.estado_venta === 'VENTA' ?
                    'bg-blue-600 text-white border-blue-600' :
                    'text-body bg-neutral-primary-soft border border-default hover:bg-neutral-secondary-medium hover:text-heading'"
                class="font-medium leading-5 rounded-e-base text-sm px-3 py-2 focus:outline-none"
                @click="pedido.estado_venta = 'VENTA'">
                VENTA
            </button>
        </div>
    </div>

    {{-- ...Tipo de Venta...--}}
    <div class="mt-4">
        <span class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tipo de Venta:</span>
        <div class="inline-flex rounded-base shadow-xs -space-x-px" role="group">
            <button type="button"
                :class="pedido.tipo_venta === 'ELECTRONICA' ?
                    'bg-blue-600 text-white border-blue-600' :
                    'text-body bg-neutral-primary-soft border border-default hover:bg-neutral-secondary-medium hover:text-heading'"
                class="font-medium leading-5 rounded-s-base text-sm px-3 py-2 focus:outline-none"
                @click="pedido.tipo_venta = 'ELECTRONICA'">
                ELECTRONICA
            </button>
            <button type="button"
                :class="pedido.tipo_venta === 'REMISIONADA' ?
                    'bg-blue-600 text-white border-blue-600' :
                    'text-body bg-neutral-primary-soft border border-default hover:bg-neutral-secondary-medium hover:text-heading'"
                class="font-medium leading-5 rounded-e-base text-sm px-3 py-2 focus:outline-none"
                @click="pedido.tipo_venta = 'REMISIONADA'">
                REMISIONADA
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

    {{-- flete --}}
    <div class="mt-4">
        <label for="flete" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Flete
            (COP)</label>
        <input type="number" id="flete" x-model="pedido.flete" min="0" step="0.01"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-gray-100"
            placeholder="Ingresa el valor del flete..." />
    </div>


    {{-- Total a pagar --}}
    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-neutral-700">
        <div class="flex justify-between items-center mb-2 text-lg font-bold">
            <span>Total a Pagar:</span>
            <span>COP:
                <span x-text="getTotalAPagar().toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 2 })">
                </span>
            </span>
        </div>
    </div>
</div>
