<?php

namespace App\Livewire;

use App\Models\User;;

use App\Models\Bodega;
use App\Models\Pedido;
use App\Models\Cliente;
use App\Models\Empresa;
use Livewire\Component;
use App\Models\Producto;
use App\Models\StockBodega;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;

class POS extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public ?array $data = [];
    public array $clientes = [];
    public array $users = [];
    public array $alistadores = [];
    public array $bodegas = [];
    public array $productos = [];
    public ?Empresa $empresa = null;
    public array $stockBodegas = [];
    public ?string $bodegaSeleccionada = null;
    public array $stockDisponible = [];
    public ?int $userId = null;
    public $showConfirmModal = false;
    public $confirmModalTitle = '';
    public $confirmModalBody = '';

    public function mount(): void
    {
        // Si no hay user_id en sesión, usar el usuario logueado
        if (!$this->userId) {
            $this->userId = auth()->id();
        }

        $this->form->fill();
        $this->empresa = Empresa::first();
        $this->stockBodegas = StockBodega::select('id', 'bodega_id', 'producto_id', 'stock')->get()->toArray();
        $this->clientes = Cliente::select('id', 'razon_social', 'numero_documento', 'ciudad', 'retenedor_fuente', 'saldo_total_pedidos_en_cartera', 'saldo_total_pedidos_vencidos')->get()->toArray();
        $this->users = User::select('id', 'name')->get()->toArray();
        $this->bodegas = Bodega::select('id', 'nombre_bodega')->get()->toArray();
        $this->alistadores = User::select('id', 'name')->whereHas('roles', function ($query) {
            $query->where('name', 'Logistica');
        })->get()->toArray();
        $this->productos = $this->getFilteredProductos();
        $this->bodegaSeleccionada = $this->empresa->bodega_id;
        $this->stockDisponible = $this->getAvailableStock();

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
        $productos = $productosQuery->get()->toArray();
        return $productos;
    }

    public function getAvailableStock()
    {
        $empresa = $this->empresa;
        $bodega = $this->bodegaSeleccionada ?? ($empresa->bodega_id ?? null);
        $productos = $this->productos;
        $stockBodegas = $this->stockBodegas;

        // Filtrar stock por bodega y producto
        $stockPorProducto = [];
        foreach ($productos as $producto) {
            $stock = 0;
            foreach ($stockBodegas as $sb) {
                if ($sb['bodega_id'] == $bodega && $sb['producto_id'] == $producto['id']) {
                    $stock = $sb['stock'];
                    break;
                }
            }
            $stockPorProducto[$producto['id']] = $stock;
        }
        return $stockPorProducto;
    }

    public function getStockDisponible($idProducto)
        {
            $empresa = $this->empresa;
            $bodega = $this->bodegaSeleccionada ?? ($empresa->bodega_id ?? null);
            $stockBodega = StockBodega::where('bodega_id', $bodega)
                ->where('producto_id', $idProducto)
                ->first();

            return $stockBodega ? $stockBodega->stock : 0;
        }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
            ])
            ->statePath('data')
            ->model(Pedido::class);
    }
    public function create(): void
    {
        $data = $this->form->getState();

        $record = Pedido::create($data);

        $this->form->model($record)->saveRelationships();
    }
    // Método para guardar pedido y detalles desde Alpine.js
    public function guardarPedido($pedido)
    {
        $nuevoPedido = Pedido::create([
            'codigo' => $pedido['codigo'],
            'fe' => $pedido['fe'],
            'cliente_id' => $pedido['cliente_id'],
            'fecha' => $pedido['fecha'],
            'dias_plazo_vencimiento' => $pedido['dias_plazo_vencimiento'],
            'fecha_vencimiento' => $pedido['fecha_vencimiento'],
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
            'user_id' => $pedido['user_id'],
            'alistador_id' => $pedido['alistador_id'],
            'bodega_id' => $pedido['bodega_id'] ?? null,
            'iva' => $pedido['iva'] ?? 0,
        ]);
        foreach ($pedido['detalles'] as $detalle) {
            $nuevoPedido->detalles()->create([
                'producto_id' => $detalle['producto_id'],
                'cantidad' => $detalle['cantidad'],
                'precio_unitario' => $detalle['precio_unitario'] ?? 0,
                'aplicar_iva' => $detalle['aplicar_iva'],
                'iva' => $detalle['iva'] ?? 0,
                'precio_con_iva' => $detalle['precio_con_iva'] ?? 0,
                'subtotal' => $detalle['subtotal'] ?? 0,
            ]);
        }

        // Guardar la URL del PDF en la sesión para mostrar el botón en la modal
        session(['pedido_pdf_url' => route('pedidos.pdf.download', $nuevoPedido->id)]);
        $this->showConfirmModal = true;
        $this->confirmModalTitle = '¡Venta exitosa!';
        $this->confirmModalBody = 'El pedido fue ingresado exitosamente.';
    }
    public function render(): View
    {
        return view('livewire.p-o-s', [
            'clientes' => $this->clientes,
            'alistadores' => $this->alistadores,
            'bodegas' => $this->bodegas,
            'productos' => $this->productos,
            'users' => $this->users,
            'empresa' => $this->empresa,
            'bodegaSeleccionada' => $this->bodegaSeleccionada,
            'stockBodegas' => $this->stockBodegas,
            'userId' => $this->userId,
            //'stockDisponible' => $this->getStockDisponible,

        ]);
    }
}
