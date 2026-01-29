<div class="flex-shrink-0 mt-6">

    <button @click="enviar()" wire:loading.attr="disabled"
        class="w-full py-4 bg-green-600 text-white font-bold text-lg rounded-lg
                       transition-colors duration-200 hover:bg-green-700
                       disabled:opacity-50 disabled:cursor-not-allowed shadow-lg mb-3">
        Finalizar Venta
    </button>

    <!-- Botón para limpiar carrito -->
    <button @click.prevent="resetPedido()"
        class="w-full py-2 bg-red-500 text-white font-medium text-sm rounded-lg
                       transition-colors duration-200 hover:bg-red-600 shadow-md"
        onclick="return confirm('¿Estás seguro de que quieres limpiar el carrito? Esta acción no se puede deshacer.')">
        Limpiar Carrito
    </button>
</div>
