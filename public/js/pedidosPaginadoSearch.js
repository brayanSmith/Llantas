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

// Funciones para manejo de productos por página en localStorage
function getProductosPorPagina(clienteId) {
    const key = `productosPorPagina_${clienteId || 'default'}`;
    const stored = localStorage.getItem(key);
    return stored ? parseInt(stored, 10) : 12; // valor por defecto: 12
}

function setProductosPorPagina(clienteId, valor) {
    const key = `productosPorPagina_${clienteId || 'default'}`;
    localStorage.setItem(key, valor.toString());
}

function getPaginasArray(totalPaginas) {
    const paginas = [];
    for (let i = 1; i <= totalPaginas; i++) {
        paginas.push(i);
    }
    return paginas;
}

function productosFiltradosPaginados() {
    return {
        getProductosFiltrados,
        getTotalPaginasProductos,
        getProductosPaginados,
        getProductosFiltradosPaginados,
        getTotalPaginasProductosFiltrados,
        getProductosPorPagina,
        setProductosPorPagina,
        getPaginasArray
    };
}

window.productosFiltradosPaginados = productosFiltradosPaginados();
