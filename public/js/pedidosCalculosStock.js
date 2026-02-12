// Funciones de cálculo de stock reutilizables para pedidos

function changeStockDisplay(producto, cantidad, accion) {
    if (producto._stock_base === undefined || producto._stock_base === null) {
        producto._stock_base = Number(producto.stock);
    }
    const stockBase = Number(producto._stock_base);
    let stockTotal = stockBase;
    cantidad = Number(cantidad) || 0;
    if (accion === 'agregar') {
        stockTotal = stockBase - cantidad;
    } else if (accion === 'remover') {
        stockTotal = stockBase;
    } else if (accion === 'actualizar') {
        stockTotal = stockBase - cantidad;
    }
    return stockTotal;
}

window.changeStockDisplay = changeStockDisplay;
