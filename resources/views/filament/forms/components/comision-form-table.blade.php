<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    @php
        $record = $getRecord();
        $state = $getState();
        $calculados = $state['_calculados'] ?? [];
        $pedidosPreview = $state['_pedidos_preview'] ?? [];
        $abonosPreview = $state['_abonos_preview'] ?? [];
        
        $vendedores = \App\Models\User::whereHas('roles', function($q) {
            $q->whereIn('name', ['comercial']);
        })->get();

        // Usar valores calculados en tiempo real si existen, sino usar del record
        $montoVentaRemisionada = $calculados['monto_venta_remisionada'] ?? $record?->monto_venta_remisionada ?? 0;
        $totalVentaRemisionada = $calculados['total_venta_remisionada'] ?? $record?->total_venta_remisionada ?? 0;
        $montoVentaElectronica = $calculados['monto_venta_electronica'] ?? $record?->monto_venta_electronica ?? 0;
        $totalVentaElectronica = $calculados['total_venta_electronica'] ?? $record?->total_venta_electronica ?? 0;
        $montoTotalVentas = $calculados['monto_total_ventas'] ?? $record?->monto_total_ventas ?? 0;
        $totalComisionVentas = $calculados['total_comision_ventas'] ?? $record?->total_comision_ventas ?? 0;
        $montoAbonos = $calculados['monto_abonos'] ?? $record?->monto_abonos ?? 0;
        $totalAbonos = $calculados['total_abonos'] ?? $record?->total_abonos ?? 0;
        $totalComisionAbonos = $calculados['total_comision_abonos'] ?? $record?->total_comision_abonos ?? 0;
        $subtotalComision = $calculados['subtotal_comision'] ?? $record?->subtotal_comision ?? 0;
        $totalComisionNeta = $calculados['total_comision_neta'] ?? $record?->total_comision_neta ?? 0;
    @endphp

    <div class="space-y-4">
        {{-- Información General (Editable) --}}
        <div class="grid grid-cols-2 gap-4 p-4 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Vendedor <span class="text-red-600">*</span>
                </label>
                <select 
                    wire:model.live="data.comision_data.vendedor_id"
                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 focus:border-primary-500 focus:ring-primary-500"
                    required
                    @if($record) disabled @endif
                >
                    <option value="">Seleccione un vendedor</option>
                    @foreach($vendedores as $vendedor)
                        <option value="{{ $vendedor->id }}">{{ $vendedor->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Estado <span class="text-red-600">*</span>
                </label>
                <select 
                    wire:model="data.comision_data.estado_comision"
                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 focus:border-primary-500 focus:ring-primary-500"
                    required
                >
                    <option value="PENDIENTE">Pendiente</option>
                    
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Periodo Inicial <span class="text-red-600">*</span>
                </label>
                <input 
                    type="date"
                    wire:model.live="data.comision_data.periodo_inicial"
                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 focus:border-primary-500 focus:ring-primary-500"
                    required
                    @if($record) readonly @endif
                />
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Periodo Final <span class="text-red-600">*</span>
                </label>
                <input 
                    type="date"
                    wire:model.live="data.comision_data.periodo_final"
                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 focus:border-primary-500 focus:ring-primary-500"
                    required
                    @if($record) readonly @endif
                />
            </div>
        </div>

        {{-- Botón para generar datos --}}
        @if(!$record)
        <div class="flex justify-center">
            <button 
                type="button"
                wire:click="generarDatosComision"
                class="inline-flex items-center px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900"
            >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Generar Pedidos y Abonos
            </button>
        </div>
        @endif

        {{-- Tabla de Ventas --}}
        <div class="overflow-hidden border border-gray-200 dark:border-gray-700 rounded-lg">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-100 dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tipo Venta</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Monto (Calculado)</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">IVA % (Editable)</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total (Calculado)</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">% Comisión (Editable)</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Comisión (Calculado)</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                    <tr>
                        <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100">Remisionada</td>
                        <td class="px-4 py-3 text-sm text-right text-blue-600 dark:text-blue-400 font-semibold">${{ number_format($montoVentaRemisionada, 2) }}</td>
                        <td class="px-4 py-3">
                            <input 
                                type="number" 
                                step="0.01"
                                wire:model.live.debounce.500ms="data.comision_data.iva_venta_remisionada"
                                class="w-full text-center rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 focus:border-primary-500 focus:ring-primary-500"
                            />
                        </td>
                        <td class="px-4 py-3 text-sm text-right text-blue-600 dark:text-blue-400 font-semibold">${{ number_format($totalVentaRemisionada, 2) }}</td>
                        <td class="px-4 py-3" rowspan="2">
                            <input 
                                type="number" 
                                step="0.01"
                                wire:model.live.debounce.500ms="data.comision_data.porcentaje_comision_ventas"
                                class="w-full text-center rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 focus:border-primary-500 focus:ring-primary-500"
                            />
                        </td>
                        <td class="px-4 py-3 text-sm text-right font-bold text-green-600 dark:text-green-400" rowspan="2">${{ number_format($totalComisionVentas, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100">Electrónica</td>
                        <td class="px-4 py-3 text-sm text-right text-blue-600 dark:text-blue-400 font-semibold">${{ number_format($montoVentaElectronica, 2) }}</td>
                        <td class="px-4 py-3">
                            <input 
                                type="number" 
                                step="0.01"
                                wire:model.live.debounce.500ms="data.comision_data.iva_venta_electronica"
                                class="w-full text-center rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 focus:border-primary-500 focus:ring-primary-500"
                            />
                        </td>
                        <td class="px-4 py-3 text-sm text-right text-blue-600 dark:text-blue-400 font-semibold">${{ number_format($totalVentaElectronica, 2) }}</td>
                    </tr>
                    <tr class="bg-gray-50 dark:bg-gray-800">
                        <td class="px-4 py-3 text-sm font-bold text-gray-900 dark:text-gray-100">TOTAL VENTAS</td>
                        <td class="px-4 py-3 text-sm text-right font-bold text-blue-600 dark:text-blue-400" colspan="3">${{ number_format($montoTotalVentas, 2) }}</td>
                        <td class="px-4 py-3 text-sm text-right font-bold text-green-600 dark:text-green-400" colspan="2">${{ number_format($totalComisionVentas, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Tabla de Abonos --}}
        <div class="overflow-hidden border border-gray-200 dark:border-gray-700 rounded-lg">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-100 dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Concepto</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Monto (Calculado)</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">IVA % (Editable)</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total (Calculado)</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">% Comisión (Editable)</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Comisión (Calculado)</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                    <tr>
                        <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100">Abonos</td>
                        <td class="px-4 py-3 text-sm text-right text-blue-600 dark:text-blue-400 font-semibold">${{ number_format($montoAbonos, 2) }}</td>
                        <td class="px-4 py-3">
                            <input 
                                type="number" 
                                step="0.01"
                                wire:model.live.debounce.500ms="data.comision_data.iva_abonos"
                                class="w-full text-center rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 focus:border-primary-500 focus:ring-primary-500"
                            />
                        </td>
                        <td class="px-4 py-3 text-sm text-right text-blue-600 dark:text-blue-400 font-semibold">${{ number_format($totalAbonos, 2) }}</td>
                        <td class="px-4 py-3">
                            <input 
                                type="number" 
                                step="0.01"
                                wire:model.live.debounce.500ms="data.comision_data.porcentaje_comision_abonos"
                                class="w-full text-center rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 focus:border-primary-500 focus:ring-primary-500"
                            />
                        </td>
                        <td class="px-4 py-3 text-sm text-right font-bold text-green-600 dark:text-green-400">${{ number_format($totalComisionAbonos, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Tabla de Totales --}}
        <div class="overflow-hidden border border-gray-200 dark:border-gray-700 rounded-lg">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-100 dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Concepto</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Monto</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                    <tr>
                        <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100">Subtotal Comisión</td>
                        <td class="px-4 py-3 text-sm text-right text-blue-600 dark:text-blue-400 font-semibold">${{ number_format($subtotalComision, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">Descuento</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">(Editable)</span>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <input 
                                type="number" 
                                step="0.01"
                                wire:model.live.debounce.500ms="data.comision_data.descuento_comision"
                                class="w-full text-right rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 focus:border-primary-500 focus:ring-primary-500"
                            />
                        </td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">Ajuste</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">(Editable)</span>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <input 
                                type="number" 
                                step="0.01"
                                wire:model.live.debounce.500ms="data.comision_data.ajuste_comision"
                                class="w-full text-right rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 focus:border-primary-500 focus:ring-primary-500"
                            />
                        </td>
                    </tr>
                    <tr class="bg-green-50 dark:bg-green-900/20">
                        <td class="px-4 py-3 text-base font-bold text-gray-900 dark:text-gray-100">TOTAL COMISIÓN NETA</td>
                        <td class="px-4 py-3 text-lg text-right font-bold text-green-600 dark:text-green-400">${{ number_format($totalComisionNeta, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Detalle de Pedidos Encontrados --}}
        @if(count($pedidosPreview) > 0)
        <div class="overflow-hidden border border-gray-200 dark:border-gray-700 rounded-lg">
            <div class="bg-gray-100 dark:bg-gray-800 px-4 py-3">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                    📋 Pedidos Encontrados ({{ count($pedidosPreview) }})
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pedido #</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Fecha</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tipo</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Monto</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($pedidosPreview as $pedido)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                            <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">#{{ $pedido['pedido_id'] }}</td>
                            <td class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">{{ \Carbon\Carbon::parse($pedido['fecha_venta'])->format('d/m/Y') }}</td>
                            <td class="px-4 py-2 text-center">
                                <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full 
                                    {{ $pedido['tipo_venta'] === 'REMISIONADA' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400' }}">
                                    {{ $pedido['tipo_venta'] }}
                                </span>
                            </td>
                            <td class="px-4 py-2 text-sm text-right font-semibold text-gray-900 dark:text-gray-100">${{ number_format($pedido['monto_venta'], 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- Detalle de Abonos Encontrados --}}
        @if(count($abonosPreview) > 0)
        <div class="overflow-hidden border border-gray-200 dark:border-gray-700 rounded-lg">
            <div class="bg-gray-100 dark:bg-gray-800 px-4 py-3">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                    💰 Abonos Encontrados ({{ count($abonosPreview) }})
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Abono #</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Fecha</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Monto</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($abonosPreview as $abono)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                            <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">#{{ $abono['abono_id'] }}</td>
                            <td class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">{{ \Carbon\Carbon::parse($abono['fecha_abono'])->format('d/m/Y') }}</td>
                            <td class="px-4 py-2 text-sm text-right font-semibold text-gray-900 dark:text-gray-100">${{ number_format($abono['monto_abono'], 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
            <p class="text-sm text-blue-800 dark:text-blue-200">
                <strong>✨ Cálculo en Tiempo Real:</strong> Los valores se actualizan automáticamente al cambiar cualquier campo. Al guardar, se confirmarán los pedidos, abonos y totales en la base de datos.
            </p>
        </div>
    </div>
</x-dynamic-component>
