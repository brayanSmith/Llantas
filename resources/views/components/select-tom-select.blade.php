@props([
    'options',
    'idKey' => 'id',
    'textKey' => 'nombre',
    'selectId' => 'select-dinamico'
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
                dropdownParent: 'body'
            });
        };
        initTomSelect();
    });
" class="w-full">
    <select id="{{ $selectId }}" {{ $attributes }}>
        @foreach($options as $item)
            <option value="{{ (string) $item[$idKey] }}">{{ $item[$textKey] }}</option>
        @endforeach
    </select>
</div>
