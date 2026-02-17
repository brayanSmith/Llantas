<!-- Modal de confirmación de venta (Alpine/Filament) -->
<div x-data="{ show: @entangle('showConfirmModal') }" x-show="show" x-transition class="fixed inset-0 z-50 flex items-center justify-center"
    style="display: none;">
    <div class="absolute inset-0 bg-black opacity-40"></div>
    <div
        class="relative bg-white dark:bg-neutral-800 rounded-2xl shadow-2xl w-full max-w-xl mx-auto p-8 z-50 flex flex-col items-center">
        <h2 class="text-2xl font-bold mb-4 text-center text-gray-900 dark:text-gray-100">{{ $confirmModalTitle }}
        </h2>
        <p class="mb-6 text-center text-gray-700 dark:text-gray-200">{{ $confirmModalBody }}</p>
        <div class="flex gap-4">
            <button @click.prevent="history.back()"
                class="px-6 py-2 bg-blue-600 text-white rounded-lg font-bold hover:bg-blue-700">Salir
            </button>
        </div>
    </div>
</div>
