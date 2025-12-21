<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    @php
        $record = $getRecord();
    @endphp

    <div class="space-y-4">
        {{-- Información General --}}
        <div class="grid grid-cols-4 gap-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
            <div>
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Vendedor</span>
                <p class="text-sm font-semibold">{{ $record->vendedor->name ?? 'N/A' }}</p>
            </div>
            <div>
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Periodo Inicial</span>
                <p class="text-sm font-semibold">{{ $record->periodo_inicial instanceof \Carbon\Carbon ? $record->periodo_inicial->format('d/m/Y') : $record->periodo_inicial }}</p>
            </div>
            <div>
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Periodo Final</span>
                <p class="text-sm font-semibold">{{ $record->periodo_final instanceof \Carbon\Carbon ? $record->periodo_final->format('d/m/Y') : $record->periodo_final }}</p>
            </div>
            <div>
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Estado</span>
                <p>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        @if($record->estado_comision === 'PAGADA') bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100
                        @elseif($record->estado_comision === 'RECHAZADA') bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100
                        @else bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100
                        @endif">
                        {{ $record->estado_comision }}
                    </span>
                </p>
            </div>
        </div>

        {{-- Tabla de Ventas --}}
        <div class="overflow-hidden border border-gray-200 dark:border-gray-700 rounded-lg">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-100 dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tipo Venta</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Monto</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">IVA %</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">% Comisión</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Comisión</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                    <tr>
                        <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100">Remisionada</td>
                        <td class="px-4 py-3 text-sm text-right text-gray-900 dark:text-gray-100">${{ number_format($record->monto_venta_remisionada, 2) }}</td>
                        <td class="px-4 py-3 text-sm text-right text-gray-900 dark:text-gray-100">{{ number_format($record->iva_venta_remisionada, 2) }}%</td>
                        <td class="px-4 py-3 text-sm text-right text-gray-900 dark:text-gray-100">${{ number_format($record->total_venta_remisionada, 2) }}</td>
                        <td class="px-4 py-3 text-sm text-right text-gray-900 dark:text-gray-100" rowspan="2">{{ number_format($record->porcentaje_comision_ventas, 2) }}%</td>
                        <td class="px-4 py-3 text-sm text-right font-semibold text-gray-900 dark:text-gray-100" rowspan="2">${{ number_format($record->total_comision_ventas, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100">Electrónica</td>
                        <td class="px-4 py-3 text-sm text-right text-gray-900 dark:text-gray-100">${{ number_format($record->monto_venta_electronica, 2) }}</td>
                        <td class="px-4 py-3 text-sm text-right text-gray-900 dark:text-gray-100">{{ number_format($record->iva_venta_electronica, 2) }}%</td>
                        <td class="px-4 py-3 text-sm text-right text-gray-900 dark:text-gray-100">${{ number_format($record->total_venta_electronica, 2) }}</td>
                    </tr>
                    <tr class="bg-gray-50 dark:bg-gray-800">
                        <td class="px-4 py-3 text-sm font-bold text-gray-900 dark:text-gray-100">TOTAL VENTAS</td>
                        <td class="px-4 py-3 text-sm text-right font-bold text-gray-900 dark:text-gray-100" colspan="3">${{ number_format($record->monto_total_ventas, 2) }}</td>
                        <td class="px-4 py-3 text-sm text-right font-bold text-gray-900 dark:text-gray-100" colspan="2">${{ number_format($record->total_comision_ventas, 2) }}</td>
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
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Monto</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">IVA %</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">% Comisión</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Comisión</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                    <tr>
                        <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100">Abonos</td>
                        <td class="px-4 py-3 text-sm text-right text-gray-900 dark:text-gray-100">${{ number_format($record->monto_abonos, 2) }}</td>
                        <td class="px-4 py-3 text-sm text-right text-gray-900 dark:text-gray-100">{{ number_format($record->iva_abonos, 2) }}%</td>
                        <td class="px-4 py-3 text-sm text-right text-gray-900 dark:text-gray-100">${{ number_format($record->total_abonos, 2) }}</td>
                        <td class="px-4 py-3 text-sm text-right text-gray-900 dark:text-gray-100">{{ number_format($record->porcentaje_comision_abonos, 2) }}%</td>
                        <td class="px-4 py-3 text-sm text-right font-semibold text-gray-900 dark:text-gray-100">${{ number_format($record->total_comision_abonos, 2) }}</td>
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
                        <td class="px-4 py-3 text-sm text-right text-gray-900 dark:text-gray-100">${{ number_format($record->subtotal_comision, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100">Descuento</td>
                        <td class="px-4 py-3 text-sm text-right text-red-600 dark:text-red-400">-${{ number_format($record->descuento_comision, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100">Ajuste</td>
                        <td class="px-4 py-3 text-sm text-right text-gray-900 dark:text-gray-100">${{ number_format($record->ajuste_comision, 2) }}</td>
                    </tr>
                    <tr class="bg-green-50 dark:bg-green-900/20">
                        <td class="px-4 py-3 text-base font-bold text-gray-900 dark:text-gray-100">TOTAL COMISIÓN NETA</td>
                        <td class="px-4 py-3 text-lg text-right font-bold text-green-600 dark:text-green-400">${{ number_format($record->total_comision_neta, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Fechas --}}
        <div class="grid grid-cols-2 gap-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg text-xs">
            <div>
                <span class="text-gray-500 dark:text-gray-400">Creado:</span>
                <span class="text-gray-900 dark:text-gray-100">{{ $record->created_at instanceof \Carbon\Carbon ? $record->created_at->format('d/m/Y H:i') : $record->created_at }}</span>
            </div>
            <div>
                <span class="text-gray-500 dark:text-gray-400">Actualizado:</span>
                <span class="text-gray-900 dark:text-gray-100">{{ $record->updated_at instanceof \Carbon\Carbon ? $record->updated_at->format('d/m/Y H:i') : $record->updated_at }}</span>
            </div>
        </div>
    </div>
</x-dynamic-component>
