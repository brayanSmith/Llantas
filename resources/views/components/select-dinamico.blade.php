@props([
    'label',
    'placeholder',
    'model',
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
        const modelPath = '{{ $model }}'.split('.');
        const getModel = () => modelPath.reduce((a, b) => a && a[b], $data);
        const setModel = (val) => {
            let ref = $data;
            for (let i = 0; i < modelPath.length - 1; i++) {
                ref = ref[modelPath[i]];
            }
            ref[modelPath[modelPath.length - 1]] = val;
        };
        const tom = new TomSelect(select, {
            placeholder: '{{ $placeholder }}',
            allowEmptyOption: true,
            dropdownParent: 'body',
            onChange: function(value) {
                setModel(value);
            }
        });
        tom.setValue(getModel());
        $watch('{{ $model }}', value => {
            tom.setValue(value);
        });
    });
" class="w-full">
    <select x-model="{{ $model }}" id="{{ $selectId }}" {{ $attributes }}>
        <option value="">{{ $placeholder }}</option>
        @foreach($options as $item)
            <option value="{{ $item[$idKey] }}">{{ $item[$textKey] }}</option>
        @endforeach
    </select>
</div>
