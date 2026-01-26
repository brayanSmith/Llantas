import '../css/filament/admin/theme.css';

// Sincroniza precio_unitario de Livewire con Alpine.js cuando cambia el producto
window.addEventListener('livewire:processed', () => {
    document.querySelectorAll('.pos-row-container').forEach(row => {
        // Busca el input de precio_unitario generado por Filament
        const precioInput = row.querySelector('input[name$="[precio_unitario]"]');
        if (precioInput && row.__x) {
            // Actualiza la variable Alpine precio si es diferente
            const precioBackend = parseFloat(precioInput.value);
            if (!isNaN(precioBackend) && row.__x.$data.precio !== precioBackend) {
                row.__x.$data.precio = precioBackend;
            }
        }
    });
});
