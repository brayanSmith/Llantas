
<div x-data x-init="
    $nextTick(() => {
        const select = $el.querySelector('select');
        if (select.tomselect) {
            select.tomselect.destroy();
        }
        const tom = new TomSelect(select, {
            placeholder: placeholder,
            allowEmptyOption: true,
            onChange: function(value) {
                $eval(model + ' = value');
            }
        });
        tom.setValue($eval(model));
        $watch(model, value => {
            tom.setValue(value);
        });
    });
" class="w-full">
    <label x-text="label"></label>
    <select :x-model="model" :id="selectId">
        <option value="" x-text="placeholder"></option>
        <template x-for="item in options" :key="item[idKey]">
            <option :value="item[idKey]" x-text="item[textKey]"></option>
        </template>
    </select>
</div>
