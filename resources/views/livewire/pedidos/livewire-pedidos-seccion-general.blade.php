<div class="grid grid-cols-2 gap-4" x-data="{
    calcularFechaVencimiento() {
        if (!pedido.fecha || !pedido.dias_plazo_vencimiento) return '';
        const fecha = new Date(pedido.fecha);
        fecha.setDate(fecha.getDate() + Number(pedido.dias_plazo_vencimiento));
        return fecha.toISOString().slice(0, 16);
    }
}">
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Estado Venta</label>
        <div class="flex gap-4 mt-2">
            <label class="inline-flex items-center cursor-pointer">
                <input type="radio" x-model="pedido.estado_venta" value="COTIZACION" class="form-radio text-blue-600 focus:ring-blue-400" />
                <span class="ml-2">COTIZACION</span>
            </label>
            <label class="inline-flex items-center cursor-pointer">
                <input type="radio" x-model="pedido.estado_venta" value="VENTA" class="form-radio text-blue-600 focus:ring-blue-400" />
                <span class="ml-2">VENTA</span>
            </label>
        </div>
    </div>
    <div x-data x-init="
        $nextTick(() => {
            const select = $el.querySelector('select');
            // Destruir instancia previa si existe
            if (select.tomselect) {
                select.tomselect.destroy();
            }
            const tom = new TomSelect(select, {
                placeholder: 'Seleccione un cliente',
                allowEmptyOption: true,
                onChange: function(value) {
                    pedido.cliente_id = value;
                }
            });
            // Preload: setear valor inicial
            tom.setValue(pedido.cliente_id);
            // Cuando cambia Alpine, actualizar Tom Select
            $watch('pedido.cliente_id', value => {
                tom.setValue(value);
            });
        });
    " class="w-full">
        <label>Cliente</label>
        <select x-model="pedido.cliente_id" id="select-cliente">
            <option value="">Seleccione un cliente</option>
            <template x-for="cliente in clientes" :key="cliente.id">
                <option :value="cliente.id" x-text="cliente.numero_documento + ' - ' + cliente.razon_social"></option>
            </template>
        </select>
    </div>
<div class="grid grid-cols-3 gap-4">
    <div>
        <label>Fecha</label>
        <input type="datetime-local" x-model="formatDateForInput(pedido.fecha)" class="input-pedido" />
    </div>
    <div>
        <label>Días Plazo Vencimiento</label>
        <input type="number" x-model="pedido.dias_plazo_vencimiento" class="input-pedido" />
    </div>
    <div>
        <label>Fecha Vencimiento</label>
        <input type="datetime-local" x-model="formatDateForInput(pedido.fecha_vencimiento)" :value="calcularFechaVencimiento()" class="input-pedido" readonly />
    </div>
</div>
    <div>
        <label>Ciudad</label>
        <input type="text" x-model="pedido.ciudad" class="input-pedido" />
    </div>
    <div>
        <label>Método de Pago</label>
        <select x-model="pedido.metodo_pago" class="input-pedido-select" >
            <option value="">Seleccione un método de pago</option>
            <option value="CREDITO">CREDITO</option>
            <option value="CONTADO">CONTADO</option>
        </select>
    </div>
    <div>
        <label>Tipo Precio</label>
        <select x-model="pedido.tipo_precio" class="input-pedido-select">
            <option value="FERRETERO">FERRETERO</option>
            <option value="MAYORISTA">MAYORISTA</option>
            <option value="DETAL">DETAL</option>
        </select>
    </div>

    <div>
        <label>Estado</label>
        <select x-model="pedido.estado" class="input-pedido-select">
            <option value="PENDIENTE">Pendiente</option>
            <option value="FACTURADO">Facturado</option>
            <option value="ANULADO">Anulado</option>
        </select>
    </div>
    <div>
        <label>Tipo Venta</label>
        <select x-model="pedido.tipo_venta" class="input-pedido-select">
            <option value="ELECTRONICA">ELECTRONICA</option>
            <option value="REMISIONADA">REMISIONADA</option>
        </select>
    </div>
    <div>
        <label>Bodega</label>
        <select x-model="pedido.bodega_id" class="input-pedido-select">
            <option value="">Seleccione una bodega</option>
            <template x-for="bodega in bodegas" :key="bodega.id">
                <option :value="bodega.id" x-text="bodega.nombre_bodega"></option>
            </template>
        </select>
    </div>
    <div>
        <label>BodegaTexto</label>
        <input type="text" x-model="pedido.bodega.nombre_bodega" class="input-pedido" />
    </div>
    <div>
        <label>Alistador</label>
        <select x-model="pedido.alistador_id" class="input-pedido-select">
            <option value="">Seleccione un Alistador</option>
            <template x-for="alistador in alistadores" :key="alistador.id">
                <option :value="alistador.id" x-text="alistador.name"></option>
            </template>
        </select>
    </div>
    <div>
        <label>Vendedor</label>
        <select x-model="pedido.user_id" class="input-pedido-select">
            <option value="">Seleccione un Vendedor</option>
            <template x-for="user in users" :key="user.id">
                <option :value="user.id" x-text="user.name"></option>
            </template>
        </select>
    </div>

    <div>
        <label>Primer Comentario</label>
        <input type="text" x-model="pedido.primer_comentario" class="input-pedido" />
    </div>
</div>

