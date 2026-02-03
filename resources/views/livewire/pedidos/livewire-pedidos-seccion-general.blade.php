<div class="grid grid-cols-2 gap-4" x-data="{
    calcularFechaVencimiento() {
        if (!pedido.fecha || !pedido.dias_plazo_vencimiento) return '';
        const fecha = new Date(pedido.fecha);
        fecha.setDate(fecha.getDate() + Number(pedido.dias_plazo_vencimiento));
        return fecha.toISOString().slice(0, 16);
    }
}">

    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Cliente</label>
        <x-select-dinamico
            label="Cliente"
            placeholder="Seleccione un cliente"
            model="pedido.cliente_id"
            :options="$clientes"
            idKey="id"
            textKey="razon_social"
            selectId="select-cliente"
        />
    </div>

<div class="grid grid-cols-3 gap-4">
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Fecha</label>
            <input type="datetime-local"
                :value="formatDateForInput(pedido.fecha)"
                @input="pedido.fecha = $event.target.value"
                class="input-pedido"
                @change="console.log('Fecha actual:', pedido.fecha)" />
    </div>
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Días Plazo Vencimiento</label>
        <input type="number" x-model="pedido.dias_plazo_vencimiento" class="input-pedido" />
    </div>
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Fecha Vencimiento</label>
        <input type="datetime-local"
            :value="calcularFechaVencimiento(pedido.fecha, pedido.dias_plazo_vencimiento)"
            @input="pedido.fecha_vencimiento = $event.target.value"
            class="input-pedido" readonly />
    </div>
</div>
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Ciudad</label>
        <input type="text" x-model="pedido.ciudad" class="input-pedido" />
    </div>
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Método de Pago</label>
        <select x-model="pedido.metodo_pago" class="input-pedido-select" >
            <option value="">Seleccione un método de pago</option>
            <option value="CREDITO">CREDITO</option>
            <option value="CONTADO">CONTADO</option>
        </select>
    </div>
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Tipo Precio</label>
        <select x-model="pedido.tipo_precio" class="input-pedido-select" @change="actualizarTodosLosDetalles()">
            <option value="FERRETERO">FERRETERO</option>
            <option value="MAYORISTA">MAYORISTA</option>
            <option value="DETAL">DETAL</option>
        </select>
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Estado</label>
        <select x-model="pedido.estado" class="input-pedido-select">
            <option value="PENDIENTE">Pendiente</option>
            <option value="FACTURADO">Facturado</option>
            <option value="ANULADO">Anulado</option>
        </select>
    </div>
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Tipo Venta</label>
        <select x-model="pedido.tipo_venta" class="input-pedido-select">
            <option value="ELECTRONICA">ELECTRONICA</option>
            <option value="REMISIONADA">REMISIONADA</option>
        </select>
    </div>
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Bodega</label>
        <select x-model="pedido.bodega_id" class="input-pedido-select">
            <option value="">Seleccione una bodega</option>
            <template x-for="bodega in bodegas" :key="bodega.id">
                <option :value="bodega.id" x-text="bodega.nombre_bodega"></option>
            </template>
        </select>
    </div>
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Alistador</label>
        <select x-model="pedido.alistador_id" class="input-pedido-select">
            <option value="">Seleccione un Alistador</option>
            <template x-for="alistador in alistadores" :key="alistador.id">
                <option :value="alistador.id" x-text="alistador.name"></option>
            </template>
        </select>
    </div>
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Vendedor</label>
        <select x-model="pedido.user_id" class="input-pedido-select">
            <option value="">Seleccione un Vendedor</option>
            <template x-for="user in users" :key="user.id">
                <option :value="user.id" x-text="user.name"></option>
            </template>
        </select>
    </div>
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">F.E.</label>
        <input type="text" x-model="pedido.fe" class="input-pedido" />
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Primer Comentario</label>
        <textarea x-model="pedido.primer_comentario" class="input-pedido"></textarea>
    </div>
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Segundo Comentario</label>
        <textarea x-model="pedido.segundo_comentario" class="input-pedido"></textarea>
    </div>
</div>

