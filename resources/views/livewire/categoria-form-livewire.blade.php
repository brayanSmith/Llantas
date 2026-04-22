<div x-data="categoriaForm({
    categoria: @js($categoriaEncontrada),
    atributos: @js($atributos),

})" x-init="init()" class="space-y-4">
    <!-- Nombre de la categoría -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900 p-6">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nombre de la categoría</label>
        <input type="text" x-model="categoria.nombre_categoria" placeholder="Nombre de la categoría"
            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 dark:placeholder-gray-400 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
    </div>

    <!-- Tabla de Atributos -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900 p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Atributos de la Categoría</h2>
            <button type="button" @click="agregarAtributo"
                class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                + Agregar Atributo
            </button>
        </div>

        <!-- Tabla -->
        <template x-if="atributos.length > 0">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-gray-700 border-b-2 border-gray-300 dark:border-gray-600">
                            <th class="text-left px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">Nombre</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">Tipo</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">Opciones</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">Valor por defecto</th>
                            <th class="text-center px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(atributo, index) in atributos" :key="index">
                            <tr class="border-b border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">
                                <!-- Columna: Nombre -->
                                <td class="px-4 py-3">
                                    <input type="text" x-model="atributo.nombre"
                                        placeholder="Ej: Color, Talla"
                                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 dark:placeholder-gray-400 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500">
                                </td>

                                <!-- Columna: Tipo -->
                                <td class="px-4 py-3">
                                    <select x-model="atributo.tipo" @change="if (atributo.tipo !== 'ENUM') atributo.opciones = ''"
                                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500">
                                        <option value="TEXTO">Texto</option>
                                        <option value="NUMERO">Número</option>
                                        <option value="DECIMAL">Decimal</option>
                                        <option value="ENUM">ENUM</option>
                                        <option value="SEPARADOR">Separador</option>
                                    </select>
                                </td>

                                <!-- Columna: Opciones -->
                                <td class="px-4 py-3">
                                    <template x-if="atributo.tipo === 'ENUM'">
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs text-gray-500 dark:text-gray-400"
                                                x-text="(() => {
                                                    let opts = atributo.opciones;
                                                    if (!opts) return 'Sin opciones';
                                                    if (Array.isArray(opts)) return opts.length + ' opciones';
                                                    try { let p = JSON.parse(opts); if (Array.isArray(p)) return p.length + ' opciones'; } catch(e) {}
                                                    return opts.split(',').filter(o => o.trim()).length + ' opciones';
                                                })()">
                                            </span>
                                            <button type="button"
                                                @click="abrirModalOpciones(index)"
                                                class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-xs font-medium transition">
                                                Editar opciones
                                            </button>
                                        </div>
                                    </template>
                                    <template x-if="atributo.tipo !== 'ENUM'">
                                        <span class="text-gray-400 dark:text-gray-500 text-sm">-</span>
                                    </template>
                                </td>

                                <!-- Columna: Valor por defecto -->
                                <td class="px-4 py-3">
                                    <input type="text" x-model="atributo.valor_por_defecto"
                                        placeholder="Valor por defecto"
                                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 dark:placeholder-gray-400 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500">
                                </td>

                                <!-- Columna: Acción -->
                                <td class="px-4 py-3 text-center">
                                    <button type="button" @click="atributos.splice(index, 1)"
                                        class="text-red-500 hover:text-red-700 font-semibold text-lg transition">
                                        ✕
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </template>

        <!-- Mensaje si no hay atributos -->
        <template x-if="atributos.length === 0">
            <p class="text-gray-500 dark:text-gray-400 text-sm py-8 text-center">No hay atributos. Haz clic en "+ Agregar Atributo" para empezar.</p>
        </template>
    </div>

    <!-- Toast de notificación -->
    <x-toast
        visiblePath="mensaje.visible"
        messagePath="mensaje.texto"
        typePath="mensaje.tipo" />

    <!-- Modal de opciones ENUM -->
    <template x-if="modalOpciones.visible">
        <div class="fixed inset-0 z-50 flex items-center justify-center" @keydown.escape.window="cerrarModalOpciones()">
            <!-- Fondo oscuro -->
            <div class="absolute inset-0 bg-black/50" @click="cerrarModalOpciones()"></div>

            <!-- Contenido del modal -->
            <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-md mx-4 p-6 z-10">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                        Opciones para: <span class="text-blue-600 dark:text-blue-400" x-text="modalOpciones.nombreAtributo"></span>
                    </h3>
                    <button type="button" @click="cerrarModalOpciones()"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 text-xl font-bold">✕</button>
                </div>

                <!-- Input para nueva opción -->
                <div class="flex gap-2 mb-4">
                    <input type="text" x-model="modalOpciones.nuevaOpcion"
                        @keydown.enter.prevent="agregarOpcionEnum()"
                        placeholder="Escribir nueva opción..."
                        class="flex-1 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 dark:placeholder-gray-400 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 text-sm"
                        x-ref="inputNuevaOpcion">
                    <button type="button" @click="agregarOpcionEnum()"
                        class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                        + Agregar
                    </button>
                </div>

                <!-- Lista de opciones -->
                <div class="max-h-60 overflow-y-auto space-y-2 mb-4">
                    <template x-if="modalOpciones.opciones.length === 0">
                        <p class="text-gray-400 dark:text-gray-500 text-sm text-center py-4">No hay opciones. Agrega una arriba.</p>
                    </template>
                    <template x-for="(opcion, i) in modalOpciones.opciones" :key="i">
                        <div class="flex items-center justify-between bg-gray-50 dark:bg-gray-700 rounded-lg px-3 py-2 group">
                            <span class="text-sm text-gray-700 dark:text-gray-200" x-text="opcion"></span>
                            <button type="button" @click="modalOpciones.opciones.splice(i, 1)"
                                class="text-red-400 hover:text-red-600 dark:hover:text-red-400 opacity-0 group-hover:opacity-100 transition-opacity text-sm font-bold">
                                ✕
                            </button>
                        </div>
                    </template>
                </div>

                <!-- Botones del modal -->
                <div class="flex justify-end gap-2 pt-2 border-t border-gray-200 dark:border-gray-600">
                    <button type="button" @click="cerrarModalOpciones()"
                        class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 border border-gray-300 dark:border-gray-600 rounded-lg transition">
                        Cancelar
                    </button>
                    <button type="button" @click="guardarOpcionesEnum()"
                        class="px-4 py-2 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition">
                        Guardar opciones
                    </button>
                </div>
            </div>
        </div>
    </template>


    <div class="flex justify-end">
        <button type="button" @click="enviar()" :disabled="guardando"
                class="bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white px-4 py-2 rounded-lg text-sm font-medium transition flex items-center gap-2">
                <template x-if="!guardando">
                    <span>Guardar Categoría</span>
                </template>
                <template x-if="guardando">
                    <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>Guardando...</span>
                </template>
            </button>
        </div>

        <script>
            function categoriaForm({
                categoria = null,
                atributos = [],
            }) {
            return {
                categoria: categoria,
                atributos: atributos,
                guardando: false,
                mensaje: {
                    visible: false,
                    texto: '',
                    tipo: 'success',
                },
                modalOpciones: {
                    visible: false,
                    indiceAtributo: null,
                    nombreAtributo: '',
                    opciones: [],
                    nuevaOpcion: '',
                },

                init() {
                    console.log('Categoria:', this.categoria);
                    console.log('Atributos:', this.atributos);
                    this.escucharEventos();

                    // Verificar si hay un mensaje guardado en localStorage (después de reload)
                    const mensajeGuardado = localStorage.getItem('mensajeToast');
                    if (mensajeGuardado) {
                        const { texto, tipo } = JSON.parse(mensajeGuardado);
                        this.mostrarMensaje(texto, tipo);
                        localStorage.removeItem('mensajeToast');
                    }
                },

                escucharEventos() {
                    // Escuchar el evento de éxito desde Livewire
                    Livewire.on('notify-success', () => {
                        this.guardando = false;
                        this.resetearFormulario();

                        // Guardar mensaje en localStorage y recargar
                        localStorage.setItem('mensajeToast', JSON.stringify({
                            texto: '✅ Categoría guardada correctamente',
                            tipo: 'success'
                        }));
                        window.location.reload();
                    });

                    Livewire.on('notify-error', (mensaje) => {
                        this.guardando = false;
                        this.mostrarMensaje('❌ ' + mensaje, 'error');
                    });
                },

                mostrarMensaje(texto, tipo = 'success') {
                    this.mensaje = {
                        visible: true,
                        texto: texto,
                        tipo: tipo,
                    };

                    setTimeout(() => {
                        if (tipo === 'success') {
                            this.mensaje.visible = false;
                        }
                    }, 3000);
                },

                resetearFormulario() {
                    this.categoria = {
                        nombre_categoria: ''
                    };
                    this.atributos = [];
                },

                agregarAtributo() {
                    const nuevoAtributo = {
                        nombre: '',
                        tipo: 'TEXTO',
                        opciones: '',
                    };
                    this.atributos.push(nuevoAtributo);
                },

                abrirModalOpciones(index) {
                    const atributo = this.atributos[index];
                    this.modalOpciones.indiceAtributo = index;
                    this.modalOpciones.nombreAtributo = atributo.nombre || 'Sin nombre';

                    // Parsear opciones: puede venir como JSON array, array JS, o string con comas
                    let opciones = atributo.opciones;
                    if (Array.isArray(opciones)) {
                        this.modalOpciones.opciones = opciones.filter(o => o !== '');
                    } else if (typeof opciones === 'string' && opciones.trim() !== '') {
                        try {
                            const parsed = JSON.parse(opciones);
                            if (Array.isArray(parsed)) {
                                this.modalOpciones.opciones = parsed.filter(o => o !== '');
                            } else {
                                this.modalOpciones.opciones = opciones.split(',').map(o => o.trim()).filter(o => o !== '');
                            }
                        } catch (e) {
                            this.modalOpciones.opciones = opciones.split(',').map(o => o.trim()).filter(o => o !== '');
                        }
                    } else {
                        this.modalOpciones.opciones = [];
                    }

                    this.modalOpciones.nuevaOpcion = '';
                    this.modalOpciones.visible = true;

                    this.$nextTick(() => {
                        if (this.$refs.inputNuevaOpcion) {
                            this.$refs.inputNuevaOpcion.focus();
                        }
                    });
                },

                cerrarModalOpciones() {
                    this.modalOpciones.visible = false;
                    this.modalOpciones.indiceAtributo = null;
                    this.modalOpciones.opciones = [];
                    this.modalOpciones.nuevaOpcion = '';
                },

                agregarOpcionEnum() {
                    const valor = this.modalOpciones.nuevaOpcion.trim();
                    if (valor === '') return;

                    if (this.modalOpciones.opciones.includes(valor)) {
                        this.mostrarMensaje('⚠️ Esa opción ya existe', 'error');
                        return;
                    }

                    this.modalOpciones.opciones.push(valor);
                    this.modalOpciones.nuevaOpcion = '';

                    this.$nextTick(() => {
                        if (this.$refs.inputNuevaOpcion) {
                            this.$refs.inputNuevaOpcion.focus();
                        }
                    });
                },

                guardarOpcionesEnum() {
                    const index = this.modalOpciones.indiceAtributo;
                    if (index !== null && this.atributos[index]) {
                        this.atributos[index].opciones = this.modalOpciones.opciones.join(', ');
                    }
                    this.cerrarModalOpciones();
                },

                enviar() {
                    // Validación
                    if (!this.categoria.nombre_categoria || this.categoria.nombre_categoria.trim() === '') {
                        alert('Por favor ingresa el nombre de la categoría');
                        return;
                    }

                    if (this.atributos.length === 0) {
                        alert('Por favor agrega al menos un atributo');
                        return;
                    }

                    // Validar que todos los atributos tengan nombre
                    if (this.atributos.some(a => !a.nombre || a.nombre.trim() === '')) {
                        alert('Todos los atributos deben tener un nombre');
                        return;
                    }

                    // Validar que si es ENUM, tenga opciones
                    if (this.atributos.some(a => a.tipo === 'ENUM' && (!a.opciones || a.opciones.trim() === ''))) {
                        alert('Los atributos de tipo ENUM deben tener opciones');
                        return;
                    }

                    // Convertir Proxy a objetos planos y generar JSON único (IMPORTANTE)
                    const jsonCategoria = JSON.parse(JSON.stringify({
                        id: this.categoria.id || null,
                        nombre_categoria: this.categoria.nombre_categoria,
                        atributos: this.atributos
                    }));

                    this.guardando = true;
                    console.log('Enviando JSON único:', jsonCategoria);
                    this.$wire.guardarCategoria(jsonCategoria);
                }
            }
        }
        </script>
    </div>
