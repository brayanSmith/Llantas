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
        <x-select-dinamico label="Cliente" placeholder="Seleccione un cliente" model="pedido.cliente_id" :options="$clientes"
            idKey="id" textKey="razon_social" selectId="select-cliente" />
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Fecha</label>
            <input type="datetime-local" :value="formatDateForInput(pedido.fecha)"
                @input="pedido.fecha = $event.target.value" class="input-pedido"
                @change="console.log('Fecha actual:', pedido.fecha)" />
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Tipo Precio</label>
            <select x-model="pedido.tipo_precio" class="input-pedido-select" @change="actualizarTodosLosDetalles()">
                <option value="FERRETERO">FERRETERO</option>
                <option value="MAYORISTA">MAYORISTA</option>
                <option value="DETAL">DETAL</option>
            </select>
        </div>
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
