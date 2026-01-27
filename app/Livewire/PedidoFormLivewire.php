<?php

namespace App\Livewire;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use App\Models\Pedido;
use App\Models\Cliente;
use App\Models\User;;
use App\Models\Bodega;
use App\Models\Producto;

class PedidoFormLivewire extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public ?array $data = [];
    public array $clientes = [];
    public array $users = [];
    public array $alistadores = [];
    public array $bodegas = [];
    public array $productos = [];

    public function mount(): void
    {
        $this->form->fill();
        $this->clientes = Cliente::select('id', 'razon_social', 'numero_documento', 'ciudad', 'retenedor_fuente')->get()->toArray();

        $this->users = User::select('id', 'name')->get()->toArray();

        $this->alistadores = User::select('id', 'name')->whereHas('roles', function ($query) {
            $query->where('name', 'Logistica');
        })->get()->toArray();

        $this->bodegas = Bodega::select('id', 'nombre_bodega')->get()->toArray();

        $this->productos = Producto::select(
            'id',
            'concatenar_codigo_nombre',
            'valor_detal_producto',
            'valor_mayorista_producto',
            'valor_ferretero_producto',
            'iva_producto'
            )->where('categoria_producto', '!=', 'MATERIA_PRIMA')
            ->get()
            ->toArray();
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
            'bodega_id' => $pedido['bodega_id'],
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
    }

    public function render(): View
    {
        return view('livewire.pedidos.pedido-form-livewire', [
            'clientes' => $this->clientes,
            'alistadores' => $this->alistadores,
            'bodegas' => $this->bodegas,
            'productos' => $this->productos,
            'users' => $this->users,
        ]);
    }
}
