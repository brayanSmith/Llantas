// Archivo de pedidos.js generado automáticamente para evitar error de build.
export const sumar = (a, b) => a + b;

function crearPedidoVacio(bodegaSeleccionada, empresa, userId) {
	return {
		codigo: '',
		fe: '',
		cliente_id: null,
		fecha: '',
		dias_plazo_vencimiento: 30,
		fecha_vencimiento: '',
		ciudad: '',
		estado: 'PENDIENTE',
		stock_retirado: false,
		en_cartera: false,
		metodo_pago: 'CREDITO',
		tipo_precio: 'FERRETERO',
		tipo_venta: 'ELECTRONICA',
		estado_pago: 'EN_CARTERA',
		estado_cartera: 'CARTERA_AL_DIA',
		estado_venta: 'VENTA',
		estado_vencimiento: 'AL_DIA',
		bodega_id: bodegaSeleccionada ?? (empresa ? empresa.bodega_id : null),
		primer_comentario: '',
		subtotal: 0,
		abono: 0,
		descuento: 0,
		flete: 0,
		total_a_pagar: 0,
		saldo_pendiente: 0,
		user_id: userId,
		alistador_id: userId,
		detalles: [],
		created_at: '',
		updated_at: '',
		iva: 0
	};
}
window.crearPedidoVacio = crearPedidoVacio;



