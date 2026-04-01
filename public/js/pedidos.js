function crearPedidoVacio(bodegaSeleccionada, empresa, userId, tipoPrecio) {
    return {
        codigo: "",
        cliente_id: null,
        fecha: "",
        estado: "PENDIENTE",
        estado_pago: null,
        tipo_pago: "CONTADO",
        tipo_precio: tipoPrecio,
        puc_id: null,
        bodega_id: bodegaSeleccionada ?? (empresa ? empresa.bodega_id : null),
        observacion: "",
        observacion_pago: "",
        subtotal: 0,
        abono: 0,
        descuento: 0,
        flete: 0,
        total_a_pagar: 0,
        saldo_pendiente: 0,
        user_id: userId,
        aplica_turno: true,
        turno: null,
        detalles: [],
    };
}

function validarRegistros(pedido){
    let errores = [];
    if (!pedido.cliente_id) {
        errores.push("Debe seleccionar un cliente.");
    }
    if (!pedido.tipo_pago) {
        errores.push("Debe seleccionar un tipo de pago.");
    }
    if(!pedido.id_puc){
        errores.push("Debe seleccionar un medio de pago.");
    }

    if (!pedido.tipo_precio) {
        errores.push("Debe seleccionar un tipo de precio.");
    }
    if (!pedido.con_cuanto_paga && pedido.tipo_pago !== 'CONTRA_ENTREGA') {
        errores.push("Debe ingresar con cuánto paga el cliente.");
    }

    if (pedido.detalles.length === 0) {
        errores.push("Debe agregar al menos un producto al pedido.");
    } else {
        pedido.detalles.forEach((detalle, idx) => {
            if (!detalle.producto_id) {
                errores.push(`Debe seleccionar un producto en la fila ${idx + 1}`);
            }
            if (!detalle.cantidad || detalle.cantidad < 1) {
                errores.push(
                    `La cantidad debe ser mayor a 0 en la fila ${idx + 1}`,
                );
            }
        });
    }
    return errores;
}


function agregarDetalleReutilizable(
    pedido,
    productoSeleccionado,
    cantidadSeleccionada,
    precioUnitario,//
    subTotal,
    mostrarToastFn,
) {
    if (!productoSeleccionado) {
        if (typeof mostrarToastFn === "function") {
            mostrarToastFn("Debe seleccionar un producto válido.");
        } else {
            alert("Debe seleccionar un producto válido.");
        }
        return;
    }
    const detalles = pedido.detalles;
    const yaExiste = detalles.some(
        (d) => d.producto_id === productoSeleccionado.id,
    );
    if (yaExiste) {
        if (typeof mostrarToastFn === "function") {
            mostrarToastFn(
                "Este producto ya se encuentra en el carrito. Si desea modificar la cantidad, hágalo manualmente desde ahí.",
            );
        } else {
            alert(
                "Este producto ya se encuentra en el carrito. Si desea modificar la cantidad, hágalo manualmente desde ahí.",
            );
        }
        return;
    }
    // Actualiza el stock del producto seleccionado visualmente
    if (typeof changeStockDisplay === "function") {
        productoSeleccionado.stock = changeStockDisplay(
            productoSeleccionado,
            cantidadSeleccionada,
            "agregar",
        );
    }
    const detalle = {
        producto_id: productoSeleccionado.id,
        cantidad: cantidadSeleccionada,
        precio_unitario: precioUnitario,
        subtotal: subTotal,
    };
    detalles.push(detalle);
    console.log("Detalle agregado:", detalle);

    // Recalcular todos los totales del pedido
    if (typeof getTotal === "function") {
        pedido.subtotal = getTotal(pedido);
    }
    if (typeof getTotalAPagar === "function") {
        pedido.total_a_pagar = getTotalAPagar(pedido);
    }
    if (typeof getSaldoPendiente === "function") {
        pedido.saldo_pendiente = getSaldoPendiente(pedido);
    }
}

function actualizarCantidadReutilizable(
    pedido,
    index,
    setTotalCantidadProductosFn,
) {
    // Recalcular el subtotal del detalle modificado
    const detalle = pedido.detalles[index];
    if (detalle) {
        detalle.subtotal = detalle.precio_unitario * detalle.cantidad;
    }

    // Recalcular todos los totales del pedido
    if (typeof getTotal === "function") {
        pedido.subtotal = getTotal(pedido);
    }
    if (typeof getTotalAPagar === "function") {
        pedido.total_a_pagar = getTotalAPagar(pedido);
    }
    if (typeof getSaldoPendiente === "function") {
        pedido.saldo_pendiente = getSaldoPendiente(pedido);
    }

    // Actualizar el total de cantidad de productos
    if (typeof setTotalCantidadProductosFn === "function") {
        setTotalCantidadProductosFn(
            pedido.detalles.reduce(
                (acc, d) => acc + (parseFloat(d.cantidad) || 0),
                0,
            ),
        );
    }
}

function removeDetalleReutilizable(pedido, index, setTotalCantidadProductosFn) {
    // Eliminar el detalle
    pedido.detalles.splice(index, 1);

    // Recalcular todos los totales del pedido
    if (typeof getTotal === "function") {
        pedido.subtotal = getTotal(pedido);
    }
    if (typeof getTotalAPagar === "function") {
        pedido.total_a_pagar = getTotalAPagar(pedido);
    }
    if (typeof getSaldoPendiente === "function") {
        pedido.saldo_pendiente = getSaldoPendiente(pedido);
    }

    // Actualizar el total de cantidad de productos
    if (typeof setTotalCantidadProductosFn === "function") {
        setTotalCantidadProductosFn(
            pedido.detalles.reduce(
                (acc, d) => acc + (parseFloat(d.cantidad) || 0),
                0,
            ),
        );
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
            errores.push(
                `La cantidad debe ser mayor a 0 en la fila ${idx + 1}`,
            );
        }
    });
    return errores;
}
window.validarDetalles = validarDetalles;
window.actualizarCantidadReutilizable = actualizarCantidadReutilizable;
window.crearPedidoVacio = crearPedidoVacio;
window.validarRegistros = validarRegistros;
window.agregarDetalleReutilizable = agregarDetalleReutilizable;
window.removeDetalleReutilizable = removeDetalleReutilizable;
