<div>
     <div class="mb-3" wire:ignore>
        {{-- <label class="form-label">Buscar Cliente</label>- --}}
        <select id="client-search" class="form-control" placeholder="Buscar cliente..." x-model="pedido.cliente_id">
            <option value="">Seleccione un cliente</option>
            @foreach ($clientes as $cliente)
                <option value="{{ $cliente['id'] }}">
                    {{ $cliente['numero_documento'] }} - {{ $cliente['razon_social'] }} - {{ $cliente['ciudad'] }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label text-sm font-medium text-gray-700 dark:text-gray-300">
            Ciudad
        </label>
        <div class="mt-2">
            <span x-text="clienteSeleccionado.ciudad" class="inline-flex items-center px-4 py-2 rounded-lg text-base font-medium bg-gray-100 text-gray-900 dark:bg-gray-800 dark:text-gray-100"></span>
        </div>
    </div>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
    <div>
        <label class="form-label text-sm font-medium text-gray-700 dark:text-gray-300">
            Cartera Total
        </label>
        <div class="mt-2">
            <span x-text="clienteSeleccionado.saldo_total_pedidos_en_cartera" class="inline-flex items-center px-4 py-2 rounded-lg text-lg font-boldbg-amber-100 text-amber-900 dark:bg-amber-900/20 dark:text-amber-400">
            </span>
        </div>
    </div>

    <div>
        <label class="form-label text-sm font-medium text-gray-700 dark:text-gray-300">
            Cartera Vencida
        </label>
        <div class="mt-2">
            <span x-text="clienteSeleccionado.saldo_total_pedidos_vencidos" class="inline-flex items-center px-4 py-2 rounded-lg text-lg font-bold bg-red-100 text-red-900 dark:bg-red-900/20 dark:text-red-400">
            </span>
        </div>
    </div>
</div>
    <!-- Tom Select se inicializa y reinicializa automáticamente con Alpine.js -->
    <script>
        // Reintenta la inicialización en eventos típicos de Livewire/Filament
        document.addEventListener('DOMContentLoaded', initTomSelect, { once: true });
        document.addEventListener('livewire:initialized', initTomSelect);
        document.addEventListener('livewire:navigated', initTomSelect);
        document.addEventListener('livewire:update', initTomSelect);

        function initTomSelect() {
            const sel = document.getElementById('client-search');
            if (!sel || sel.tomselect || typeof TomSelect === 'undefined') return;

            const ts = new TomSelect(sel, {
                allowEmptyOption: true,
                searchField: ['text'],   // busca por el texto visible
                maxOptions: 10000,
                placeholder: sel.getAttribute('placeholder') || 'Buscar...',
                sortField: { field: 'text', direction: 'asc' },
            });

            // Solo actualiza el valor visual del select cuando Alpine (o Livewire) lo indique
            window.addEventListener('cliente-set', (e) => ts.setValue(e.detail ?? ''));
        }
    </script>
</div>
