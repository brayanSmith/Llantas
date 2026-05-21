<div x-show="mostrarModalPago" style="display: none;"
    class="fixed inset-0 flex items-center justify-center z-50 bg-black/40 dark:bg-black/60 backdrop-blur-sm">
    <div class="bg-white dark:bg-neutral-900 p-6 rounded shadow-lg w-full max-w-md mx-2 max-h-[90vh] overflow-y-auto">

        {{-- ...Metodo de Pago... --}}
        <div class="mt-4">
            <span class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Método de Pago:</span>
            <div class="inline-flex rounded-base shadow-xs -space-x-px" role="group">
                <button type="button"
                    :class="pedido.tipo_pago === 'APARTADO' ?
                        'bg-blue-600 text-white border-blue-600' :
                        'text-body bg-neutral-primary-soft border border-default hover:bg-neutral-secondary-medium hover:text-heading'"
                    class="font-medium leading-5 rounded-s-base text-sm px-3 py-2 focus:outline-none"
                    @click="pedido.tipo_pago = 'APARTADO'">
                    APARTADO
                </button>
                <button type="button"
                    :class="pedido.tipo_pago === 'CONTADO' ?
                        'bg-blue-600 text-white border-blue-600' :
                        'text-body bg-neutral-primary-soft border border-default hover:bg-neutral-secondary-medium hover:text-heading'"
                    class="font-medium leading-5 rounded-e-base text-sm px-3 py-2 focus:outline-none"
                    @click="pedido.tipo_pago = 'CONTADO'">
                    CONTADO
                </button>
                <button type="button"
                    :class="pedido.tipo_pago === 'CONTRA_ENTREGA' ?
                        'bg-blue-600 text-white border-blue-600' :
                        'text-body bg-neutral-primary-soft border border-default hover:bg-neutral-secondary-medium hover:text-heading'"
                    class="font-medium leading-5 rounded-e-base text-sm px-3 py-2 focus:outline-none"
                    @click="pedido.tipo_pago = 'CONTRA_ENTREGA'">
                    CONTRA ENTREGA
                </button>
                <button type="button"
                    :class="pedido.tipo_pago === 'CREDITO' ?
                        'bg-blue-600 text-white border-blue-600' :
                        'text-body bg-neutral-primary-soft border border-default hover:bg-neutral-secondary-medium hover:text-heading'"
                    class="font-medium leading-5 rounded-e-base text-sm px-3 py-2 focus:outline-none"
                    @click="pedido.tipo_pago = 'CREDITO'">
                    CREDITO
                </button>
            </div>
        </div>

        {{-- Descuento --}}
        <div class="flex justify-between items-center gap-3 mt-4">
            <label for="descuento"
                class="text-sm font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">Descuento (COP):</label>
            <input type="number" id="descuento" x-model="pedido.descuento" min="0" step="0.01"
                class="w-32 px-3 py-2 border border-gray-300 rounded-lg text-right focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-neutral-800 dark:border-neutral-700 dark:text-gray-100"
                placeholder="0" />
        </div>

        {{-- flete --}}
        <div class="flex justify-between items-center gap-3 mt-4">
            <label for="flete" class="text-sm font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">Flete
                (COP):</label>
            <input type="number" id="flete" x-model="pedido.flete" min="0" step="0.01"
                class="w-32 px-3 py-2 border border-gray-300 rounded-lg text-right focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-neutral-800 dark:border-neutral-700 dark:text-gray-100"
                placeholder="0" />
        </div>

        {{-- Total a pagar --}}
        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-neutral-700">
            <div class="flex justify-between items-center mb-2 text-lg font-bold">
                <span>Total a Pagar:</span>
                <span>COP:
                    <span
                        x-text="getTotalAPagar().toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 2 })">
                    </span>
                </span>
            </div>
        </div>

        <div x-show="pedido.tipo_pago !== 'CONTRA_ENTREGA'">
            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-neutral-700 space-y-4">
                <div>
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-700 dark:text-gray-300">Abonos</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Agrega uno o varios abonos para este pedido.</p>
                </div>

                {{-- Puc --}}
                <div class="flex justify-between items-center gap-3">
                    <x-select-searchable :options="$pucs" idKey="id" textKey="concatenar_subcuenta_concepto"
                        selectId="select-puc-searchable" placeholder="Seleccione un medio de pago..."
                        x-model="pedido.abono_puc_id" />
                </div>

                {{-- Con cuanto paga --}}
                <div class="flex justify-between items-center gap-3 mt-4">
                    <label for="con-cuanto-paga"
                        class="text-sm font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">Monto del abono
                        (COP):</label>
                    <input type="number" id="con-cuanto-paga" x-model.number="pedido.con_cuanto_paga" min="0"
                        step="0.01"
                        class="w-32 px-3 py-2 border border-gray-300 rounded-lg text-right focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-neutral-800 dark:border-neutral-700 dark:text-gray-100"
                        placeholder="0" />
                </div>

                <div>
                    <label for="descripcion-abono"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Descripcion del abono</label>
                    <textarea id="descripcion-abono" x-model="pedido.descripcion_abono" rows="2"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-gray-100"
                        placeholder="Ejemplo: desprendible, transferencia, observacion del abono..."></textarea>
                </div>

                <div class="flex justify-end">
                    <button type="button" @click="agregarAbono()"
                        class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-400">
                        Agregar abono
                    </button>
                </div>

                <div class="space-y-2 max-h-56 overflow-y-auto pr-1">
                    <template x-if="!pedido.abonos || pedido.abonos.length === 0">
                        <div class="rounded-lg border border-dashed border-gray-300 px-4 py-3 text-sm text-gray-500 dark:border-neutral-700 dark:text-gray-400">
                            Aún no has agregado abonos.
                        </div>
                    </template>

                    <template x-for="(abono, index) in pedido.abonos" :key="`abono-${index}`">
                        <div class="flex items-center justify-between gap-3 rounded-lg border border-gray-200 px-4 py-3 dark:border-neutral-700">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-gray-800 dark:text-gray-100"
                                    x-text="abono.puc_nombre || getNombrePuc(abono.puc_id)">NA</p>



                                <template x-if="abono.descripcion">
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 wrap-break-word"
                                        x-text="abono.descripcion"></p>
                                </template>

                                <p class="text-xs text-gray-500 dark:text-gray-400"
                                    x-text="abono.fecha || 'Sin fecha'"></p>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-sm font-bold text-blue-600 dark:text-blue-400"
                                    x-text="Number(abono.monto || 0).toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 2 })"></span>
                                <button type="button" @click="removerAbono(index)"
                                    class="rounded-md bg-red-50 px-3 py-1 text-xs font-semibold text-red-600 transition-colors hover:bg-red-100 dark:bg-red-950/40 dark:text-red-300 dark:hover:bg-red-950/70">
                                    Quitar
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- Cambio --}}
            <div class="mt-4 pt-2">
                <div class="flex justify-between items-center mb-2 text-base font-semibold text-gray-700 dark:text-gray-300">
                    <span>Total abonado:</span>
                    <span>
                        COP:
                        <span
                            x-text="getTotalAbonos().toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 2 })">
                        </span>
                    </span>
                </div>
                <div class="flex justify-between items-center mb-2 text-lg font-bold">
                    <span class="flex items-center gap-2">
                        Cambio:
                    </span>
                    <span class="text-blue-600 font-bold flex items-center gap-1">
                        COP:
                        <span
                            x-text="pedido.cambio !== undefined ? pedido.cambio.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) : '0.00'">
                        </span>
                    </span>
                </div>
            </div>

        {{-- Comentario Pago --}}
        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-neutral-700">
            <div class="mt-2">
                <label for="observacion_pago"
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Comentario Pago</label>
                <textarea id="observacion_pago" x-model="pedido.observacion_pago" rows="2"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-gray-100"
                    placeholder="Escribe un comentario sobre el pago..."></textarea>
            </div>
        </div>

        <div class="flex gap-3 mt-6">
            <button @click="enviar()" wire:loading.attr="disabled"
                class="flex-1 py-3 bg-green-600 text-white font-bold text-base rounded-lg shadow-lg
                       transition-colors duration-200 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-400
                       disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                <svg x-show="isLoading" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 24 24" style="display: none;"
                    :style="isLoading ? 'display:inline-block' : 'display:none'">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
                <span x-show="!isLoading" :style="isLoading ? 'display:none' : 'display:inline'">Finalizar Venta</span>
                <span x-show="isLoading" :style="isLoading ? 'display:inline' : 'display:none'">Procesando...</span>
            </button>
            <button @click="mostrarModalPago = false" type="button"
                class="flex-1 py-3 rounded-lg bg-gray-100 dark:bg-neutral-800 text-gray-700 dark:text-gray-200 font-semibold border border-gray-300 dark:border-neutral-700
                       hover:bg-gray-200 dark:hover:bg-neutral-700 transition-colors duration-200 shadow focus:outline-none focus:ring-2 focus:ring-gray-400">
                Cerrar
            </button>
        </div>
    </div>
</div>
