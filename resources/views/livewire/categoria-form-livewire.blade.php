<div x-data="categoriaForm({
    categoria: @js($categoriaEncontrada),
    atributos: @js($atributos),

})" x-init="init()" class="space-y-4">
    <!-- Nombre de la categoría -->
    <div class="bg-white rounded-lg shadow p-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">Nombre de la categoría</label>
        <input type="text" x-model="categoria.nombre_categoria" placeholder="Nombre de la categoría"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
    </div>

    <!-- Tabla de Atributos -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-800">Atributos de la Categoría</h2>
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
                        <tr class="bg-gray-100 border-b-2 border-gray-300">
                            <th class="text-left px-4 py-3 font-semibold text-gray-700">Nombre</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-700">Tipo</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-700">Opciones</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-700">Valor por defecto</th>
                            <th class="text-center px-4 py-3 font-semibold text-gray-700">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(atributo, index) in atributos" :key="index">
                            <tr class="border-b border-gray-200 hover:bg-gray-50">
                                <!-- Columna: Nombre -->
                                <td class="px-4 py-3">
                                    <input type="text" x-model="atributo.nombre"
                                        placeholder="Ej: Color, Talla"
                                        class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500">
                                </td>

                                <!-- Columna: Tipo -->
                                <td class="px-4 py-3">
                                    <select x-model="atributo.tipo" @change="if (atributo.tipo !== 'ENUM') atributo.opciones = ''"
                                        class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500">
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
                                        <input type="text" x-model="atributo.opciones"
                                            placeholder="Ej: Rojo, Azul, Verde (separados por coma)"
                                            class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500">
                                    </template>
                                    <template x-if="atributo.tipo !== 'ENUM'">
                                        <span class="text-gray-400 text-sm">-</span>
                                    </template>
                                </td>

                                <!-- Columna: Valor por defecto -->
                                <td class="px-4 py-3">
                                    <input type="text" x-model="atributo.valor_por_defecto"
                                        placeholder="Valor por defecto"
                                        class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500">
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
            <p class="text-gray-500 text-sm py-8 text-center">No hay atributos. Haz clic en "+ Agregar Atributo" para empezar.</p>
        </template>
    </div>

    <!-- Toast de notificación -->
    <x-toast
        visiblePath="mensaje.visible"
        messagePath="mensaje.texto"
        typePath="mensaje.tipo" />


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
