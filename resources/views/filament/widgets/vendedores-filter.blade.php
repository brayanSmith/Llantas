<div class="fi-wi-stats-overview">
    <div class="fi-wi-stats-overview-cards-grid">
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="flex items-center gap-3">
                <svg class="h-6 w-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.633 10.5c3.806 0 6.908-3.098 6.908-6.908A6.908 6.908 0 006.633 10.5zm0 0c1.806 0 3.284-.616 4.513-1.657M14.25 9h2.25M5.373 19.993a7.962 7.962 0 0113.255-.884M14.25 18h2.25m-9.303 3c1.152 0 2.243-.26 3.317-.723M8.625 21h2.25c1.152 0 2.243-.26 3.317-.723M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="text-base font-semibold text-gray-950 dark:text-white">
                    Filtro de Vendedores
                </h3>
            </div>

            <div class="mt-4">
                <select
                    wire:model="selectedVendedores"
                    multiple
                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                >
                    @foreach($this->getUsersComerciales() as $vendedor)
                        <option value="{{ $vendedor->id }}">{{ $vendedor->name }}</option>
                    @endforeach
                </select>
                <small class="text-gray-500">Puedes seleccionar varios vendedores manteniendo presionada la tecla Ctrl (Windows) o Cmd (Mac).</small>
            </div>
        </div>
    </div>
</div>
