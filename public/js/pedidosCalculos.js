// Funciones de cálculo reutilizables para pedidos

function getPrecio(productoSeleccionado, tipoPrecio) {
    const prod = productoSeleccionado;
    if (!prod) return 0;
    switch (tipoPrecio) {
        case 'DETAL': return prod.valor_detal ?? 0;
        case 'MAYORISTA': return prod.valor_mayorista ?? 0;
        //case 'OTRO': return prod.valor_otro ?? 0;
        default: return prod.valor_detal ?? 0;
    }
}

function getPrecioConIva(productoSeleccionado, precio, aplicarIva) {
    const prod = productoSeleccionado
    if (aplicarIva) {
        precioConIva = precio;
    }else{precioConIva = precio;
    }
    return Math.round(precioConIva * 100) / 100;
}

function getSubtotal(precio, cantidad) {
    return Math.round(precio * cantidad * 100) / 100;
}

function getTotal(pedido) {
    const total = pedido.detalles.reduce((acc, detalle) => acc + (detalle.subtotal || 0), 0);
    pedido.subtotal = total;
    return total;
}

function getTotalAPagar(pedido) {
    const subtotal = parseFloat(pedido.subtotal) || 0;
    const flete = parseFloat(pedido?.flete) || 0;
    const descuento = parseFloat(pedido?.descuento) || 0;
    const totalAPagar = subtotal + flete - descuento;
    if (pedido) pedido.total_a_pagar = totalAPagar;
    return totalAPagar;
}

function getSaldoPendiente(pedido) {
    const totalAPagar = parseFloat(pedido.total_a_pagar || 0);
    const abono = parseFloat(pedido?.abono) || 0;
    const saldoPendiente = totalAPagar - abono;
    if (pedido) pedido.saldo_pendiente = saldoPendiente;
    return saldoPendiente;
}

window.getPrecio = getPrecio;
window.getPrecioConIva = getPrecioConIva;
window.getSubtotal = getSubtotal;
window.getTotal = getTotal;
window.getTotalAPagar = getTotalAPagar;
window.getSaldoPendiente = getSaldoPendiente;
