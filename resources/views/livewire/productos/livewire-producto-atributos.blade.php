<div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700" x-show="atributos.length > 0">
    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Atributos de la Categoría</h3>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <template x-for="atributo in atributos" :key="atributo.id">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2" x-text="atributo.nombre"></label>

                <!-- Input TEXTO -->
                <template x-if="atributo.tipo === 'TEXTO'">
                    <input
                        type="text"
                        :id="'atributo-' + atributo.id"
                        x-model="producto.atributo_productos[atributo.id]"
                        x-init="if (producto.atributo_productos[atributo.id] === undefined || producto.atributo_productos[atributo.id] === null || producto.atributo_productos[atributo.id] === '') producto.atributo_productos[atributo.id] = atributo.valor_por_defecto || ''"
                        @input="actualizarReferencia()"
                        class="input-pedido"
                        :placeholder="'Ingrese ' + atributo.nombre" />
                </template>
                <!-- Input NUMERO -->
                <template x-if="atributo.tipo === 'NUMERO'">
                    <input
                        type="number"
                        :id="'atributo-' + atributo.id"
                        x-model="producto.atributo_productos[atributo.id]"
                        x-init="if (producto.atributo_productos[atributo.id] === undefined || producto.atributo_productos[atributo.id] === null || producto.atributo_productos[atributo.id] === '') producto.atributo_productos[atributo.id] = atributo.valor_por_defecto || ''"
                        @input="actualizarReferencia()"
                        class="input-pedido"
                        :placeholder="'Ingrese ' + atributo.nombre" />
                </template>
                <!-- Input SEPARADOR -->
                <template x-if="atributo.tipo === 'SEPARADOR'">
                    <input
                        type="text"
                        :id="'atributo-' + atributo.id"
                        x-model="producto.atributo_productos[atributo.id]"
                        x-init="if (producto.atributo_productos[atributo.id] === undefined || producto.atributo_productos[atributo.id] === null || producto.atributo_productos[atributo.id] === '') producto.atributo_productos[atributo.id] = atributo.valor_por_defecto || ''"
                        class="input-pedido text-center px-1"
                        readonly
                        :placeholder="'Ingrese ' + atributo.nombre" />
                </template>

                <!-- Select ENUM -->
                <template x-if="atributo.tipo === 'ENUM'">
                    <select
                        :id="'atributo-' + atributo.id"
                        x-model="producto.atributo_productos[atributo.id]"
                        x-init="if (producto.atributo_productos[atributo.id] === undefined || producto.atributo_productos[atributo.id] === null || producto.atributo_productos[atributo.id] === '') producto.atributo_productos[atributo.id] = atributo.valor_por_defecto || ''"
                        @change="actualizarReferencia()"
                        class="input-pedido"
                    >
                        <option value="">Seleccione una opción</option>
                        <template x-for="opcion in parseOpciones(atributo.opciones)" :key="opcion">
                            <option :value="opcion" x-text="opcion"></option>
                        </template>
                    </select>
                </template>
            </div>
        </template>
    </div>
</div>
