<div x-data="productoForm({
    producto: @js($productoEncontrado),
    marcas: @js($marcas),
})"
    x-init="init()"
    @select-changed.window="actualizarConcatenacion()"
    class="space-y-4">
    <div class="space-y-6">
        <!-- Primera sección: Categoría/Tipo/Inventariable -->
        @include('livewire.productos.livewire-producto-categoria')

        <!-- Segunda sección: Atributos y Imagen lado a lado -->
        <div class="grid grid-cols-1 lg:grid-cols-10 gap-6">
            <div class="lg:col-span-7">
                <div x-show="producto.categoria === 'LLANTA' && (producto.tipo === 'NUEVO' || producto.tipo === 'USADO')" x-cloak>
                    @include('livewire.productos.categorias.livewire-productos-categoria-llantas')
                </div>
                <div x-show="producto.categoria === 'RIN' && (producto.tipo === 'NUEVO' || producto.tipo === 'USADO')" x-cloak>
                    @include('livewire.productos.categorias.livewire-productos-categoria-rines')
                </div>
                <div x-show="producto.categoria === 'OTRO' && (producto.tipo === 'NUEVO' || producto.tipo === 'USADO')" x-cloak>
                    @include('livewire.productos.categorias.livewire-productos-categoria-otros')
                </div>
                <div x-show="producto.categoria === 'SERVICIO'" x-cloak>
                    @include('livewire.productos.categorias.livewire-productos-categoria-servicios')
                </div>
            </div>
            <div class="lg:col-span-3">
                @include('livewire.productos.livewire-producto-imagen')
            </div>
        </div>

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
    function productoForm({ producto, marcas }) {
        return {
            producto: producto || {
                categoria: '',
                tipo: '',
                inventariable: false,
                referencia_producto: '',
                nombre_producto: '',
                descripcion: '',
                precio: '',
                stock: '',
                marca_id: null,
                //categoriaSeleccionada: null,
            },
            marcas: marcas || [],
            enviando: false,
            init() {
                // Watchers para actualizar concatenación automáticamente
                const camposReferencia = [
                    'producto.ancho',
                    'producto.perfil',
                    'producto.construccion',
                    'producto.rin',
                    'producto.diametro',
                    'producto.categoria',
                    'producto.tipo',
                ];
                camposReferencia.forEach((campo) => {
                    this.$watch(campo, () => {
                        this.actualizarReferencia();
                    });
                });

                this.$watch('producto.tipo', () => {
                    this.producto.ancho = '';
                    this.producto.perfil = '';
                    this.producto.construccion = '';
                    this.producto.rin = '';
                    this.producto.diametro = '';
                    this.producto.referencia_producto = '';
                    this.producto.descripcion_producto = '';
                    this.producto.marca_id = null;
                    this.actualizarConcatenacion();
                });

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

            actualizarReferencia() {
                const ancho = String(this.producto.ancho ?? '').trim();
                const perfil = String(this.producto.perfil ?? '').trim();
                const construccion = String(this.producto.construccion ?? '').trim();
                const rin = String(this.producto.rin ?? '').trim();
                const diametro = String(this.producto.diametro ?? '').trim();
                let referencia = '';

                if (this.producto.categoria === 'LLANTA' && this.producto.tipo === 'NUEVO') {
                    referencia += (ancho ? ancho : '') + '/' +
                        (perfil ? perfil : '') +
                        (construccion ? construccion : '') +
                        (rin ? rin : '');
                } else if (this.producto.categoria === 'RIN' && this.producto.tipo === 'NUEVO') {
                    referencia += (diametro ? diametro : '') + 'X' +
                        (ancho ? ancho : '');
                }
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
