<div class="grid grid-cols-2 gap-4">
    {{-- <div>
        <label>Código</label>
        <input type="text" x-model="pedido.codigo" class="border rounded w-full" />
    </div> --}}
    <div>
        <label>Estado Venta</label>
        <select x-model="pedido.estado_venta" class="border rounded w-full">
            <option value="COTIZACION">COTIZACION</option>
            <option value="VENTA">VENTA</option>
        </select>
    </div>
    <div>
        <label>Cliente</label>
        <select x-model="pedido.cliente_id" class="border rounded w-full">
            <option value="">Seleccione un cliente</option>
            <template x-for="cliente in clientes" :key="cliente.id">
                <option :value="cliente.id" x-text="cliente.razon_social"></option>
            </template>
        </select>
    </div>
    <div>
        <label>Fecha</label>
        <input type="datetime-local" x-model="pedido.fecha" class="border rounded w-full" />
    </div>
    <div>
        <label>Días Plazo Vencimiento</label>
        <input type="number" x-model="pedido.dias_plazo_vencimiento" class="border rounded w-full" />
    </div>
    <div>
        <label>Fecha Vencimiento</label>
        <input type="datetime-local" x-model="pedido.fecha_vencimiento" class="border rounded w-full" />
    </div>
    <div>
        <label>Ciudad</label>
        <input type="text" x-model="pedido.ciudad" class="border rounded w-full" />
    </div>
    <div>
        <label>Método de Pago</label>
        <select x-model="pedido.metodo_pago" class="border rounded w-full">
            <option value="CREDITO">CREDITO</option>
            <option value="CONTADO">CONTADO</option>
        </select>
    </div>
    <div>
        <label>Tipo Precio</label>
        <select x-model="pedido.tipo_precio" class="border rounded w-full">
            <option value="FERRETERO">FERRETERO</option>
            <option value="MAYORISTA">MAYORISTA</option>
            <option value="DETAL">DETAL</option>
        </select>
    </div>

    <div>
        <label>Estado</label>
        <select x-model="pedido.estado" class="border rounded w-full">
            <option value="">Seleccione Estado</option>
            <option value="PENDIENTE">Pendiente</option>
            <option value="FACTURADO">Facturado</option>
            <option value="ANULADO">Anulado</option>
        </select>
    </div>
    <div>
        <label>Tipo Venta</label>
        <select x-model="pedido.tipo_venta" class="border rounded w-full">
            <option value="ELECTRONICA">ELECTRONICA</option>
            <option value="REMISIONADA">REMISIONADA</option>
        </select>
    </div>
    <div>
        <label>Bodega</label>
        <select x-model="pedido.bodega_id" class="border rounded w-full">
            <option value="">Seleccione una bodega</option>
            <template x-for="bodega in bodegas" :key="bodega.id">
                <option :value="bodega.id" x-text="bodega.name ?? bodega.nombre_bodega"></option>
            </template>
        </select>
    </div>
    <div>
        <label>Alistador</label>
        <select x-model="pedido.alistador_id" class="border rounded w-full">
            <option value="">Seleccione un Alistador</option>
            <template x-for="alistador in alistadores" :key="alistador.id">
                <option :value="alistador.id" x-text="alistador.name"></option>
            </template>
        </select>
    </div>
    <div>
        <label>Vendedor</label>
        <select x-model="pedido.user_id" class="border rounded w-full">
            <option value="">Seleccione un Vendedor</option>
            <template x-for="user in users" :key="user.id">
                <option :value="user.id" x-text="user.name"></option>
            </template>
        </select>
    </div>

    <div>
        <label>Primer Comentario</label>
        <input type="text" x-model="pedido.primer_comentario" class="border rounded w-full" />
    </div>

</div>
