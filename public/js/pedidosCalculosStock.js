// Funciones de cálculo de stock reutilizables para pedidos

function changeStockDisplay(producto, cantidad, accion) {
    const stockActual = Number(producto.stock);
    let stockTotal = stockActual;
    cantidad = Number(cantidad) || 0;
    if (accion === 'agregar') {
        stockTotal = stockActual - cantidad;
    } else if (accion === 'remover') {
        stockTotal = stockActual;
    } else if (accion === 'actualizar') {
        stockTotal = stockActual - cantidad; // O la lógica que necesites
    }
    return stockTotal;
}

window.changeStockDisplay = changeStockDisplay;
