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

function enviarPedidoReutilizable(pedido, guardarPedidoWireFn) {
    if (!pedido.detalles || pedido.detalles.length === 0) {
        // Mostrar toast o alert según contexto
        alert('El carrito está vacío. Agregue al menos un producto antes de enviar el pedido.');
        return;
    }
    if (!pedido.cliente_id) {
        alert('Debe seleccionar un cliente antes de enviar el pedido.');
        return;
    }
    const errores = validarDetalles(pedido.detalles);
    if (errores.length > 0) {
        alert(errores.join('\n'));
        return;
    }
    console.log('JSON generado para enviar:', JSON.stringify(pedido, null, 2));
    guardarPedidoWireFn(pedido);
    localStorage.removeItem('pedidoPOS');
}

function agregarDetalleReutilizable(pedido, productoSeleccionado, cantidadSeleccionada, precioUnitario, aplicarIva, precioConIva, subTotal, mostrarToastFn) {
    if (!productoSeleccionado) {
        if (typeof mostrarToastFn === 'function') {
            mostrarToastFn('Debe seleccionar un producto válido.');
        } else {
            alert('Debe seleccionar un producto válido.');
        }
        return;
    }
    const detalles = pedido.detalles;
    const yaExiste = detalles.some(
        d => d.producto_id === productoSeleccionado.id
    );
    if (yaExiste) {
        if (typeof mostrarToastFn === 'function') {
            mostrarToastFn('Este producto ya se encuentra en el carrito. Si desea modificar la cantidad, hágalo manualmente desde ahí.');
        } else {
            alert('Este producto ya se encuentra en el carrito. Si desea modificar la cantidad, hágalo manualmente desde ahí.');
        }
        return;
    }
    const detalle = {
        producto_id: productoSeleccionado.id,
        cantidad: cantidadSeleccionada,
        precio_unitario: precioUnitario,
        aplicar_iva: aplicarIva,
        iva: productoSeleccionado.iva_producto || 0,
        precio_con_iva: precioConIva,
        subtotal: subTotal
    };
    detalles.push(detalle);
    // ...resto del código...
}

function removeDetalleReutilizable(pedido, index, setTotalCantidadProductosFn) {
	pedido.detalles.splice(index, 1);
	if (typeof setTotalCantidadProductosFn === 'function') {
		setTotalCantidadProductosFn(pedido.detalles.reduce((acc, d) => acc + (parseFloat(d.cantidad) || 0), 0));
	}
}

function actualizarCantidadReutilizable(pedido, index, setTotalCantidadProductosFn) {
	// Puedes agregar lógica adicional aquí si lo necesitas
	if (typeof setTotalCantidadProductosFn === 'function') {
		setTotalCantidadProductosFn(pedido.detalles.reduce((acc, d) => acc + (parseFloat(d.cantidad) || 0), 0));
	}
}

// Función reutilizable para validar detalles de un pedido
function validarDetalles(detalles) {
	let errores = [];
	detalles.forEach((detalle, idx) => {
		if (!detalle.producto_id) {
			errores.push(`Debe seleccionar un producto en la fila ${idx + 1}`);
		}
		if (!detalle.cantidad || detalle.cantidad < 1) {
			errores.push(`La cantidad debe ser mayor a 0 en la fila ${idx + 1}`);
		}
	});
	return errores;
}
window.validarDetalles = validarDetalles;
window.actualizarCantidadReutilizable = actualizarCantidadReutilizable;
window.crearPedidoVacio = crearPedidoVacio;
window.enviarPedidoReutilizable = enviarPedidoReutilizable;
window.agregarDetalleReutilizable = agregarDetalleReutilizable;
window.removeDetalleReutilizable = removeDetalleReutilizable;
