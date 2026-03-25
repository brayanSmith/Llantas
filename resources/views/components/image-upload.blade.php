@props([
    'property' => 'imagen',
    'path' => 'imagenPath',
    'deleteMethod' => 'eliminarImagen',
    'label' => 'Imagen',
    'id' => 'imagen-' . uniqid(),
    'wireIgnore' => false,
])

<!-- Component de Carga de Imagen Reutilizable -->
<div class="mt-4" {{ $wireIgnore ? 'wire:ignore' : '' }}>
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ $label }}</label>
    <div class="space-y-4">

        <!-- Campo de carga de archivo (solo visible si NO hay imagen) -->
        @if (!$this->$property && !$this->$path)
            <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center hover:border-blue-500 dark:hover:border-blue-400 transition-colors cursor-pointer"
                @click="document.getElementById('{{ $id }}').click()">

                <!-- Spinner mientras se carga -->
                <div wire:loading wire:target="{{ $property }}" class="flex flex-col items-center justify-center">
                    <div class="relative w-16 h-16 mb-3">
                        <!-- Círculo de fondo -->
                        <div class="absolute inset-0 border-4 border-blue-100 dark:border-blue-900 rounded-full"></div>

                        <!-- Spinner principal -->
                        <div class="absolute inset-0 border-4 border-transparent border-t-blue-600 border-r-blue-500 dark:border-t-blue-400 dark:border-r-blue-300 rounded-full animate-spin"></div>

                        <!-- Spinner secundario (velocidad diferente) -->
                        <div class="absolute inset-2 border-3 border-transparent border-b-blue-400 dark:border-b-blue-300 rounded-full animate-spin" style="animation-direction: reverse; animation-duration: 1.5s;"></div>

                        <!-- Centro punteado -->
                        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-2 h-2 bg-blue-600 dark:bg-blue-400 rounded-full animate-pulse"></div>
                    </div>
                    <p class="text-sm text-blue-600 dark:text-blue-400 font-medium">Cargando imagen...</p>
                </div>

                <!-- Contenido normal -->
                <div wire:loading.remove wire:target="{{ $property }}">
                    <svg class="w-8 h-8 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Haz clic o arrastra una imagen aquí</p>
                    <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">Solo PNG o JPG hasta 2MB</p>
                </div>
            </div>
        @endif

        <!-- Vista previa de la imagen (solo visible si existe imagen) -->
        @if ($this->$property || $this->$path)
            <div class="relative mt-4 border border-gray-200 dark:border-gray-600 rounded-lg p-4 bg-gray-50 dark:bg-gray-800">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Imagen Cargada</label>

                <img
                    src="{{ $this->$path ?: $this->$property->temporaryUrl() }}"
                    alt="Vista previa de {{ $label }}"
                    class="max-h-64 rounded border border-gray-200 dark:border-gray-600 object-contain mx-auto mb-3" />

                <button
                    type="button"
                    wire:click="{{ $deleteMethod }}"
                    wire:loading.attr="disabled"
                    class="w-full px-3 py-2 text-sm text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 border border-red-300 dark:border-red-600 rounded hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors font-medium disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">

                    <!-- Spinner mientras se elimina -->
                    <div wire:loading wire:target="{{ $deleteMethod }}" class="flex items-center gap-2">
                        <div class="relative w-4 h-4">
                            <div class="absolute inset-0 border-2 border-transparent border-t-red-600 dark:border-t-red-400 rounded-full animate-spin"></div>
                        </div>
                        <span>Eliminando...</span>
                    </div>

                    <!-- Contenido normal -->
                    <div wire:loading.remove wire:target="{{ $deleteMethod }}" class="flex items-center gap-2">
                        <span>✕ Eliminar imagen</span>
                    </div>
                </button>
            </div>
        @endif

        <input
            type="file"
            id="{{ $id }}"
            wire:model="{{ $property }}"
            @change="@this.call('actualizarImagenPrevia')"
            accept="image/png,image/jpeg,image/jpg"
            class="hidden" />
    </div>
</div>
