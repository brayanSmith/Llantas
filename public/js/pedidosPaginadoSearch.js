//Funcion de Busqueda
function getProductosFiltrados(productos, search) {
    if (!search) return productos;
    const palabras = search.toLowerCase().split(/\s+/).filter(Boolean);
    return productos.filter(p => {
        const textoProducto = [
            p.nombre_producto,
            p.codigo_producto,
            p.concatenar_codigo_nombre
        ].filter(Boolean).join(' ').toLowerCase();
        return palabras.every(palabra => textoProducto.includes(palabra));
    });
}

// Funciones de paginado
function getTotalPaginasProductos(productos, productosPorPagina) {
    return Math.ceil(productos.length / productosPorPagina);
}
//
function getProductosPaginados(productos, paginaProductos, productosPorPagina) {
    const inicio = (paginaProductos - 1) * productosPorPagina;
    return productos.slice(inicio, inicio + productosPorPagina);
}
//
function getProductosFiltradosPaginados(productos, search, paginaProductos, productosPorPagina) {
    const filtrados = getProductosFiltrados(productos, search);
    const inicio = (paginaProductos - 1) * productosPorPagina;
    return filtrados.slice(inicio, inicio + productosPorPagina);
}
//
function getTotalPaginasProductosFiltrados(productos, search, productosPorPagina) {
    const filtrados = getProductosFiltrados(productos, search);
    return Math.ceil(filtrados.length / productosPorPagina);
}

function productosFiltradosPaginados() {
    return {
        getProductosFiltrados,
        getTotalPaginasProductos,
        getProductosPaginados,
        getProductosFiltrados,
        getProductosFiltradosPaginados,
        getTotalPaginasProductosFiltrados
    };
}

window.productosFiltradosPaginados = productosFiltradosPaginados();
