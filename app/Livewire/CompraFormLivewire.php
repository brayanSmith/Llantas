<?php

namespace App\Livewire;

use App\Models\Bodega;
use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\Puc;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class CompraFormLivewire extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public array $proveedores = [];
    public array $bodegas = [];
    public array $productos = [];
    public array $pucs = [];
    public $showConfirmModal = false;
    public $confirmModalTitle = '';
    public $confirmModalBody = '';
    public array $compra = [];
    public array $detallesCompra = [];
    public bool $esEdicion = false;

    protected function getCompraDefaults(): array
    {
        return [
            'item_compra' => 'PRODUCTO',
            'categoria_compra' => 'OTRO',
            'factura' => null,
            'proveedor_id' => null,
            'fecha' => now()->toDateString(),
            'dias_plazo_vencimiento' => 30,
            'fecha_vencimiento' => now()->addDays(30)->toDateString(),
            'metodo_pago' => 'CREDITO',
            'estado' => 'PENDIENTE',
            'bodega_id' => null,
            'observaciones' => '',
            'subtotal' => 0,
            'abono' => 0,
            'descuento' => 0,
            'total_a_pagar' => 0,
            'saldo_pendiente' => 0,
            'estado_pago' => 'EN_CARTERA',
            'tipo_compra' => 'ELECTRONICA',
        ];
    }

    public function mount(): void
    {
        $compraId = request()->get('compra_id');
        $this->compra = $this->getCompraDefaults();
        if ($compraId) {
            $this->esEdicion = true;
            // Cargar la relación bodega junto con el pedido
            $compraEncontrada = Compra::with('bodega', 'detallesCompra', 'proveedor')->find($compraId);
            if ($compraEncontrada) {
                $this->compra = array_merge($this->getCompraDefaults(), $compraEncontrada->toArray());
            }
            $this->detallesCompra = DetalleCompra::where('compra_id', $compraId)->get()->toArray();
        } else {
            $this->detallesCompra = [];
        }
        $this->proveedores = Proveedor::select('id', 'nombre_proveedor')->get()->toArray();
        $this->bodegas = Bodega::select('id', 'nombre_bodega')->get()->toArray();
        $this->productos = $this->getFilteredProductos();
        $this->pucs = $this->getFilteredPuc();
    }
    protected function getFilteredProductos()
    {
        return Producto::select(
            'id',
            'categoria_producto',
            'concatenar_codigo_nombre',
            'valor_detal_producto',
            'valor_mayorista_producto',
            'valor_ferretero_producto',
            'iva_producto',
            'nombre_producto',
            'codigo_producto',
        )->where('activo', 1)
            ->get()->toArray();
    }
    protected function getFilteredPuc()
    {
        return Puc::select(
            'id',
            'cuenta',
            'subcuenta',
            'concepto',
            'descripcion',
            'concatenar_subcuenta_concepto',
        )->get()->toArray();
    }
    public function guardarCompra($compra)
    {
        \Log::info('GUARDAR compra recibida desde Alpine', ['compra' => $compra]);

        $payload = [
            'factura' => $compra['factura'] ?? null,
            'proveedor_id' => $compra['proveedor_id'] ?? null,
            'fecha' => $compra['fecha'] ?? now()->toDateString(),
            'dias_plazo_vencimiento' => $compra['dias_plazo_vencimiento'] ?? 30,
            'fecha_vencimiento' => $compra['fecha_vencimiento'] ?? now()->addDays(30)->toDateString(),
            'metodo_pago' => $compra['metodo_pago'] ?? 'CREDITO',
            'estado_pago' => $compra['estado_pago'] ?? 'EN_CARTERA',
            'tipo_compra' => $compra['tipo_compra'] ?? 'ELECTRONICA',
            'estado' => $compra['estado'] ?? 'PENDIENTE',
            'observaciones' => $compra['observaciones'] ?? '',
            'subtotal' => $compra['subtotal'] ?? 0,
            'abono' => $compra['abono'] ?? 0,
            'descuento' => $compra['descuento'] ?? 0,
            'total_a_pagar' => $compra['total_a_pagar'] ?? 0,
            'saldo_pendiente' => $compra['saldo_pendiente'] ?? 0,
            'categoria_compra' => $compra['categoria_compra'] ?? null,
            'item_compra' => $compra['item_compra'] ?? 'PRODUCTO',
            'bodega_id' => $compra['bodega_id'] ?? null,
        ];

        \Log::info('GUARDAR compra payload a insertar', $payload);
        \Log::info('GUARDAR compra detalles', ['detalles' => $compra['detalles_compra'] ?? []]);

        $nuevaCompra = Compra::create($payload);

        \Log::info('GUARDAR compra CREADA en BD', [
            'id' => $nuevaCompra->id,
            'subtotal' => $nuevaCompra->subtotal,
            'total_a_pagar' => $nuevaCompra->total_a_pagar,
            'fresh_subtotal' => $nuevaCompra->fresh()->subtotal,
            'fresh_total_a_pagar' => $nuevaCompra->fresh()->total_a_pagar,
        ]);

        foreach (($compra['detalles_compra'] ?? []) as $detalle) {
            $nuevaCompra->detallesCompra()->create([
                'item_id' => $detalle['producto_id'],
                'descripcion_item' => $detalle['descripcion_item'] ?? '',
                'cantidad' => $detalle['cantidad'],
                'precio_unitario' => $detalle['precio_unitario'] ?? 0,
                'iva' => $detalle['iva'] ?? 0,
                'precio_con_iva' => $detalle['precio_con_iva'] ?? 0,
                'subtotal' => $detalle['subtotal'] ?? 0,
                'tipo_item' => $detalle['tipo_item'] ?? 'producto',
            ]);
        }

        \Log::info('GUARDAR compra DESPUÉS de agregar detalles', [
            'id' => $nuevaCompra->id,
            'detalles_count' => $nuevaCompra->detallesCompra()->count(),
            'subtotal' => $nuevaCompra->subtotal,
            'total_a_pagar' => $nuevaCompra->total_a_pagar,
            'saldo_pendiente' => $nuevaCompra->saldo_pendiente,
            'fresh_subtotal' => $nuevaCompra->fresh()->subtotal,
            'fresh_total_a_pagar' => $nuevaCompra->fresh()->total_a_pagar,
            'fresh_saldo_pendiente' => $nuevaCompra->fresh()->saldo_pendiente,
        ]);

        // Guardar la URL del PDF en la sesión para mostrar el botón en la modal
        session(['compras_stream_pdf_url' => route('compras-pdf.stream', $nuevaCompra->id)]);
        session(['compras_download_pdf_url' => route('compras-pdf.download', $nuevaCompra->id)]);
        $this->showConfirmModal = true;
        $this->confirmModalTitle = '¡Compra exitosa!';
        $this->confirmModalBody = 'La compra fue ingresada exitosamente.';
    }

    public function editarCompra($compra)
    {
        \Log::info('EDITAR/CREAR compra recibida desde Alpine', ['compra' => $compra]);

        // Buscar la compra por el código
        $compraExistente = Compra::where('id', $compra['id'])->first();

        if (!$compraExistente) {
            $this->confirmModalTitle = 'Error';
            $this->confirmModalBody = 'No se encontró la compra a editar.';
            $this->showConfirmModal = true;
            return;
        }

        $payload = [
            'factura' => $compra['factura'],
            'proveedor_id' => $compra['proveedor_id'],
            'fecha' => empty($compra['fecha']) ? now()->toDateString() : $compra['fecha'],
            'dias_plazo_vencimiento' => $compra['dias_plazo_vencimiento'],
            'fecha_vencimiento' => empty($compra['fecha_vencimiento']) ? now()->addDays(30)->toDateString() : $compra['fecha_vencimiento'],
            'metodo_pago' => $compra['metodo_pago'],
            'estado_pago' => $compra['estado_pago'],
            'tipo_compra' => $compra['tipo_compra'],
            'estado' => $compra['estado'],
            'observaciones' => $compra['observaciones'],
            'subtotal' => $compra['subtotal'],
            'abono' => $compra['abono'],
            'descuento' => $compra['descuento'],
            'total_a_pagar' => $compra['total_a_pagar'],
            'categoria_compra' => $compra['categoria_compra'],
            'item_compra' => $compra['item_compra'],
            'bodega_id' => $compra['bodega_id'],
            'saldo_pendiente' => $compra['saldo_pendiente'],
            'solicitado' => $compra['solicitado'],
        ];

        \Log::info('EDITAR compra payload a actualizar', $payload);

        // Actualizar los campos de la compra
        $compraExistente->update($payload);

        $detallesActuales = $compraExistente->detallesCompra()->get()->keyBy('item_id');
        $nuevosIds = collect($compra['detalles_compra'])->pluck('producto_id')->all();

        // Actualizar o crear
        foreach ($compra['detalles_compra'] as $detalle) {
            $detalleExistente = $detallesActuales->firstWhere('item_id', $detalle['producto_id']);
            if ($detalleExistente) {
                \Log::info('ACTUALIZANDO detalle existente', [
                    'producto_id' => $detalle['producto_id'],
                    'cantidad' => $detalle['cantidad'],
                    'subtotal' => $detalle['subtotal']
                ]);
                $detalleExistente->update([
                    'descripcion_item' => $detalle['descripcion_item'] ?? '',
                    'cantidad' => $detalle['cantidad'],
                    'precio_unitario' => $detalle['precio_unitario'] ?? 0,
                    'iva' => $detalle['iva'] ?? 0,
                    'precio_con_iva' => $detalle['precio_con_iva'] ?? 0,
                    'subtotal' => $detalle['subtotal'] ?? 0,
                    'tipo_item' => $detalle['tipo_item'] ?? 'producto',
                ]);
            } else {
                \Log::info('CREANDO nuevo detalle', [
                    'producto_id' => $detalle['producto_id'],
                    'cantidad' => $detalle['cantidad'],
                    'subtotal' => $detalle['subtotal']
                ]);
                $compraExistente->detallesCompra()->create([
                    'item_id' => $detalle['producto_id'],
                    'descripcion_item' => $detalle['descripcion_item'] ?? '',
                    'cantidad' => $detalle['cantidad'],
                    'precio_unitario' => $detalle['precio_unitario'] ?? 0,
                    'iva' => $detalle['iva'] ?? 0,
                    'precio_con_iva' => $detalle['precio_con_iva'] ?? 0,
                    'subtotal' => $detalle['subtotal'] ?? 0,
                    'tipo_item' => $detalle['tipo_item'] ?? 'producto',
                ]);
            }
        }

        // Eliminar los que ya no están
        $idsAEliminar = $detallesActuales->keys()->diff($nuevosIds);
        if ($idsAEliminar->isNotEmpty()) {
            $compraExistente->detallesCompra()->whereIn('item_id', $idsAEliminar)->delete();
        }


        // Guardar la URL del PDF en la sesión para mostrar el botón en la modal
        session(['compras_stream_pdf_url' => route('compras-pdf.stream', $compraExistente->id)]);
        session(['compras_download_pdf_url' => route('compras-pdf.download', $compraExistente->id)]);
        $this->showConfirmModal = true;
        $this->confirmModalTitle = '¡Venta exitosa!';
        $this->confirmModalBody = 'El pedido fue ingresado exitosamente.';
    }

    public function render(): View
    {
        return view('livewire.compras.compra-form-livewire', [
            'proveedores' => $this->proveedores,
            'bodegas' => $this->bodegas,
            'productos' => $this->productos,
            'pucs' => $this->pucs,
            'compraEncontrada' => $this->compra,
            'detalles_compra' => $this->detallesCompra,
            'esEdicion' => $this->esEdicion,
        ]);
    }
}
