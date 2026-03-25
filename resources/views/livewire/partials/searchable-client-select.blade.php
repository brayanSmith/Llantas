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
