<div {{ $getExtraAttributeBag() }} class="flex justify-center">
    <a href="{{ route('pedidosFacturados.pdf.download', $getRecord()->id) }}"
       target="_blank"
       title="Descargar PDF"
       class="inline-flex items-center justify-center p-2 text-gray-500 bg-gray-100 hover:bg-blue-500 hover:text-white rounded-md transition duration-200">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
        </svg>
    </a>
</div>
