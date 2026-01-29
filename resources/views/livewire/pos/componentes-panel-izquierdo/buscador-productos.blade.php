<div class="flex-shrink-0 mb-4">
    <input :value="search" @input="setSearch($event.target.value)" @keydown.enter.prevent type="text" placeholder="Buscar productos por nombre o SKU..."
        class="w-full px-5 py-3 border focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors
                       dark:bg-neutral-800 dark:border-blue-700 dark:text-gray-100">

    <template x-if="error">
        <div class="mt-2 p-4 bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300 rounded-lg shadow-md" x-text="error"></div>
    </template>

    <template x-if="success">
        <div class="mt-2 p-4 bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 rounded-lg shadow-md" x-text="success"></div>
    </template>
</div>
