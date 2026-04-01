<?php

namespace App\Livewire;

use App\Models\User;

use App\Models\Bodega;
use App\Models\Pedido;
use App\Models\Cliente;
use App\Models\Empresa;
use Livewire\Component;
use App\Models\Producto;
use App\Models\Puc;
use App\Models\StockBodega;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Illuminate\Validation\Rules\In;

class POS extends Component implements HasActions, HasSchemas
// Escuchar el evento emitido desde JS/Echo

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
    public array $pucs = [];
    public array $stock = [];
    public ?string $tipoPrecio = null;
    public ?bool $esAdmin = false;

    public function mount(): void
    {
        // Si no hay user_id en sesión, usar el usuario logueado
        if (!$this->userId) {
            $this->userId = auth()->id();
        }
        $this->empresa = Empresa::first();
        $user = auth()->user();
        $this->tipoPrecio = $this->tipoDePrecioDeAcuerdoAlRol();
        $this->esAdmin = $user->hasRole('super_admin');
        \Log::info('[Livewire] POS mount', [
            'userId' => $this->userId,
            'tipoPrecio' => $this->tipoPrecio,
            'esAdmin' => $this->esAdmin,
        ]);
        $clientesQuery = Cliente::select(
            'id',
            'razon_social',
            'numero_documento',
            'ciudad',
            'saldo_total_pedidos_en_cartera',
            'saldo_total_pedidos_vencidos'
        )
            ->where('activo', 1);
        /*if (!$user->hasRole('super_admin')) {
            $clientesQuery->where('comercial_id', $this->userId);
        }*/
        $this->clientes = $clientesQuery->get()->toArray();
        $this->users = User::select('id', 'name')->with('roles')->get()->toArray();
        $this->bodegas = Bodega::select('id', 'nombre_bodega')->get()->toArray();
        $this->alistadores = User::select('id', 'name')->whereHas('roles', function ($query) {
            $query->where('name', 'Logistica');
        })->get()->toArray();
        //$this->productos = $this->getFilteredProductos();
        $this->productos = Producto::select(
            'id',
            'concatenar_codigo_nombre',
            'valor_detal',
            'valor_mayorista',
            'valor_sin_instalacion',
            'imagen_producto',
        )->get()->toArray();
        $this->bodegaSeleccionada = User::select('bodega_id')->where('id', $this->userId)->first()->bodega_id ?? null;
        $this->stock = $this->obtenerStockPorBodega($this->bodegaSeleccionada);
        $this->pucs = Puc::select('id', 'concatenar_subcuenta_concepto')->get()->toArray();
    }

    public function tipoDePrecioDeAcuerdoAlRol()
    {
        $role = User::find($this->userId)->roles()->pluck('name')->first();
        if (in_array($role, ['super_admin', 'comercial'])) {
            return 'DETAL';
        } elseif ($role === 'Comercial x Mayor') {
            return 'MAYORISTA';
        }
        return 'DETAL'; // Valor por defecto

    }

    public function obtenerStockPorBodega($bodegaId)
    {
        $this->bodegaSeleccionada = $bodegaId;
        $productos = Producto::with(['stockBodegas' => function ($query) use ($bodegaId) {
            $query->where('bodega_id', $bodegaId);
        }])->get();

        $stockPorProducto = [];
        foreach ($productos as $producto) {
            $stockPorProducto[$producto->id] = $producto->stockBodegas->first()->stock ?? 0;
        }

        \Log::info('[Livewire] obtenerStockPorBodega llamada', [
            'bodegaId' => $bodegaId,
            'stockPorProducto' => $stockPorProducto
        ]);

        return $stockPorProducto;
    }

    protected function getFilteredProductos()
    {
        $empresa = $this->empresa;
        $bodega = $this->bodegaSeleccionada ?? ($empresa->bodega_id ?? null);
        $productosQuery = Producto::select(
            'id',
            'concatenar_codigo_nombre',
            'valor_detal',
            'valor_mayorista',
            'valor_sin_instalacion',
            'imagen_producto',
        );
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
    protected $listeners = ['stockActualizado' => 'actualizarStock'];
    /**
     * Actualiza solo los productos afectados por el evento StockActualizado
     * @param array $data ['productos' => [], 'bodegaId' => int]
     */
    public function actualizarStock($data)
    {
        $bodega = $data['bodegaId'] ?? $this->bodegaSeleccionada;
        $idsAfectados = $data['productos'] ?? [];
        if (empty($idsAfectados)) return;

        // Obtener solo los productos afectados desde la base de datos
        $productosActualizados = $this->getFilteredProductos();
        $productosActualizados = collect($productosActualizados)
            ->whereIn('id', $idsAfectados)
            ->keyBy('id');

        // Actualizar solo los productos afectados en el array actual
        foreach ($this->productos as &$producto) {
            if (isset($productosActualizados[$producto['id']])) {
                $producto = $productosActualizados[$producto['id']];
            }
        }
        unset($producto);
    }

    //Metodo para generar Turno
    public function generarTurno(){
        // Buscar el último turno generado
        $ultimoPedido = Pedido::whereNotNull('turno')
            ->orderByDesc('id')
            ->first();
        $ultimoTurno = 0;
        if ($ultimoPedido && preg_match('/TURNO-(\d{4})/', $ultimoPedido->turno, $matches)) {
            $ultimoTurno = intval($matches[1]);
        }
        $nuevoTurno = $ultimoTurno + 1;
        $turno = 'TURNO-' . str_pad($nuevoTurno, 4, '0', STR_PAD_LEFT);
        return $turno;
    }

    // Método para guardar pedido y detalles desde Alpine.js
    public function guardarPedido($pedido)
    {
        $nuevoPedido = Pedido::create([
            //'codigo' => $pedido['codigo'],
            'cliente_id' => $pedido['cliente_id'],
            'fecha' => empty($pedido['fecha']) ? now()->toDateString() : $pedido['fecha'],
            'estado' => $pedido['estado'],
            'estado_pago' => $pedido['estado_pago'],
            //'tipo_pedido' => $pedido['tipo_pedido'],
            'tipo_pago' => $pedido['tipo_pago'],
            'tipo_precio' => $pedido['tipo_precio'],
            'id_puc' => $pedido['id_puc'] ?? null,
            'bodega_id' => $pedido['bodega_id'] ?? null,
            'observacion' => $pedido['observacion'] ?? '',
            'observacion_pago' => $pedido['observacion_pago'] ?? '',
            'subtotal' => $pedido['subtotal'],
            'descuento' => $pedido['descuento'],
            'flete' => $pedido['flete'],
            'total_a_pagar' => $pedido['total_a_pagar'],
            'abono' => $pedido['abono'],
            'saldo_pendiente' => $pedido['saldo_pendiente'],
            'user_id' => auth()->id(),
            'aplica_turno' => $pedido['aplica_turno'] ?? false,
            'turno' => $pedido['aplica_turno'] ? $this->generarTurno() : null,
        ]);
        $productosAfectados = [];
        foreach ($pedido['detalles'] as $detalle) {
            $nuevoPedido->detalles()->create([
                'producto_id' => $detalle['producto_id'],
                'cantidad' => $detalle['cantidad'],
                'precio_unitario' => $detalle['precio_unitario'] ?? 0,
                'subtotal' => $detalle['subtotal'] ?? 0,
            ]);
            $productosAfectados[] = $detalle['producto_id'];
        }
        // Ya no se emite el evento StockActualizado aquí, se maneja desde Alpine.js

        // Guardar la URL del PDF en la sesión para mostrar el botón en la modal
        session(['pedidoFacturado_pdf_url' => route('pedidosFacturados.pdf.stream', $nuevoPedido->id)]);
        $this->showConfirmModal = true;
        $this->confirmModalTitle = '¡Venta exitosa!';
        $this->confirmModalBody = 'El pedido fue ingresado exitosamente con el turno ' . ($nuevoPedido->turno ?? 'N/A') . '.';
    }
    public function render(): View
    {
        // Solo pasar productos, clientes y stockBodegas si no se han cargado (para evitar recarga masiva tras guardar)
        $productos = request()->has('productos_cargados') ? [] : $this->productos;
        $clientes = request()->has('clientes_cargados') ? [] : $this->clientes;
        $view = view('livewire.p-o-s', [
            'clientes' => $clientes,
            'alistadores' => $this->alistadores,
            'bodegas' => $this->bodegas,
            'productos' => $productos,
            'users' => $this->users,
            'empresa' => $this->empresa,
            'bodegaSeleccionada' => $this->bodegaSeleccionada,
            'userId' => $this->userId,
            'pucs' => $this->pucs,
            'tipoPrecio' => $this->tipoPrecio,
            'esAdmin' => $this->esAdmin,
        ]);
        return $view;
    }
}
