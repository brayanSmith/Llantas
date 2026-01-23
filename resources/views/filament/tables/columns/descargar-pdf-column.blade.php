<div {{ $getExtraAttributeBag() }} class="flex gap-2 justify-center">
    <a href="{{ route('pedidosFacturados.pdf.download', $getRecord()->id) }}"
       target="_blank"
       title="Descargar PDF Facturado"
       class="inline-flex items-center justify-center p-2 text-white bg-blue-600 hover:bg-blue-800 rounded-md transition duration-200">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
        </svg>
    </a>
    <a href="{{ route('pedidos.pdf.download', $getRecord()->id) }}"
       target="_blank"
       title="Descargar PDF Pendiente"
       class="inline-flex items-center justify-center p-2 text-white bg-red-600 hover:bg-red-800 rounded-md transition duration-200">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
        </svg>
    </a>
</div>
