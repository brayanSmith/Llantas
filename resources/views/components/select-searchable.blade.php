@props([
    'options' => [],
    'idKey' => 'id',
    'textKey' => 'nombre',
    'selectId' => 'select-dynamic',
    'placeholder' => 'Seleccione una opción...',
    'dependsOn' => null,
    'filterKey' => null,
    'clearOnDependencyChange' => true,
    'loadAllOptions' => true,
    'maxVisibleOptions' => 150,
])

@php
    $inputAttributes = $attributes->except(['x-model', 'dependsOn']);
@endphp

<div x-data="selectSearchable({
    selectId: '{{ $selectId }}',
    idKey: '{{ $idKey }}',
    textKey: '{{ $textKey }}',
    placeholder: '{{ $placeholder }}',
    allOptions: {{ Js::from($options) }},
    dependsOn: '{{ $dependsOn ?? '' }}',
    filterKey: '{{ $filterKey ?? '' }}',
    clearOnDependencyChange: {{ $clearOnDependencyChange ? 'true' : 'false' }},
    maxVisibleOptions: {{ (int) $maxVisibleOptions }},
})" class="w-full">

    <div class="relative">
        <!-- Input de búsqueda -->
        <input type="text" x-ref="searchInput" @input="openDropdown(); debouncedFilter($event.target.value)"
            @click="openDropdown()" @blur="closeDropdownAfterDelay()" @keydown.arrow-down.prevent="selectNext()"
            @keydown.arrow-up.prevent="selectPrev()" @keydown.enter.prevent="selectCurrent()" @keydown.escape="closeDropdown()"
            :placeholder="placeholder"
            {{ $inputAttributes->class('input-pedido-select w-full pr-10 py-2') }}
            />

        <!-- Botón limpiar selección -->
        <button type="button" x-show="selectedOption" @mousedown.prevent @click.prevent="clearSelection()"
            class="absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 rounded-full flex items-center justify-center text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-700 transition"
            aria-label="Limpiar selección">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>

        <!-- Select oculto para x-model -->
        <select id="{{ $selectId }}" x-ref="hiddenSelect"
            {{ $attributes->filter(fn($value, $key) => $key !== 'dependsOn') }} class="hidden">
            <option value="">{{ $placeholder }}</option>
        </select>
    </div>

    <!-- Dropdown de opciones - Renderizado al body con positioning absoluto -->
    <div x-show="isOpen" x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95" x-ref="dropdown"
        :style="`position: fixed; top: ${dropdownTop}px; left: ${dropdownLeft}px; width: ${dropdownWidth}px; display: ${isOpen ? 'block' : 'none'};`"
        class="bg-white/90 border border-gray-200 dark:border-gray-700 dark:bg-gray-800/90 rounded-b-lg rounded-t-md shadow-lg z-[99999] max-h-60 overflow-y-auto overflow-x-hidden"
        style="margin: 0;">
        <!-- Sin resultados -->
        <div x-show="isOpen && filteredOptions.length === 0" class="p-3 text-gray-500 text-sm text-center">
            Sin resultados
        </div>

        <!-- Opciones -->
        <template x-for="(option, index) in filteredOptions" :key="option[idKey]">
            <div @mousedown="selectOption(option)" @mouseover="currentIndex = index"
                :class="currentIndex === index ? 'bg-blue-100 dark:bg-blue-900' : 'hover:bg-gray-100 dark:hover:bg-gray-700'"
                class="px-4 py-2 cursor-pointer text-sm transition whitespace-normal break-words"
                x-html="getHighlightedText(option)">
            </div>
        </template>
    </div>

    <script>
        function selectSearchable({
        selectId,
        idKey,
        textKey,
        placeholder,
        allOptions = [],
        dependsOn = '',
        filterKey = '',
        clearOnDependencyChange = true,
        maxVisibleOptions = 150,
    }) {
        return {
            selectId,
            idKey,
            textKey,
            placeholder,
            allOptions,
            dependsOn,
            filterKey,
            clearOnDependencyChange,
            maxVisibleOptions,
            isOpen: false,
            searchText: '',
            currentIndex: -1,
            filteredOptions: [],
            indexedOptions: [],
            selectedOption: null,
            closeDropdownTimeout: null,
            filterTimeout: null,
            dropdownTop: 0,
            dropdownLeft: 0,
            dropdownWidth: 0,
            scrollListener: null,
            resizeListener: null,
            clickListener: null,
            dependencyElement: null,
            dependencyChangeListener: null,
            modelPath: null,

            init() {
                // NO precargar opciones al inicio - mantener vacío hasta que se abra
                this.filteredOptions = [];
                this.prepareIndex();

                this.modelPath = this.getModelPath();

                // Actualizar select oculto
                this.updateHiddenSelect();

                this.$nextTick(() => {
                    this.syncFromModelValue(this.getModelValue());
                });

                this.$watch(() => this.getModelValue(), (newValue) => {
                    this.syncFromModelValue(newValue);
                });

                // Si hay dependencia, escuchar cambios
                if (this.dependsOn) {
                    this.$watch(() => this.getDependencyValue(), () => {
                        if (this.clearOnDependencyChange) {
                            this.clearSelection();
                        }
                        this.filterOptionsBasedOnDependency();
                    });

                    this.$nextTick(() => {
                        this.dependencyElement = this.findDependencyElement();

                        if (this.dependencyElement) {
                            this.dependencyChangeListener = () => {
                                if (this.clearOnDependencyChange) {
                                    this.clearSelection();
                                }
                                this.filterOptionsBasedOnDependency();
                            };

                            this.dependencyElement.addEventListener('change', this.dependencyChangeListener);
                        }
                    });
                }

                // Listeners con referencias para poder limpiarlas
                this.scrollListener = () => this.updateDropdownPosition();
                this.resizeListener = () => this.updateDropdownPosition();
                this.clickListener = (e) => {
                    if (this.isOpen &&
                        this.$refs.searchInput &&
                        this.$refs.dropdown &&
                        !this.$refs.searchInput.contains(e.target) &&
                        !this.$refs.dropdown.contains(e.target)) {
                        this.closeDropdown();
                    }
                };

                window.addEventListener('scroll', this.scrollListener, true);
                window.addEventListener('resize', this.resizeListener);
                document.addEventListener('click', this.clickListener);

                // Limpiar listeners al destruir
                this.$el.addEventListener('destroy', () => {
                    window.removeEventListener('scroll', this.scrollListener, true);
                    window.removeEventListener('resize', this.resizeListener);
                    document.removeEventListener('click', this.clickListener);

                    if (this.dependencyElement && this.dependencyChangeListener) {
                        this.dependencyElement.removeEventListener('change', this.dependencyChangeListener);
                    }
                });
            },

            debouncedFilter(searchValue) {
                // Limpiar timeout anterior
                if (this.filterTimeout) {
                    clearTimeout(this.filterTimeout);
                }

                // Ejecutar filtrado después de 150ms de inactividad
                this.filterTimeout = setTimeout(() => {
                    this.filterOptions(searchValue);
                }, 150);
            },

            normalizeText(text) {
                return String(text ?? '')
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '')
                    .toLowerCase();
            },

            escapeHtml(text) {
                return String(text ?? '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/\"/g, '&quot;')
                    .replace(/'/g, '&#39;');
            },

            escapeRegExp(text) {
                return String(text ?? '').replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            },

            getHighlightedText(option) {
                const originalText = String(option?.[this.textKey] ?? '');
                const escapedText = this.escapeHtml(originalText);
                const normalizedSearch = this.normalizeText(this.searchText);
                const searchWords = normalizedSearch.split(/\s+/).filter(word => word.length > 0);

                if (searchWords.length === 0) {
                    return escapedText;
                }

                let highlightedText = escapedText;

                searchWords.forEach(word => {
                    const pattern = new RegExp(`(${this.escapeRegExp(word)})`, 'ig');
                    highlightedText = highlightedText.replace(
                        pattern,
                        '<mark class="bg-yellow-200 dark:bg-yellow-500/40 text-inherit rounded px-0.5">$1</mark>'
                    );
                });

                return highlightedText;
            },

            prepareIndex() {
                this.indexedOptions = this.allOptions.map(option => ({
                    option,
                    normalizedText: this.normalizeText(option[this.textKey]),
                    normalizedId: this.normalizeText(option[this.idKey]),
                    dependencyValue: this.filterKey ? String(option[this.filterKey] ?? '') : '',
                }));
            },

            filterOptions(searchValue) {
                this.searchText = String(searchValue ?? '').toLowerCase();
                const normalizedSearch = this.normalizeText(this.searchText);
                const searchWords = normalizedSearch.split(/\s+/).filter(word => word.length > 0);

                const rawDependencyValue = (this.dependsOn && this.filterKey) ?
                    this.getDependencyValue() :
                    null;

                const shouldFilterByDependency = this.dependsOn &&
                    this.filterKey &&
                    rawDependencyValue !== null &&
                    rawDependencyValue !== undefined &&
                    String(rawDependencyValue) !== '';

                const dependencyValue = shouldFilterByDependency ? String(rawDependencyValue) : '';
                const filtered = [];

                for (const item of this.indexedOptions) {
                    if (shouldFilterByDependency && item.dependencyValue !== dependencyValue) {
                        continue;
                    }

                    if (searchWords.length > 0) {
                        const matchesAllWords = searchWords.every(word =>
                            item.normalizedText.includes(word) || item.normalizedId.includes(word)
                        );

                        if (!matchesAllWords) {
                            continue;
                        }
                    }

                    filtered.push(item.option);

                    if (filtered.length >= this.maxVisibleOptions) {
                        break;
                    }
                }

                this.filteredOptions = filtered;
                this.currentIndex = -1;
            },

            filterOptionsBasedOnDependency() {
                this.filterOptions(this.searchText);
            },

            getRootValue(path) {
                if (!path) {
                    return null;
                }

                const parts = String(path).split('.');
                const scopes = this.$el?._x_dataStack || [];

                for (const scope of scopes) {
                    let value = scope;
                    let found = true;

                    for (const part of parts) {
                        if (value === null || value === undefined) {
                            found = false;
                            break;
                        }

                        const nextValue = value[part];

                        if (nextValue === undefined && !(part in Object(value))) {
                            found = false;
                            break;
                        }

                        value = nextValue;
                    }

                    if (found) {
                        return value;
                    }
                }

                return null;
            },

            findDependencyElement() {
                if (!this.dependsOn) {
                    return null;
                }

                const selector = `[x-model="${this.dependsOn}"]`;
                const localContainer = this.$el.closest('tr') || this.$el.parentElement || document;

                return localContainer.querySelector(selector) ||
                    (this.$root && this.$root.querySelector ? this.$root.querySelector(selector) : null) ||
                    document.querySelector(selector);
            },

            getDependencyValue() {
                const rootValue = this.getRootValue(this.dependsOn);

                if (rootValue !== null && rootValue !== undefined && String(rootValue) !== '') {
                    return rootValue;
                }

                if (!this.dependencyElement) {
                    this.dependencyElement = this.findDependencyElement();
                }

                if (this.dependencyElement && this.dependencyElement.value !== undefined) {
                    const value = this.dependencyElement.value;
                    return value === '' ? null : value;
                }

                return null;
            },

            getModelPath() {
                if (!this.$refs.hiddenSelect) {
                    return null;
                }

                return this.$refs.hiddenSelect.getAttribute('x-model');
            },

            getModelValue() {
                if (this.modelPath) {
                    const rootValue = this.getRootValue(this.modelPath);
                    if (rootValue !== undefined) {
                        return rootValue;
                    }
                }

                if (this.$refs.hiddenSelect && this.$refs.hiddenSelect.value !== undefined) {
                    return this.$refs.hiddenSelect.value;
                }

                return null;
            },

            syncFromModelValue(rawValue) {
                const modelValue = (rawValue === null || rawValue === undefined) ?
                    '' :
                    String(rawValue);

                if (modelValue === '') {
                    if (this.selectedOption !== null) {
                        this.selectedOption = null;
                        if (this.$refs.searchInput) {
                            this.$refs.searchInput.value = '';
                        }
                        this.syncHiddenSelectOption();
                    }

                    return;
                }

                if (this.selectedOption && String(this.selectedOption[this.idKey]) === modelValue) {
                    return;
                }

                const option = this.allOptions.find(item => String(item[this.idKey]) === modelValue);

                if (!option) {
                    return;
                }

                this.selectedOption = option;

                if (this.$refs.searchInput) {
                    this.$refs.searchInput.value = String(option[this.textKey] ?? '');
                }

                this.syncHiddenSelectOption(option);
            },

            selectOption(option) {
                this.selectedOption = option;
                this.$refs.searchInput.value = option[this.textKey];

                const hiddenSelect = this.$refs.hiddenSelect;
                this.syncHiddenSelectOption(option);

                // Disparar evento change
                hiddenSelect.dispatchEvent(new Event('change', {
                    bubbles: true
                }));

                // Cerrar inmediatamente
                this.isOpen = false;

                // Limpiar timeout si existe
                if (this.closeDropdownTimeout) {
                    clearTimeout(this.closeDropdownTimeout);
                    this.closeDropdownTimeout = null;
                }
            },

            selectCurrent() {
                if (this.currentIndex >= 0 && this.filteredOptions[this.currentIndex]) {
                    this.selectOption(this.filteredOptions[this.currentIndex]);
                }
            },

            selectNext() {
                if (this.currentIndex < this.filteredOptions.length - 1) {
                    this.currentIndex++;
                    this.scrollToCurrentOption();
                }
            },

            selectPrev() {
                if (this.currentIndex > 0) {
                    this.currentIndex--;
                    this.scrollToCurrentOption();
                }
            },

            scrollToCurrentOption() {
                this.$nextTick(() => {
                    const dropdown = this.$refs.dropdown;
                    if (dropdown && this.currentIndex >= 0) {
                        const option = dropdown.children[this.currentIndex +
                            1]; // +1 por el mensaje "Sin resultados"
                        if (option && option.scrollIntoView) {
                            option.scrollIntoView({
                                block: 'nearest',
                                behavior: 'smooth'
                            });
                        }
                    }
                });
            },

            openDropdown() {
                this.isOpen = true;
                this.filterOptions(this.searchText);

                // Calcular posición del dropdown
                this.$nextTick(() => {
                    this.updateDropdownPosition();
                });
            },

            closeDropdown() {
                this.isOpen = false;
                this.currentIndex = -1;

                // Limpiar timeout si existe
                if (this.closeDropdownTimeout) {
                    clearTimeout(this.closeDropdownTimeout);
                    this.closeDropdownTimeout = null;
                }
            },

            closeDropdownAfterDelay() {
                // Limpiar timeout anterior si existe
                if (this.closeDropdownTimeout) {
                    clearTimeout(this.closeDropdownTimeout);
                }

                // Esperar a que se procese el click en las opciones
                this.closeDropdownTimeout = setTimeout(() => {
                    this.isOpen = false;
                    this.currentIndex = -1;
                }, 200);
            },

            updateDropdownPosition() {
                if (this.$refs.searchInput) {
                    const inputRect = this.$refs.searchInput.getBoundingClientRect();
                    this.dropdownTop = inputRect.bottom;
                    this.dropdownLeft = inputRect.left;
                    this.dropdownWidth = inputRect.width;
                }
            },

            clearSelection() {
                this.selectedOption = null;
                this.$refs.searchInput.value = '';
                this.$refs.hiddenSelect.value = '';
                this.$refs.hiddenSelect.dispatchEvent(new Event('change', {
                    bubbles: true
                }));
                this.searchText = '';
                this.filteredOptions = [];
                this.isOpen = false;
                this.syncHiddenSelectOption();
            },

            syncHiddenSelectOption(option = null) {
                const select = this.$refs.hiddenSelect;

                if (!select) {
                    return;
                }

                Array.from(select.options).slice(1).forEach(opt => opt.remove());

                if (option) {
                    const opt = document.createElement('option');
                    opt.value = String(option[this.idKey]);
                    opt.textContent = String(option[this.textKey]);
                    select.appendChild(opt);
                    select.value = String(option[this.idKey]);
                } else {
                    select.value = '';
                }
            },

            updateHiddenSelect() {
                this.$nextTick(() => {
                    this.syncHiddenSelectOption(this.selectedOption);
                });
            },
        };
    }
    </script>
</div>
