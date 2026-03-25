<div x-data="productoForm({
    producto: @js($productoEncontrado),
    categorias: @js($categorias),
    marcas: @js($marcas),
    atributoProductos: @js($atributoProductos),
})"
    x-watch="producto.categoria_id"
    x-init="init(); cargarAtributosLocales()"
    @select-changed.window="actualizarConcatenacion()"
    class="space-y-4">
    <div class="space-y-6">
        @include('livewire.productos.livewire-producto-categoria')
        @include('livewire.productos.livewire-producto-atributos')
        @include('livewire.productos.livewire-producto-create')
        <div class="flex justify-end gap-4 pt-6 border-t border-gray-200 dark:border-gray-700">
            <!-- Botón Cancelar -->
            <button
                type="button"
                class="inline-flex items-center gap-2 px-6 py-3 bg-white dark:bg-gray-800 border-2 border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 font-semibold hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-400 dark:hover:border-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed shadow-sm hover:shadow-md"
                @click="window.history.back()"
                :disabled="enviando">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>Cancelar</span>
            </button>

            <!-- Botón Guardar -->
            <button
                type="button"
                class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 dark:from-blue-500 dark:to-blue-600 hover:from-blue-700 hover:to-blue-800 dark:hover:from-blue-600 dark:hover:to-blue-700 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 disabled:opacity-60 disabled:cursor-not-allowed min-w-[180px] justify-center"
                @click="enviar()"
                :disabled="enviando">
                <!-- Spinner -->
                <svg x-show="enviando" class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <!-- Icono de guardar -->
                <svg x-show="!enviando" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span x-text="enviando ? 'Guardando...' : 'Guardar Producto'"></span>
            </button>
        </div>
    </div>
</div>
<script>
    function productoForm({ producto, categorias, marcas, atributoProductos }) {
        return {
            producto: producto || {
                nombre_producto: '',
                descripcion: '',
                precio: '',
                stock: '',
                categoria_id: null,
                marca_id: null,
                atributo_productos: {},
                categoriaSeleccionada: null,
            },
            categorias: categorias || [],
            marcas: marcas || [],
            atributos: [],
            lastCategoriaId: null,
            enviando: false,
            init() {
                const existing = this.producto.atributo_productos;
                if (Array.isArray(existing)) {
                    const map = {};
                    existing.forEach((item) => {
                        if (item && item.atributo_id !== undefined) {
                            map[item.atributo_id] = item.valor ?? '';
                        }
                    });
                    this.producto.atributo_productos = map;
                } else if (!existing || Object.keys(existing).length === 0) {
                    this.producto.atributo_productos = atributoProductos || {};
                }
                // Inicializar atributos si ya hay categoría seleccionada
                this.cargarAtributosLocales();

                // Watcher para actualizar referencia automáticamente
                this.$watch('producto.atributo_productos', () => {
                    this.actualizarReferencia();
                });

                // Watchers para actualizar concatenación automáticamente
                this.$watch('producto.referencia_producto', () => {
                    this.actualizarConcatenacion();
                });
                this.$watch('producto.marca_id', () => {
                    this.actualizarConcatenacion();
                });
                this.$watch('producto.descripcion_producto', () => {
                    this.actualizarConcatenacion();
                });
            },
            cargarAtributosLocales() {
                if (this.producto.categoria_id) {
                    const categoriaSeleccionada = this.categorias.find(cat => cat.id === this.producto.categoria_id);
                    if (categoriaSeleccionada && categoriaSeleccionada.atributos) {
                        this.atributos = categoriaSeleccionada.atributos;
                        if (this.lastCategoriaId !== null && this.lastCategoriaId !== this.producto.categoria_id) {
                            // Resetear los valores de atributos al cambiar de categoría
                            this.producto.atributo_productos = {};
                        }
                        this.lastCategoriaId = this.producto.categoria_id;
                        console.log('Atributos de la categoría:', this.atributos);

                        // Inicializar valores por defecto y actualizar referencia
                        this.$nextTick(() => {
                            this.actualizarReferencia();
                        });
                    }
                } else {
                    this.atributos = [];
                    this.producto.atributo_productos = {};
                    this.lastCategoriaId = null;
                }
            },
            parseOpciones(opcionesString) {
                if (!opcionesString) return [];
                try {
                    let result = [];
                    let cleaned = String(opcionesString).trim();

                    // Intentar parsear como JSON
                    try {
                        let parsed = JSON.parse(cleaned);

                        // Si es array dentro de array, desempacar
                        if (Array.isArray(parsed) && parsed.length > 0 && typeof parsed[0] === 'string') {
                            try {
                                parsed = JSON.parse(parsed[0]);
                            } catch (e) {
                                // No es JSON válido, manejarlo después
                            }
                        }

                        // Si es array, procesarlo
                        if (Array.isArray(parsed)) {
                            result = parsed.map(item => {
                                let str = String(item);
                                // Limpiar comillas escapadas y corchetes
                                return str.replace(/\\+"/g, '"').replace(/^\["|"\]$/g, '').replace(/^"|"$/g, '').trim();
                            });
                        } else {
                            result = [String(parsed)];
                        }
                    } catch (parseErr) {
                        // Si JSON.parse falla, intentar regex para extraer valores
                        const matches = cleaned.match(/"([^"\\]|\\.)*"/g) || [];
                        result = matches.map(m => m.replace(/^"|"$/g, '').replace(/\\"/g, '"'));
                    }

                    return result;
                } catch (e) {
                    console.error('Error parsing opciones:', opcionesString);
                    return [];
                }
            },
            actualizarReferencia() {
                if (this.atributos.length === 0) {
                    return;
                }

                // Concatenar valores de atributos en orden
                let referencia = '';
                this.atributos.forEach((atributo) => {
                    const valor = this.producto.atributo_productos[atributo.id];
                    if (valor !== null && valor !== undefined && String(valor).trim() !== '') {
                        referencia += String(valor);
                    }
                });

                this.producto.referencia_producto = referencia;
            },
            actualizarConcatenacion() {
                let concatenacion = '';

                // Agregar referencia
                if (this.producto.referencia_producto) {
                    concatenacion += this.producto.referencia_producto;
                }

                // Agregar marca
                if (this.producto.marca_id) {
                    const marca = this.marcas.find(m => String(m.id) === String(this.producto.marca_id));
                    if (marca && marca.marca) {
                        concatenacion += (concatenacion ? '-' : '') + marca.marca;
                    }
                }

                // Agregar descripción
                if (this.producto.descripcion_producto) {
                    concatenacion += (concatenacion ? '-' : '') + this.producto.descripcion_producto;
                }

                this.producto.concatenar_codigo_nombre = concatenacion;
                console.log('Concatenación actualizada:', concatenacion);
            },
            enviar() {
                if (!this.producto.categoria_id) {
                    alert('Por favor, seleccione una categoría.');
                    return;
                }
                if (!this.producto.marca_id) {
                    alert('Por favor, seleccione una marca.');
                    return;
                }
                if (!this.producto.referencia_producto || this.producto.referencia_producto.trim() === '') {
                    alert('Por favor, ingrese una referencia para el producto.');
                    return;
                }
                if (!this.producto.descripcion_producto || this.producto.descripcion_producto.trim() === '') {
                    alert('Por favor, ingrese una descripción para el producto.');
                    return;
                }
                if (this.producto.costo_producto === null || this.producto.costo_producto === undefined || this.producto.costo_producto === '') {
                    alert('Por favor, ingrese un costo para el producto.');
                    return;
                }
                if (this.producto.valor_detal === null || this.producto.valor_detal === undefined || this.producto.valor_detal === '') {
                    alert('Por favor, ingrese un valor detal para el producto.');
                    return;
                }
                if (this.producto.valor_mayorista === null || this.producto.valor_mayorista === undefined || this.producto.valor_mayorista === '') {
                    alert('Por favor, ingrese un valor mayorista para el producto.');
                    return;
                }
                if (this.producto.valor_sin_instalacion === null || this.producto.valor_sin_instalacion === undefined || this.producto.valor_sin_instalacion === '') {
                    alert('Por favor, ingrese un valor sin instalación para el producto.');
                    return;
                }

                this.enviando = true;

                const payload = JSON.parse(JSON.stringify(this.producto));
                const atributos = payload.atributo_productos || {};
                payload.atributo_productos = Object.entries(atributos)
                    .filter(([, valor]) => valor !== null && valor !== undefined && String(valor).trim() !== '')
                    .map(([atributoId, valor]) => ({
                        atributo_id: Number(atributoId),
                        valor: valor,
                    }));
                console.log('Producto payload:', payload);

                this.$wire.guardarProducto(payload)
                    .then(() => {
                        this.enviando = false;
                        window.history.back();
                    })
                    .catch((error) => {
                        this.enviando = false;
                        console.error('Error al guardar producto:', error);
                    });
            }
        }
    }
</script>
