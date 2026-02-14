<div class="grid grid-cols-2 gap-4" >
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Factura</label>
        <input type="text" x-model="compra.factura" class="input-pedido" />
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Proveedor</label>
        <x-select-dinamico label="Proveedor" placeholder="Seleccione un proveedor" model="compra.proveedor_id"
            :options="$proveedores" idKey="id" textKey="nombre_proveedor" selectId="select-proveedor" />
    </div>

    <div class="grid grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Fecha</label>
            <input type="datetime-local" :value="formatDateForInput(compra.fecha)" @input="compra.fecha = $event.target.value" class="input-pedido" />
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Plazo</label>
            <input type="number" x-model="compra.dias_plazo_vencimiento" class="input-pedido" />
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Fecha Vencimiento</label>
            <input type="datetime-local" :value="calcularFechaVencimiento(compra.fecha, compra.dias_plazo_vencimiento)"
             class="input-pedido" readonly />
        </div>
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Método de Pago</label>
        <select x-model="compra.metodo_pago" class="input-pedido-select">
            <option value="">Seleccione un método de pago</option>
            <option value="CREDITO">CREDITO</option>
            <option value="CONTADO">CONTADO</option>
        </select>
    </div>
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Estado</label>
        <select x-model="compra.estado" class="input-pedido-select">
            <option value="PENDIENTE">PENDIENTE</option>
            <option value="FACTURADO">FACTURADO</option>
            <option value="ANULADO">ANULADO</option>
        </select>
    </div>
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Tipo Venta</label>
        <select x-model="compra.tipo_venta" class="input-pedido-select">
            <option value="ELECTRONICA">ELECTRONICA</option>
            <option value="REMISIONADA">REMISIONADA</option>
        </select>
    </div>
   <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Bodega</label>
        <select x-model="compra.bodega_id" class="input-pedido-select">
            <option value="">Seleccione una bodega</option>
            <template x-for="bodega in bodegas" :key="bodega.id">
                <option :value="bodega.id" x-text="bodega.nombre_bodega"></option>
            </template>
        </select>
    </div>
    <div class="col-span-2">
        <label class="block text-sm font-semibold text-gray-700 mb-1">Observaciones</label>
        <textarea x-model="compra.observaciones" class="input-pedido"></textarea>
    </div>
</div>
