<div>
    <div class="mb-3" wire:ignore>
        {{-- <label class="form-label">Buscar Cliente</label>- --}}
        <select id="client-search" class="form-control" placeholder="Buscar cliente...">
            <option value="">Seleccione un cliente</option>
            @foreach ($clientes as $cliente)
                <option value="{{ $cliente->id }}">
                    {{ $cliente->numero_documento }} - {{ $cliente->razon_social }} - {{ $cliente->ciudad }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label text-sm font-medium text-gray-700 dark:text-gray-300">
            Ciudad
        </label>
        <div class="mt-2">
            <span class="inline-flex items-center px-4 py-2 rounded-lg text-base font-medium
                        bg-gray-100 text-gray-900 dark:bg-gray-800 dark:text-gray-100">
                {{ $ciudad ?: 'No especificada' }}
            </span>
        </div>
    </div>

   <div class="mb-3">
    <label class="form-label text-sm font-medium text-gray-700 dark:text-gray-300">
        Saldo Total Cartera Cliente
    </label>
    <div class="mt-2">
        <span class="inline-flex items-center px-4 py-2 rounded-lg text-lg font-bold
                     bg-amber-100 text-amber-900 dark:bg-amber-900/20 dark:text-amber-400">
            $ {{ number_format($saldoTotalCarteraCliente, 0, ',', '.') }}
        </span>
    </div>
</div>

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

            // Sincroniza -> Livewire
            ts.on('change', (value) => {
                const comp = sel.closest('[wire\\:id]');
                if (comp && window.Livewire) {
                    window.Livewire.find(comp.getAttribute('wire:id'))
                        .set('cliente_id', value || null);
                }
            });

            // (Opcional) si cambias cliente_id desde PHP y quieres reflejarlo en el select:
            window.addEventListener('cliente-set', (e) => ts.setValue(e.detail ?? ''));
        }
    </script>
</div>
