<div x-data="cotizacionForm(@js($productos), @js($bodegas))" x-init="init()" class="space-y-4">

    @include('livewire.cotizacion.livewire-cotizacion-buscador')

    @vite(['resources/js/app.js'])

    <script>
        function cotizacionForm(productos = [], bodegas = [], referenciaIngresada = '100/80-17', tipoPrecioSeleccionado =
            'DETAL') {
            return {
                productos,
                bodegas,
                referenciaIngresada,
                tipoPrecioSeleccionado,
                salida: '',
                copiado: false,
                init() {
                    console.log('Productos:', this.productos);
                    console.log('Bodegas:', this.bodegas);
                    console.log('Referencia ingresada:', this.referenciaIngresada);
                    console.log('Tipo de precio seleccionado:', this.tipoPrecioSeleccionado);
                },
                buscarProducto(referenciaIngresada, tipoPrecioSeleccionado) {
                    const referencia = referenciaIngresada.trim();
                    const productosEncontrados = this.productos.filter(producto => producto.referencia_producto ===
                        referencia);

                    if (productosEncontrados.length > 0) {
                        let salida = `*Modelos para la referencia ${referencia}:*\n`;
                        let notas = [];
                        productosEncontrados.forEach(producto => {
                            let precio = '';
                            if (this.tipoPrecioSeleccionado === 'DETAL') {
                                precio = producto.valor_detal;
                            } else if (this.tipoPrecioSeleccionado === 'MAYORISTA') {
                                precio = producto.valor_mayorista;
                            } else {
                                precio = producto.valor_detal;
                            }
                            salida += `\n• ${producto.concatenar_codigo_nombre}: $ ${precio}`;

                            if (producto.concatenar_codigo_nombre.includes('R13')) {
                                if (!notas.includes('Aplica x2 unidades. Incluye instalación + válvula.')) {
                                    notas.push('Aplica x2 unidades. Incluye instalación + válvula.');
                                }
                            }
                            if (producto.concatenar_codigo_nombre.includes('ROVELO') || producto
                                .concatenar_codigo_nombre.includes('WESTLAKE')) {
                                if (!notas.includes('3 años de garantía.')) {
                                    notas.push('3 años de garantía.');
                                }
                            }
                        });
                        if (notas.length > 0) {
                            salida += `\n\n*Notas:*`;
                            notas.forEach(nota => {
                                salida += `\n-  ${nota}`;
                            });
                        }
                        salida += `\n\nSujeto a disponibilidad.`;
                        this.salida = salida;
                    } else {
                        this.salida = `No se encontró ningún producto con la referencia: ${referencia}`;
                    }
                },

                copiarSalida() {
                    if (this.salida) {
                        navigator.clipboard.writeText(this.salida).then(() => {
                            this.copiado = true;
                            setTimeout(() => { this.copiado = false; }, 2000);
                        });
                    }
                }
            }
        }
    </script>

</div>
