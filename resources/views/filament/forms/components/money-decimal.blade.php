<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div
        x-data="{ state: $wire.$entangle(@js($getStatePath())) }"
        {{ $getExtraAttributeBag() }}
    >
        {{-- Interact with the `state` property in Alpine.js --}}
        <div style="display: flex; align-items: center;">
            <span style="font-weight: bold; margin-right: 4px;">$</span>
            <input
                type="text"
                {{ $attributes->merge(['class' => 'filament-forms-input', 'inputmode' => 'decimal', 'style' => 'width:120px; text-align:right;']) }}
                value="{{ $getState() }}"
                oninput="formateaDecimal(this)"
                onblur="formateaDecimal(this, true)"
            >
        </div>
        <script>
        function formateaDecimal(input, force = false) {
            let value = input.value.replace(/[^0-9.,]/g, '');
            value = value.replace(/,/g, '.');
            if (value) {
                let num = parseFloat(value);
                if (!isNaN(num)) {
                    input.value = num.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                } else if(force) {
                    input.value = '0.00';
                }
            } else if(force) {
                input.value = '0.00';
            }
        }
        </script>
    </div>
</x-dynamic-component>
