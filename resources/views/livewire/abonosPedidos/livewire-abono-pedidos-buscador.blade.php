<!-- Contenedor Principal -->
<div class="space-y-6">

    <!-- Sección 1: Búsqueda de pedidos -->
    <div>
        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            Búsqueda de Pedidos
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- Cliente -->
            <div wire:ignore>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Cliente <span class="text-red-500">*</span></label>
                <x-select-dinamico label="Cliente" placeholder="Seleccione un cliente" model="clienteAbonoIngresado"
                    :options="$clientes" idKey="id" textKey="razon_social" selectId="select-cliente" required />
            </div>

            <!-- Usuario -->
            <div wire:ignore>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Usuario <span class="text-red-500">*</span></label>
                <x-select-dinamico label="Usuario" placeholder="Seleccione un usuario" model="usuarioAbonoIngresado"
                    :options="$users" idKey="id" textKey="name" selectId="select-usuario" required />
            </div>

            <!-- Botón Buscar -->
            <div class="flex items-end">
                <button @click="buscarPedidosEnCartera()"
                    class="w-full px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-medium rounded hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                    :disabled="!clienteAbonoIngresado || isLoading">
                    <template x-if="!isLoading">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </template>
                    <template x-if="isLoading">
                        <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </template>
                    <span x-text="isLoading ? 'Cargando...' : 'Buscar'"></span>
                </button>
            </div>
        </div>
    </div>

    <!-- Sección 2: Información del Abono -->
    <div>
        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Detalles del Abono
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Medio de Pago -->
            <div wire:ignore>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Medio de Pago <span class="text-red-500">*</span></label>
                <x-select-dinamico label="Medio de Pago" placeholder="Seleccione un medio de pago"
                    model="formaDePagoAbonoIngresada" :options="$formasPago" idKey="id" textKey="concatenar_subcuenta_concepto"
                    selectId="select-forma-pago" required />
            </div>

            <!-- Fecha Abono -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Fecha Abono <span class="text-red-500">*</span></label>
                <input type="date" x-model="fechaAbonoIngresada"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-black dark:text-white rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    required />
            </div>

            <!-- Valor Abono -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Valor Abono <span class="text-red-500">*</span></label>
                <input type="number" step="0.01" min="0" x-model.number="valorAbonoIngresado"
                    placeholder="0.00"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-black dark:text-white rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-right font-semibold"
                    required />
            </div>

            <!-- Resultado -->
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900 dark:to-blue-800 rounded p-3 border border-blue-200 dark:border-blue-700">
                <label class="block text-xs font-semibold text-blue-700 dark:text-blue-300 uppercase mb-1">Restante</label>
                <span class="block text-2xl font-bold text-blue-900 dark:text-blue-100"
                    x-text="restante.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 2 })">
                </span>
            </div>
        </div>

        <!-- Descripción (Ancho Completo) -->
        <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Descripción del Abono</label>
            <textarea x-model="descripcionAbonoIngresada"
                placeholder="Ingrese una descripción o referencia para el abono (opcional)"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-black dark:text-white rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                rows="3"></textarea>
        </div>
    </div>

    <!-- Sección 3: Resumen de Selección -->
    <div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Pedidos Seleccionados -->
            <div class="flex items-center bg-white dark:bg-gray-700 rounded p-3 border border-gray-200 dark:border-gray-600 shadow-sm">
                <div class="flex-shrink-0">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Pedidos Seleccionados</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white" x-text="cantidadPedidosSeleccionados">0</p>
                </div>
            </div>

            <!-- Total a Debitar -->
            <div class="flex items-center bg-white dark:bg-gray-700 rounded p-3 border border-gray-200 dark:border-gray-600 shadow-sm">
                <div class="flex-shrink-0">
                    <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Total a Debitar</p>
                    <p class="text-xl font-bold text-emerald-600 dark:text-emerald-400"
                        x-text="totalAPagarSeleccionado.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 2 })">
                        $0.00
                    </p>
                </div>
            </div>

            <!-- Estado -->
            <div class="flex items-center bg-white dark:bg-gray-700 rounded p-3 border border-gray-200 dark:border-gray-600 shadow-sm">
                <div class="flex-shrink-0">
                    <svg class="w-8 h-8 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Estado</p>
                    <p class="text-sm font-bold"
                        :class="restante > 0 ? 'text-amber-600 dark:text-amber-400' : restante === 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400'">
                        <span x-show="restante > 0">Pendiente</span>
                        <span x-show="restante === 0">Completo</span>
                        <span x-show="restante < 0">Excedido</span>
                    </p>
                </div>
            </div>
        </div>
    </div>

</div>
