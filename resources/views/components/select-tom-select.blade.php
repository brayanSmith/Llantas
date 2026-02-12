@props([
    'options',
    'idKey' => 'id',
    'textKey' => 'nombre',
    'selectId' => 'select-dinamico',
    'placeholder' => 'Seleccione una opción...'
])
<div x-data="{}" x-init="
    $nextTick(() => {
        const select = $el.querySelector('select');
        if (select.tomselect) {
            select.tomselect.destroy();
        }
        let tom;
        const initTomSelect = () => {
            tom = new TomSelect(select, {
                allowEmptyOption: true,
                dropdownParent: 'body',
                placeholder: '{{ $placeholder }}',
                zIndex: 999999,
                onChange: function(value) {
                    // Disparar evento change para que Alpine.js lo capture
                    select.dispatchEvent(new Event('change', { bubbles: true }));
                }
            });
        };
        initTomSelect();
    });
" class="w-full">
    <select id="{{ $selectId }}" {{ $attributes }}>
        <option value="">{{ $placeholder }}</option>
        @foreach($options as $item)
            <option value="{{ (string) $item[$idKey] }}">{{ $item[$textKey] }}</option>
        @endforeach
    </select>
</div>
