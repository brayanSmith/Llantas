<div class="flex-shrink-0 mt-6">
    {{-- Total a pagar --}}
    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-neutral-700">
        <div class="flex justify-between items-center mb-2 text-lg font-bold">
            <span>Total a Pagar:</span>
            <span>COP:
                <span
                    x-text="getTotalAPagar().toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 2 })">
                </span>
            </span>
        </div>
    </div>

    {{--<button @click="enviar()" wire:loading.attr="disabled"
        class="w-full py-4 bg-green-600 text-white font-bold text-lg rounded-lg
                       transition-colors duration-200 hover:bg-green-700
                       disabled:opacity-50 disabled:cursor-not-allowed shadow-lg mb-3">
        Finalizar Venta
    </button>--}}

    <button @click="mostrarModalPago = true" wire:loading.attr="disabled"
        class="w-full py-3 bg-blue-600 text-white font-semibold text-base rounded-lg
                       transition-colors duration-200 hover:bg-blue-700 shadow-md mb-3 disabled:opacity-50 disabled:cursor-not-allowed">
        Proceder al Pago
    </button>



    <!-- Botón para limpiar carrito -->
    <button
        @click.prevent="if(confirm('¿Estás seguro de que quieres limpiar el carrito? Esta acción no se puede deshacer.')) { resetPedido(); location.reload(); }"
        class="w-full py-2 bg-red-500 text-white font-medium text-sm rounded-lg
                       transition-colors duration-200 hover:bg-red-600 shadow-md">
        Limpiar Carrito
    </button>
</div>
