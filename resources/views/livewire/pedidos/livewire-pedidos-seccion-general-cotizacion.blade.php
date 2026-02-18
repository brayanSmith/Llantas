<div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:shadow-none" x-data="{
    calcularFechaVencimiento() {
        if (!pedido.fecha || !pedido.dias_plazo_vencimiento) return '';
        const fecha = new Date(pedido.fecha);
        fecha.setDate(fecha.getDate() + Number(pedido.dias_plazo_vencimiento));
        return fecha.toISOString().slice(0, 16);
    }
}">
    <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4 dark:border-gray-800">
        <div class="flex items-center gap-3">
            <div class="h-9 w-9 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center dark:bg-emerald-500/10 dark:text-emerald-300">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 7h18" />
                    <path d="M7 7v10a2 2 0 002 2h6a2 2 0 002-2V7" />
                    <path d="M9 7V5a1 1 0 011-1h4a1 1 0 011 1v2" />
                </svg>
            </div>
            <div>
                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Datos de Cotizacion</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">Informacion general del pedido</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 px-6 py-6">
        <div class="space-y-2">
            <label class="text-[11px] font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Cliente</label>
            <x-select-dinamico label="Cliente" placeholder="Seleccione un cliente" model="pedido.cliente_id" :options="$clientes"
                idKey="id" textKey="razon_social" selectId="select-cliente" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="space-y-2">
                <label class="text-[11px] font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Fecha</label>
                <input type="datetime-local" :value="formatDateForInput(pedido.fecha)"
                    @input="pedido.fecha = $event.target.value" class="input-pedido"
                    @change="console.log('Fecha actual:', pedido.fecha)" />
            </div>
            <div class="space-y-2">
                <label class="text-[11px] font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Tipo Precio</label>
                <select x-model="pedido.tipo_precio" class="input-pedido-select" @change="actualizarTodosLosDetalles()">
                    <option value="FERRETERO">FERRETERO</option>
                    <option value="MAYORISTA">MAYORISTA</option>
                    <option value="DETAL">DETAL</option>
                </select>
            </div>
        </div>

        <div class="space-y-2">
            <label class="text-[11px] font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Primer Comentario</label>
            <textarea x-model="pedido.primer_comentario" class="input-pedido min-h-[96px]" placeholder="Notas internas o indicaciones"></textarea>
        </div>
        <div class="space-y-2">
            <label class="text-[11px] font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Segundo Comentario</label>
            <textarea x-model="pedido.segundo_comentario" class="input-pedido min-h-[96px]" placeholder="Observaciones para el cliente"></textarea>
        </div>
    </div>

</div>
