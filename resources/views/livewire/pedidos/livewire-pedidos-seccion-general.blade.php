<div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:shadow-none"
    x-data="{
        calcularFechaVencimiento() {
            if (!pedido.fecha || !pedido.dias_plazo_vencimiento) return '';
            const fecha = new Date(pedido.fecha);
            fecha.setDate(fecha.getDate() + Number(pedido.dias_plazo_vencimiento));
            return fecha.toISOString().slice(0, 16);
        }
    }">
    <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4 dark:border-gray-800">
        <div class="flex items-center gap-3">
            <div
                class="h-9 w-9 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center dark:bg-blue-500/10 dark:text-blue-300">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 6h16" />
                    <path d="M4 12h16" />
                    <path d="M4 18h10" />
                </svg>
            </div>
            <div>
                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">
                    Datos Generales del Pedido
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">Configuracion comercial y logistica</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <template x-if="pedido.turno">
                <span class="ml-3 px-2 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-700" title="Turno">
                    <span x-text="pedido.turno"></span>
                </span>
            </template>
            <span class="ml-3 px-2 py-1 rounded-full text-xs font-bold"
                :class="pedido.estado === 'COMPLETADO' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'">
                <span x-text="pedido.estado"></span>
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 px-6 py-6">
        <div class="space-y-2 md:col-span-2">
            <label class="text-[11px] font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Cliente</label>
            <x-select-dinamico label="Cliente" placeholder="Seleccione un cliente" model="pedido.cliente_id"
                :options="$clientes" idKey="id" textKey="razon_social" selectId="select-cliente" />
        </div>
        <div class="space-y-2">
            <label class="text-[11px] font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Fecha</label>
            <input type="datetime-local" :value="formatDateForInput(pedido.fecha)"
                @input="pedido.fecha = $event.target.value" class="input-pedido"
                @change="console.log('Fecha actual:', pedido.fecha)" />
        </div>
        <div class="space-y-2">
            <label class="text-[11px] font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Vendedor</label>
            <select x-model="pedido.user_id" class="input-pedido-select">
                <option value="">Seleccione un Vendedor</option>
                <template x-for="user in users" :key="user.id">
                    <option :value="user.id" x-text="user.name"></option>
                </template>
            </select>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 lg:col-span-2">
            <div class="space-y-2 md:col-span-2 col-span-1">
                <label class="text-[11px] font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Metodo de Pago</label>
                <x-select-dinamico label="Medio de Pago" placeholder="Seleccione un medio de pago..."
                    model="pedido.id_puc" x-model="pedido.id_puc" :options="$pucs" idKey="id"
                    textKey="concatenar_subcuenta_concepto" selectId="select-puc" />
            </div>
            <div class="space-y-2 col-span-1">
                <label class="text-[11px] font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Tipo Precio</label>
                <select x-model="pedido.tipo_precio" class="input-pedido-select"
                    @change="actualizarTodosLosDetalles($event.target.value); productoSeleccionado = getPrecioIndividual(productoIngresado, $event.target.value); valorIngresado = productoSeleccionado">
                    <option value="MAYORISTA">MAYORISTA</option>
                    <option value="DETAL">DETAL</option>
                </select>
            </div>
            <div class="space-y-2 col-span-1">
                <label class="text-[11px] font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Bodega</label>
                <select x-model="pedido.bodega_id" class="input-pedido-select">
                    <option value="">Seleccione una bodega</option>
                    <template x-for="bodega in bodegas" :key="bodega.id">
                        <option :value="bodega.id" x-text="bodega.nombre_bodega"></option>
                    </template>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:col-span-2 mt-4">
            <div class="space-y-2">
                <label class="text-[11px] font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Observacion</label>
                <textarea x-model="pedido.observacion" class="input-pedido min-h-[96px]"></textarea>
            </div>
            <div class="space-y-2">
                <label class="text-[11px] font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Observacion Pago</label>
                <textarea x-model="pedido.observacion_pago" class="input-pedido min-h-[96px]"></textarea>
            </div>
        </div>
    </div>
</div>
