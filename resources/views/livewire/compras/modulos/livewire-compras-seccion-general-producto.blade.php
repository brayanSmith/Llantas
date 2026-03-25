<div
    class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:shadow-none">
    <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4 dark:border-gray-800">
        <div class="flex items-center gap-3">
            <div
                class="h-9 w-9 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center dark:bg-amber-500/10 dark:text-amber-300">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 6h16" />
                    <path d="M4 12h16" />
                    <path d="M4 18h10" />
                </svg>
            </div>
            <div>
                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Datos Generales de la Compra</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">Informacion comercial y documental</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 px-6 py-6">
        <div class="space-y-2">
            <label
                class="text-[11px] font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Factura</label>
            <input type="text" x-model="compra.factura" class="input-pedido" required />
        </div>
        <div class="space-y-2">
            <label
                class="text-[11px] font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Proveedor</label>
            <x-select-searchable :options="$proveedores" idKey="id" textKey="nombre_proveedor"
                selectId="select-proveedor-searchable" placeholder="Seleccione un proveedor..."
                x-model="compra.proveedor_id" />
        </div>

        <div class="space-y-2">
            <label
                class="text-[11px] font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Fecha</label>
            <input type="datetime-local" :value="formatDateForInput(compra.fecha)"
                @input="compra.fecha = $event.target.value" class="input-pedido" required />
        </div>

        <div class="space-y-2">
            <label
                class="text-[11px] font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Estado</label>
            <select x-model="compra.estado" class="input-pedido-select" required>
                <option value="PENDIENTE">PENDIENTE</option>
                <option value="RECIBIDA">RECIBIDA</option>
            </select>
        </div>

        <div class="lg:col-span-4 space-y-2">
            <label
                class="text-[11px] font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Observaciones</label>
            <textarea x-model="compra.observaciones" class="input-pedido min-h-[96px]"></textarea>
        </div>
    </div>
</div>
