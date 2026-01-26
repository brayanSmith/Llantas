<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div
        x-data="{ state: $wire.$entangle(@js($getStatePath())), tipo_precio: 'DETAL' }"
        x-init="() => { $watch('tipo_precio', value => { window.dispatchEvent(new CustomEvent('tipo-precio-changed', { detail: value })); }); }"
        {{ $getExtraAttributeBag() }}
    >
        <div class="mb-4">
            <label for="tipo_precio" class="font-bold mr-2">Tipo de precio:</label>
            <select id="tipo_precio" x-model="tipo_precio" class="input">
                <option value="DETAL">Detal</option>
                <option value="MAYORISTA">Mayorista</option>
                <option value="FERRETERO">Ferretero</option>
            </select>
        </div>
        {{-- Interact with the `state` property in Alpine.js --}}
    </div>
</x-dynamic-component>
