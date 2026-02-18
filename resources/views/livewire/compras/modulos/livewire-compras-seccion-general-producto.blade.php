<div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:shadow-none">
    <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4 dark:border-gray-800">
        <div class="flex items-center gap-3">
            <div class="h-9 w-9 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center dark:bg-amber-500/10 dark:text-amber-300">
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

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 px-6 py-6">
        <div class="space-y-2">
            <label class="text-[11px] font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Categoria Compra</label>
            <div class="flex flex-wrap items-center gap-4 rounded-lg border border-gray-200/70 bg-gray-50 px-3 py-2 dark:border-gray-700 dark:bg-gray-800/60">
                <label class="inline-flex items-center cursor-pointer">
                    <input type="radio" x-model="compra.categoria_compra" value="MATERIA_PRIMA"
                        class="form-radio text-blue-600 focus:ring-blue-400"
                        @input="compra.categoria_compra = $event.target.value" required />
                    <span class="ml-2">MATERIA PRIMA</span>
                </label>
                <label class="inline-flex items-center cursor-pointer">
                    <input type="radio" x-model="compra.categoria_compra" value="PRODUCTO_TERMINADO"
                        class="form-radio text-blue-600 focus:ring-blue-400"
                        @input="compra.categoria_compra = $event.target.value" />
                    <span class="ml-2">PRODUCTO TERMINADO</span>
                </label>
                <label class="inline-flex items-center cursor-pointer">
                    <input type="radio" x-model="compra.categoria_compra" value="OTRO"
                        class="form-radio text-blue-600 focus:ring-blue-400"
                        @input="compra.categoria_compra = $event.target.value" />
                    <span class="ml-2">OTRO</span>
                </label>
            </div>
        </div>
        <div class="space-y-2">
            <label class="text-[11px] font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Factura</label>
            <input type="text" x-model="compra.factura" class="input-pedido" required />
        </div>

        <div class="space-y-2">
            <label class="text-[11px] font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Proveedor</label>
            <x-select-dinamico label="Proveedor" placeholder="Seleccione un proveedor" model="compra.proveedor_id"
                :options="$proveedores" idKey="id" textKey="nombre_proveedor" selectId="select-proveedor" required />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="space-y-2">
                <label class="text-[11px] font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Fecha</label>
                <input type="datetime-local" :value="formatDateForInput(compra.fecha)" @input="compra.fecha = $event.target.value" class="input-pedido" required />
            </div>
            <div class="space-y-2">
                <label class="text-[11px] font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Plazo</label>
                <input type="number" x-model="compra.dias_plazo_vencimiento" class="input-pedido" required />
            </div>
            <div class="space-y-2">
                <label class="text-[11px] font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Fecha Vencimiento</label>
                <input type="datetime-local" :value="calcularFechaVencimiento(compra.fecha, compra.dias_plazo_vencimiento)"
                 class="input-pedido" readonly required />
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 lg:col-span-2">
            <div class="space-y-2">
                <label class="text-[11px] font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Metodo de Pago</label>
                <select x-model="compra.metodo_pago" class="input-pedido-select" required>
                    <option value="">Seleccione un metodo de pago</option>
                    <option value="CREDITO">CREDITO</option>
                    <option value="CONTADO">CONTADO</option>
                </select>
            </div>
            <div class="space-y-2">
                <label class="text-[11px] font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Estado</label>
                <select x-model="compra.estado" class="input-pedido-select" required>
                    <option value="PENDIENTE">PENDIENTE</option>
                    <option value="FACTURADO">FACTURADO</option>
                    <option value="ANULADO">ANULADO</option>
                </select>
            </div>
            <div class="space-y-2">
                <label class="text-[11px] font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Tipo Compra</label>
                <select x-model="compra.tipo_compra" class="input-pedido-select" required>
                    <option value="ELECTRONICA">ELECTRONICA</option>
                    <option value="REMISIONADA">REMISIONADA</option>
                </select>
            </div>
            <div class="space-y-2">
                <label class="text-[11px] font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Bodega</label>
                <select x-model="compra.bodega_id" class="input-pedido-select" required>
                    <option value="">Seleccione una bodega</option>
                    <template x-for="bodega in bodegas" :key="bodega.id">
                        <option :value="bodega.id" x-text="bodega.nombre_bodega"></option>
                    </template>
                </select>
            </div>
        </div>
        <div class="lg:col-span-2 space-y-2">
            <label class="text-[11px] font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Observaciones</label>
            <textarea x-model="compra.observaciones" class="input-pedido min-h-[96px]"></textarea>
        </div>
    </div>
</div>
