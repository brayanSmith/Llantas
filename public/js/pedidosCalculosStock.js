// Funciones de cálculo de stock reutilizables para pedidos

function getStockDisponible(stockBodegas, idProducto, idBodega) {
    const stockEntry = stockBodegas.find(entry =>
        entry.bodega_id === idBodega && entry.producto_id === idProducto
    );
    return stockEntry ? stockEntry.stock : 0;
}

function getStockTotal(stockBodegas, idProducto, idBodega, cantidad, accion) {
    let stockInicial = Number(getStockDisponible(stockBodegas, idProducto, idBodega));
    let stockTotal = stockInicial;
    cantidad = Number(cantidad) || 0;
    if (accion === 'agregar') {
        stockTotal = stockInicial - cantidad;
    } else if (accion === 'remover') {
        stockTotal = stockInicial;
    } else if (accion === 'actualizar') {
        // Si tienes un valor especial para actualizar, pon la lógica aquí
        stockTotal = stockInicial - cantidad; // O la lógica que necesites
    }
    return stockTotal;
}

window.getStockDisponible = getStockDisponible;
window.getStockTotal = getStockTotal;
