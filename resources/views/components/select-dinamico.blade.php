@props([
    'options' => [],
    'idKey' => 'id',
    'textKey' => 'nombre',
    'selectId' => 'select-dinamico',
    'placeholder' => 'Seleccione una opción...',
    'label' => '',
    'initialValue' => null,
])

<div class="w-full" x-data="selectDinamico({
    placeholder: '{{ $placeholder }}',
    idKey: '{{ $idKey }}',
    textKey: '{{ $textKey }}',
    options: @js($options),
    initialValue: @js($initialValue)
})" @click.away="open = false" x-init="init()">
    @if($label)
        <label class="text-[11px] font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400 block mb-2">
            {{ $label }}
        </label>
    @endif

    <div class="relative">
        <!-- Campo de búsqueda -->
        <div class="relative flex items-center">
            <textarea
                x-model="search"
                @focus="open = true"
                @input="open = true; filterOptions(); resizeTextarea($el)"
                :placeholder="selectedText || placeholder"
                class="input-pedido w-full pr-10 resize-none overflow-hidden min-h-[2.5rem] max-h-[100px]"
                style="height: 2.5rem;"
            ></textarea>
            <!-- Botón limpiar -->
            <button
                type="button"
                @click="clearSelection()"
                x-show="selectedValue"
                class="absolute right-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
            >
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>

        <!-- Dropdown de opciones -->
        <div
            x-show="open && filtered.length > 0"
            x-transition
            @click.away="open = false"
            class="absolute top-full left-0 right-0 mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg z-50 max-h-80 overflow-y-auto"
        >
            <template x-for="option in filtered" :key="option[idKey]">
                <button
                    type="button"
                    @click.prevent="selectOption(option)"
                    :class="{
                        'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400': selectedValue === String(option[idKey]),
                        'hover:bg-gray-100 dark:hover:bg-gray-700': selectedValue !== String(option[idKey])
                    }"
                    class="w-full text-left px-4 py-2.5 text-sm transition-colors"
                    x-html="highlightText(option[textKey])"
                />
            </template>
        </div>

        <!-- Input oculto sincronizado con el padre -->
        <input type="hidden" {{ $attributes }} x-effect="if ($el.value && $el.value !== selectedValue) { selectedValue = $el.value; const opt = options.find(o => String(o[idKey]) === selectedValue); if (opt) { selectedText = opt[textKey]; } }">
    </div>

    <!-- Mensaje cuando no hay resultados -->
    <div
        x-show="open && search && filtered.length === 0"
        class="absolute top-full left-0 right-0 mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg z-50 px-4 py-3 text-sm text-gray-500 dark:text-gray-400"
    >
        No se encontraron resultados para "<span x-text="search"></span>"
    </div>
</div>

<script>
// Función auxiliar global para resaltar texto
window.highlightSearchText = function(text, search) {
    if (!search) {
        return text;
    }

    const searchLower = search.toLowerCase();
    const textString = String(text);

    // Escapar caracteres especiales en la búsqueda
    const escapeRegex = (str) => str.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    const safeSearch = escapeRegex(searchLower);

    // Crear regex case-insensitive para encontrar todas las coincidencias
    const regex = new RegExp(`(${safeSearch})`, 'gi');

    // Reemplazar coincidencias con span resaltado
    const highlighted = textString.replace(regex, (match) => {
        return `<span class="bg-yellow-200 dark:bg-yellow-600/50 font-semibold rounded px-0.5">${match}</span>`;
    });

    return highlighted;
};

function selectDinamico(config) {
    return {
        open: false,
        search: '',
        selectedValue: '',
        selectedText: '',
        options: config.options,
        filtered: config.options,
        placeholder: config.placeholder,
        idKey: config.idKey,
        textKey: config.textKey,
        initialValue: config.initialValue,

        init() {
            // Inicializar con el valor pasado
            if (this.initialValue) {
                const selectedOption = this.options.find(opt =>
                    String(opt[this.idKey]) === String(this.initialValue)
                );
                if (selectedOption) {
                    this.selectedValue = String(selectedOption[this.idKey]);
                    this.selectedText = selectedOption[this.textKey];

                    // Actualizar el input oculto en el siguiente ciclo para asegurar que x-model lo capture
                    setTimeout(() => {
                        const input = this.$el.querySelector('input[type="hidden"]');
                        if (input && input.value !== this.selectedValue) {
                            input.value = this.selectedValue;
                            // Disparar eventos para sincronizar x-model del padre
                            input.dispatchEvent(new Event('input', { bubbles: true }));
                            input.dispatchEvent(new Event('change', { bubbles: true }));
                        }
                    }, 0);
                }
            }
        },

        resizeTextarea(textarea) {
            const minHeight = 40; // 2.5rem en píxeles
            const twoLinesHeight = 70; // Aproximadamente 2 líneas

            // Resetear la altura para calcular correctamente
            textarea.style.height = 'auto';

            // Si el contenido es mayor a 2 líneas, expandir; si no, mantener 1 línea
            const newHeight = Math.max(minHeight, Math.min(textarea.scrollHeight, 100));

            if (textarea.scrollHeight > twoLinesHeight) {
                textarea.style.height = newHeight + 'px';
            } else {
                textarea.style.height = minHeight + 'px';
            }
        },

        filterOptions() {
            if (!this.search) {
                this.filtered = this.options;
            } else {
                const searchLower = this.search.toLowerCase();
                this.filtered = this.options.filter(opt =>
                    String(opt[this.textKey]).toLowerCase().includes(searchLower)
                );
            }
        },

        highlightText(text) {
            return window.highlightSearchText(text, this.search);
        },

        selectOption(option) {
            this.selectedValue = String(option[this.idKey]);
            this.selectedText = option[this.textKey];

            // Actualizar el input oculto y disparar eventos para sincronizar x-model del padre
            const input = this.$el.querySelector('input[type="hidden"]');
            if (input) {
                input.value = this.selectedValue;
                // Disparar múltiples eventos para asegurar que Alpine.js capture el cambio
                input.dispatchEvent(new Event('change', { bubbles: true }));
                input.dispatchEvent(new Event('input', { bubbles: true }));
            }

            this.search = '';
            this.open = false;
            this.filtered = this.options;
        },

        clearSelection() {
            this.selectedValue = '';
            this.selectedText = '';
            this.search = '';
            this.filtered = this.options;
            this.open = false;

            // Limpiar el input oculto y disparar evento
            const input = this.$el.querySelector('input[type="hidden"]');
            if (input) {
                input.value = '';
                input.dispatchEvent(new Event('input', { bubbles: true }));
            }
        }
    };
}
</script>
