<?php

namespace App\Livewire;

use App\Models\Bodega;
use App\Models\Categoria;
use App\Models\Compra;
use App\Models\Producto;
use App\Models\Proveedor;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class CompraFormLivewire extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public array $proveedores = [];
    public array $bodegas = [];
    public array $productos = [];
    public array $categorias = [];
    public bool $showConfirmModal = false;
    public string $confirmModalTitle = '';
    public string $confirmModalBody = '';
    public ?object $imagenCompra = null;
    public ?string $imagenCompraPath = null;
    public array $compra = [];
    public array $detallesCompra = [];
    public bool $esEdicion = false;

    protected function getCompraDefaults(): array
    {
        return [
            'factura' => null,
            'proveedor_id' => null,
            'fecha' => now()->toDateString(),
            'estado' => 'PENDIENTE',
            'observaciones' => '',
            'subtotal' => 0,
            'descuento' => 0,
            'total_a_pagar' => 0,
        ];
    }

    public function mount(): void
    {
        $this->cargarDatos();
        $this->cargarCompra(request()->get('compra_id'));
    }

    /**
     * Carga los datos maestros del sistema
     */
    protected function cargarDatos(): void
    {
        $this->proveedores = Proveedor::select('id', 'nombre_proveedor')->get()->toArray();
        $this->bodegas = Bodega::select('id', 'nombre_bodega')->get()->toArray();
        $this->categorias = Categoria::select('id', 'nombre_categoria')->get()->toArray();
        $this->productos = $this->getFilteredProductos();
    }

    /**
     * Carga una compra específica o inicializa una nueva
     */
    protected function cargarCompra(?int $compraId): void
    {
        $this->compra = $this->getCompraDefaults();

        if (!$compraId) {
            $this->detallesCompra = [];
            return;
        }

        $compraEncontrada = Compra::with('detallesCompra', 'proveedor')
            ->find($compraId);

        if ($compraEncontrada) {
            $this->esEdicion = true;
            $this->compra = array_merge($this->getCompraDefaults(), $compraEncontrada->toArray());
            $this->detallesCompra = $compraEncontrada->detallesCompra->toArray();
        }
    }

    protected function getFilteredProductos(): array
    {
        return Producto::select(
            'id',
            'categoria_id',
            'concatenar_codigo_nombre',
            'nombre_producto',
            'codigo_producto',
        )->with('categoria')->get()->toArray();
    }

    /**
     * Crea una nueva compra desde JSON
     *
     * @param array $json Datos de la compra en formato JSON
    * {
    *   "compra": {
    *     "factura": "FAC01",
    *     "proveedor_id": 1,
    *     "fecha": "2026-02-02",
    *     "estado": "PENDIENTE",
    *     "observaciones": "NA",
    *     "subtotal": 10000,
    *     "descuento": 500,
    *     "total_a_pagar": 9500,
    *     "detallesCompra": [...]
    *   }
    * }
     */
    public function crearCompra(array $json): void
    {
        try {
            $datos_validados = $this->validarJsonCreacion($json);
            $this->procesarCreacionCompra($datos_validados);
            $this->mostrarMensajeExito('¡Compra exitosa!', 'La compra fue ingresada exitosamente.');
        } catch (\Exception $e) {
            $this->mostrarMensajeError('Error', 'No se pudo guardar la compra: ' . $e->getMessage());
        }
    }

    /**
     * Actualiza una compra existente desde JSON
     *
     * @param int $compraId ID de la compra a actualizar
     * @param array $json Datos de la compra en formato JSON
     * {
     *   "compra_id": 45,
     *   "factura": "FAC01-EDITADA",
     *   "proveedor_id": 1,
     *   ...
     *   "detallesCompra": [...]
     * }
     */
    public function editarCompra(int $compraId, array $json): void
    {
        try {
            \Log::info('=== EDITAR COMPRA ===', [
                'compraId' => $compraId,
                'json_recibido' => $json,
                'detalles' => $json['detallesCompra'] ?? $json['compra']['detallesCompra'] ?? []
            ]);

            $datos_validados = $this->validarJsonEdicion($json, $compraId);

            \Log::info('Datos validados:', [
                'detalles_validados' => $datos_validados['detalles']
            ]);

            $this->procesarEdicionCompra($compraId, $datos_validados);
            $this->mostrarMensajeExito('¡Compra actualizada!', 'La compra fue actualizada exitosamente.');
        } catch (\Exception $e) {
            \Log::error('Error editando compra:', ['error' => $e->getMessage()]);
            $this->mostrarMensajeError('Error', 'No se pudo actualizar la compra: ' . $e->getMessage());
        }
    }

    /**
     * Valida el JSON para creación de compra
     */
    protected function validarJsonCreacion(array $json): array
    {
        if (!isset($json['compra']) || !is_array($json['compra'])) {
            throw new \InvalidArgumentException('El JSON debe contener la clave "compra"');
        }

        $compra = $json['compra'];

        if (empty($compra['factura'])) {
            throw new \InvalidArgumentException('La factura es obligatoria');
        }
        if (empty($compra['proveedor_id'])) {
            throw new \InvalidArgumentException('El proveedor es obligatorio');
        }

        $detalles = $compra['detallesCompra'] ?? ($json['detallesCompra'] ?? []);

        return [
            'compra' => $compra,
            'detalles' => $detalles,
        ];
    }

    /**
     * Valida el JSON para edición de compra
     */
    protected function validarJsonEdicion(array $json, int $compraId): array
    {
        $data = $json['compra'] ?? $json;
        $jsonCompraId = $json['compra_id'] ?? $data['compra_id'] ?? null;

        if ($jsonCompraId !== null && (int) $jsonCompraId !== $compraId) {
            throw new \InvalidArgumentException('El compra_id no coincide con el id recibido');
        }

        if (empty($data['factura'])) {
            throw new \InvalidArgumentException('La factura es obligatoria');
        }
        if (empty($data['proveedor_id'])) {
            throw new \InvalidArgumentException('El proveedor es obligatorio');
        }

        return [
            'compra_id' => $compraId,
            'factura' => $data['factura'],
            'proveedor_id' => $data['proveedor_id'],
            'fecha' => $data['fecha'] ?? now()->toDateString(),
            'estado' => $data['estado'] ?? 'PENDIENTE',
            'observaciones' => $data['observaciones'] ?? '',
            'subtotal' => $data['subtotal'] ?? 0,
            'descuento' => $data['descuento'] ?? 0,
            'total_a_pagar' => $data['total_a_pagar'] ?? 0,
            'detalles' => $data['detallesCompra'] ?? ($json['detallesCompra'] ?? []),
        ];
    }

    /**
     * Procesa la creación de una compra validada
     */
    protected function procesarCreacionCompra(array $datos_validados): void
    {
        $compra_data = $datos_validados['compra'];

        $compra = Compra::create([
            'factura' => $compra_data['factura'],
            'proveedor_id' => $compra_data['proveedor_id'],
            'fecha' => $compra_data['fecha'] ?? now()->toDateString(),
            'estado' => $compra_data['estado'] ?? 'PENDIENTE',
            'observaciones' => $compra_data['observaciones'] ?? '',
            'subtotal' => $compra_data['subtotal'] ?? 0,
            'descuento' => $compra_data['descuento'] ?? 0,
            'total_a_pagar' => $compra_data['total_a_pagar'] ?? 0,
        ]);

        $this->crearDetalles($compra, $datos_validados['detalles']);
        $this->actualizarCostosProductos($datos_validados['detalles']);
        $this->guardarPdfUrl($compra->id);
    }

    /**
     * Procesa la edición de una compra validada
     */
    protected function procesarEdicionCompra(int $compraId, array $datos_validados): void
    {
        $compra = Compra::findOrFail($compraId);

        $compra->update([
            'factura' => $datos_validados['factura'],
            'proveedor_id' => $datos_validados['proveedor_id'],
            'fecha' => $datos_validados['fecha'],
            'estado' => $datos_validados['estado'],
            'observaciones' => $datos_validados['observaciones'],
            'subtotal' => $datos_validados['subtotal'],
            'descuento' => $datos_validados['descuento'],
            'total_a_pagar' => $datos_validados['total_a_pagar'],
        ]);

        $this->sincronizarDetallesConAccion($compra, $datos_validados['detalles']);
        $this->actualizarCostosProductos($datos_validados['detalles']);
        $this->guardarPdfUrl($compra->id);
    }


    /**
     * Crea nuevos detalles para una compra
     */
    protected function crearDetalles(Compra $compra, array $detalles): void
    {
        foreach ($detalles as $detalle) {
            $compra->detallesCompra()->create($this->transformarDetalle($detalle));
        }
    }

    /**
     * Sincroniza detalles con acciones específicas (create, update)
     */
    protected function sincronizarDetallesConAccion(Compra $compra, array $detalles): void
    {
        \Log::info('=== SINCRONIZAR DETALLES ===', [
            'compra_id' => $compra->id,
            'cantidad_detalles' => count($detalles),
            'detalles' => $detalles
        ]);

        foreach ($detalles as $index => $detalle) {
            $accion = $detalle['accion'] ?? 'update';

            \Log::info("Procesando detalle #{$index}", [
                'detalle_id' => $detalle['id'] ?? 'null',
                'producto_id' => $detalle['producto_id'] ?? 'null',
                'bodega_id' => $detalle['bodega_id'] ?? 'null',
                'accion' => $accion
            ]);

            if ($accion === 'create') {
                \Log::info('CREANDO detalle nuevo');
                $nuevoDetalle = $compra->detallesCompra()->create($this->transformarDetalle($detalle));
                \Log::info('Detalle creado con ID: ' . $nuevoDetalle->id);
            } elseif ($accion === 'update' && isset($detalle['id'])) {
                \Log::info('ACTUALIZANDO detalle existente con ID: ' . $detalle['id']);
                $detalleExistente = $compra->detallesCompra()->find($detalle['id']);
                if ($detalleExistente) {
                    $detalleExistente->update($this->transformarDetalle($detalle));
                    \Log::info('Detalle actualizado correctamente');
                } else {
                    \Log::warning('Detalle con ID ' . $detalle['id'] . ' no encontrado');
                }
            } else {
                \Log::warning('Detalle ignorado', ['detalle' => $detalle]);
            }
        }

        // Eliminación automática deshabilitada - se controla vía accion: 'create'/'update'
        // Los detalles que el usuario elimina ya no se envían en el payload
    }

    /**
     * Transforma un detalle al formato esperado por la base de datos
     */
    protected function transformarDetalle(array $detalle): array
    {
        return [
            'producto_id' => $detalle['producto_id'],
            'bodega_id' => $detalle['bodega_id'] ?? null,
            'cantidad' => $detalle['cantidad'] ?? 0,
            'precio_unitario' => $detalle['precio_unitario'] ?? 0,
            'subtotal' => $detalle['subtotal'] ?? 0,
        ];
    }

    /**
     * Actualiza el costo de productos basado en los precios unitarios de la compra
     */
    protected function actualizarCostosProductos(array $detalles): void
    {
        foreach ($detalles as $detalle) {
            if (isset($detalle['producto_id']) && isset($detalle['precio_unitario'])) {
                $producto = Producto::find($detalle['producto_id']);

                if ($producto) {
                    $producto->update([
                        'costo_producto' => $detalle['precio_unitario']
                    ]);
                }
            }
        }
    }

    /**
     * Guarda las URLs del PDF en la sesión
     */
    protected function guardarPdfUrl(int $compraId): void
    {
        session([
            'compras_stream_pdf_url' => route('compras-pdf.stream', $compraId),
            'compras_download_pdf_url' => route('compras-pdf.download', $compraId),
        ]);
    }

    /**
     * Muestra un mensaje de éxito
     */
    protected function mostrarMensajeExito(string $titulo, string $mensaje): void
    {
        $this->confirmModalTitle = $titulo;
        $this->confirmModalBody = $mensaje;
        $this->showConfirmModal = true;
    }

    /**
     * Muestra un mensaje de error
     */
    protected function mostrarMensajeError(string $titulo, string $mensaje): void
    {
        $this->confirmModalTitle = $titulo;
        $this->confirmModalBody = $mensaje;
        $this->showConfirmModal = true;
    }

    public function render(): View
    {
        return view('livewire.compras.compra-form-livewire', [
            'proveedores' => $this->proveedores,
            'bodegas' => $this->bodegas,
            'productos' => $this->productos,
            'compraEncontrada' => $this->compra,
            'detalles_compra' => $this->detallesCompra,
            'esEdicion' => $this->esEdicion,
            'categorias' => $this->categorias,
        ]);
    }
}
