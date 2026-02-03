<?php

namespace App\Livewire;


use App\Models\User;

use App\Models\Bodega;
use App\Models\Pedido;
use App\Models\Cliente;
use App\Models\Empresa;
use Livewire\Component;
use App\Models\Producto;
use App\Models\StockBodega;
use Filament\Schemas\Schema;
use App\Models\DetallePedido;
use Illuminate\Contracts\View\View;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;

class PedidoFormLivewire extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public array $clientes = [];
    public array $users = [];
    public array $alistadores = [];
    public array $bodegas = [];
    public array $productos = [];
    public ?Empresa $empresa = null;
    public ?string $bodegaSeleccionada = null;
    public ?int $userId = null;
    public $showConfirmModal = false;
    public $confirmModalTitle = '';
    public $confirmModalBody = '';
    public ?Pedido $pedido = null;
    public array $detalles = [];

    public function mount(): void
    {
        $pedidoId = request()->get('pedido_id');
        if ($pedidoId) {
            // Cargar la relación bodega junto con el pedido
            $this->pedido = Pedido::with('bodega', 'detalles', 'user', 'alistador', 'cliente')->find($pedidoId);
        }
        $this->detalles = DetallePedido::where('pedido_id', $pedidoId)->get()->toArray();
        // Log para saber que el formulario fue cargado
        // Si no hay user_id en sesión, usar el usuario logueado
        if (!$this->userId) { $this->userId = auth()->id();}
        $this->empresa = Empresa::first();
        $user = auth()->user();
        $clientesQuery = Cliente::select(
            'id',
            'razon_social',
            'numero_documento',
            'ciudad',
            'saldo_total_pedidos_en_cartera',
            'saldo_total_pedidos_vencidos')
            ->where('activo', 1);
        if (!$user->hasRole('super_admin')) {
            $clientesQuery->where('comercial_id', $this->userId);
        }
        $this->clientes = $clientesQuery->get()->toArray();
        $this->users = User::select('id', 'name')->get()->toArray();
        $this->bodegas = Bodega::select('id', 'nombre_bodega')->get()->toArray();
        $this->alistadores = User::role('Logistica')->select('id', 'name')->get()->toArray();
        //$this->alistadores = User::select('id', 'name')->get()->toArray();
        $this->productos = $this->getFilteredProductos();
        $this->bodegaSeleccionada = $this->empresa->bodega_id;

    }
    protected function getFilteredProductos()
    {
        $empresa = $this->empresa;
        $bodega = $this->bodegaSeleccionada ?? ($empresa->bodega_id ?? null);
        $productosQuery = Producto::select(
            'id',
            'concatenar_codigo_nombre',
            'valor_detal_producto',
            'valor_mayorista_producto',
            'valor_ferretero_producto',
            'iva_producto',
            'imagen_producto',
            'nombre_producto',
            'codigo_producto',
        )->where('categoria_producto', '!=', 'MATERIA_PRIMA')
            ->where('activo', 1);
        if ($empresa && !$empresa->mostrar_productos_sin_inventario) {
            $productos = $productosQuery->whereHas('stockBodegas', function ($q) use ($bodega) {
                $q->where('bodega_id', $bodega)
                    ->where('stock', '>', 0);
            });
        }
        // Eager loading solo los stocks de la bodega seleccionada
        $productos = $productosQuery->with(['stockBodegas' => function ($q) use ($bodega) {
            $q->where('bodega_id', $bodega);
        }])->get();
        $productosArray = [];
        foreach ($productos as $producto) {
            $stock = $producto->stockBodegas->first()?->stock ?? 0;
            $productoArr = $producto->toArray();
            unset($productoArr['stock_bodegas']); // Eliminar el array de stock_bodegas
            $productoArr['stock'] = $stock;
            $productosArray[] = $productoArr;
        }
        return $productosArray;
    }

    public function editarPedido($pedido)
    {
        //$start = microtime(true);

        // Buscar el pedido por el código
        $pedidoExistente = Pedido::where('codigo', $pedido['codigo'])->first();

        if (!$pedidoExistente) {
            $this->confirmModalTitle = 'Error';
            $this->confirmModalBody = 'No se encontró el pedido a editar.';
            $this->showConfirmModal = true;
            return;
        }

        // Actualizar los campos del pedido
        $pedidoExistente->update([
            'fe' => $pedido['fe'],
            'cliente_id' => $pedido['cliente_id'],
            'fecha' => empty($pedido['fecha']) ? now()->toDateString() : $pedido['fecha'],
            'dias_plazo_vencimiento' => $pedido['dias_plazo_vencimiento'],
            'fecha_vencimiento' => empty($pedido['fecha_vencimiento']) ? now()->addDays(30)->toDateString() : $pedido['fecha_vencimiento'],
            'ciudad' => $pedido['ciudad'],
            'estado' => $pedido['estado'],
            'stock_retirado' => $pedido['stock_retirado'],
            'en_cartera' => $pedido['en_cartera'],
            'metodo_pago' => $pedido['metodo_pago'],
            'tipo_precio' => $pedido['tipo_precio'],
            'tipo_venta' => $pedido['tipo_venta'],
            'estado_pago' => $pedido['estado_pago'],
            'estado_cartera' => $pedido['estado_cartera'],
            'estado_venta' => $pedido['estado_venta'],
            'estado_vencimiento' => $pedido['estado_vencimiento'],
            'primer_comentario' => $pedido['primer_comentario'],
            'subtotal' => $pedido['subtotal'],
            'abono' => $pedido['abono'],
            'descuento' => $pedido['descuento'],
            'flete' => $pedido['flete'],
            'total_a_pagar' => $pedido['total_a_pagar'],
            'saldo_pendiente' => $pedido['saldo_pendiente'],
            'user_id' => $pedido['user_id'] ?? auth()->id(),
            'alistador_id' => $pedido['alistador_id'] ?? auth()->id(),
            'bodega_id' => $pedido['bodega_id'] ?? null,
            'iva' => $pedido['iva'] ?? 0,
        ]);

        $detallesActuales = $pedidoExistente->detalles()->get()->keyBy('producto_id');
        $nuevosIds = collect($pedido['detalles'])->pluck('producto_id')->all();

        // Actualizar o crear
        foreach ($pedido['detalles'] as $detalle) {
            $detalleExistente = $detallesActuales->get($detalle['producto_id']);
            if ($detalleExistente) {
                $detalleExistente->update([
                    'cantidad' => $detalle['cantidad'],
                    'precio_unitario' => $detalle['precio_unitario'] ?? 0,
                    'aplicar_iva' => $detalle['aplicar_iva'],
                    'iva' => $detalle['iva'] ?? 0,
                    'precio_con_iva' => $detalle['precio_con_iva'] ?? 0,
                    'subtotal' => $detalle['subtotal'] ?? 0,
                ]);
            } else {
                $pedidoExistente->detalles()->create([
                    'producto_id' => $detalle['producto_id'],
                    'cantidad' => $detalle['cantidad'],
                    'precio_unitario' => $detalle['precio_unitario'] ?? 0,
                    'aplicar_iva' => $detalle['aplicar_iva'],
                    'iva' => $detalle['iva'] ?? 0,
                    'precio_con_iva' => $detalle['precio_con_iva'] ?? 0,
                    'subtotal' => $detalle['subtotal'] ?? 0,
                ]);
            }
        }

        // Eliminar los que ya no están
        $idsAEliminar = $detallesActuales->keys()->diff($nuevosIds);
        if ($idsAEliminar->isNotEmpty()) {
            $pedidoExistente->detalles()->whereIn('producto_id', $idsAEliminar)->delete();
        }


        // Guardar la URL del PDF en la sesión para mostrar el botón en la modal
        session(['pedido_pdf_url' => route('pedidos.pdf.stream', $pedidoExistente->id)]);
        session(['pedido_facturado_pdf_url' => route('pedidosFacturados.pdf.stream', $pedidoExistente->id)]);
        $this->showConfirmModal = true;
        $this->confirmModalTitle = '¡Venta exitosa!';
        $this->confirmModalBody = 'El pedido fue ingresado exitosamente.';
    }

    public function render(): View
    {
        return view('livewire.pedidos.pedido-form-livewire', [
            'clientes' => $this->clientes,
            'alistadores' => $this->alistadores,
            'bodegas' => $this->bodegas,
            'productos' => $this->productos,
            'users' => $this->users,
            'pedidoEncontrado' => $this->pedido,
            'detalles' => $this->detalles,
        ]);
    }
}
