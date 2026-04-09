<?php

namespace App\Livewire;


use App\Models\Abono;
use App\Models\Bodega;
use App\Models\Cliente;
use App\Models\DetallePedido;
use App\Models\Empresa;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\Puc;
use App\Models\StockBodega;
use App\Models\User;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class PedidoFormLivewire extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public array $clientes = [];
    public array $users = [];
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
    public array $abonos = [];
    public array $pucs = [];
    public bool $soloLectura = true;

    public function mount(): void

    {
        $pedidoId = request()->get('pedido_id');
        if ($pedidoId) {
            // Cargar la relación bodega junto con el pedido
            $this->pedido = Pedido::with('bodega', 'detalles', 'user', 'alistador', 'cliente', 'abonos.formaPago', 'abonos.user', 'puc')->find($pedidoId);
        }
        \Log::info('[Livewire] Pedido cargado en mount', ['pedido' => $this->pedido]);
        $this->detalles = DetallePedido::where('pedido_id', $pedidoId)->get()->toArray();
        $this->abonos = $this->pedido?->abonos->toArray() ?? [];
        // Log para saber que el formulario fue cargado
        // Si no hay user_id en sesión, usar el usuario logueado
        if (!$this->userId) {
            $this->userId = auth()->id();
        }


        $this->empresa = Empresa::first();
        $user = auth()->user();

        $this->soloLectura = true;
        if ($user->hasAnyRole(['super_admin', 'Financiero', 'Logistica']) && (!$this->pedido || $this->pedido->estado !== 'COMPLETADO')) {
            $this->soloLectura = false;
        }

        $clientesQuery = Cliente::select(
            'id',
            'razon_social',
            'numero_documento',
            'ciudad',
            'saldo_total_pedidos_en_cartera',
            'saldo_total_pedidos_vencidos'
        )
            ->where('activo', 1);
        if (!$user->hasRole(['super_admin', 'Financiero', 'Logistica'])) {
            $clientesQuery->where('comercial_id', $this->userId);
        }
        $this->clientes = $clientesQuery->get()->toArray();
        $this->users = User::select('id', 'name')->get()->toArray();
        $this->bodegas = Bodega::select('id', 'nombre_bodega')->get()->toArray();

        //$this->alistadores = User::select('id', 'name')->get()->toArray();
        $this->productos = Producto::select(
            'id',
            'concatenar_codigo_nombre',
            'costo_producto',
            'valor_detal',
            'valor_mayorista',
            'valor_sin_instalacion',
            'imagen_producto',
        )->get()->toArray();
        $this->bodegaSeleccionada = $this->empresa->bodega_id;
        $this->pucs = Puc::select('id', 'concatenar_subcuenta_concepto')->get()->toArray();
    }

    public function editarPedido($pedido)
    {
        //$start = microtime(true);

        // Buscar el pedido por el código
        $pedidoExistente = Pedido::find($pedido['id']);

        if (!$pedidoExistente) {
            $this->confirmModalTitle = 'Error';
            $this->confirmModalBody = 'No se encontró el pedido a editar.';
            $this->showConfirmModal = true;
            return;
        }

        // Actualizar los campos del pedido
        $pedidoExistente->update([

            'cliente_id' => $pedido['cliente_id'],
            'fecha' => empty($pedido['fecha']) ? now()->toDateString() : $pedido['fecha'],
            'estado' => $pedido['saldo_pendiente'] <= 0 ? 'COMPLETADO' : 'PENDIENTE',
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
                    'costo_unitario' => $detalle['costo_unitario'] ?? 0,
                    'costo_total' => $detalle['costo_total'] ?? 0,
                    'ganancia_total' => $detalle['ganancia_total'] ?? 0,
                    'subtotal' => $detalle['subtotal'] ?? 0,
                ]);
            } else {
                $pedidoExistente->detalles()->create([
                    'producto_id' => $detalle['producto_id'],
                    'cantidad' => $detalle['cantidad'],
                    'precio_unitario' => $detalle['precio_unitario'] ?? 0,
                    'costo_unitario' => $detalle['costo_unitario'] ?? 0,
                    'costo_total' => $detalle['costo_total'] ?? 0,
                    'ganancia_total' => $detalle['ganancia_total'] ?? 0,
                    'subtotal' => $detalle['subtotal'] ?? 0,
                ]);
            }
        }

        // Eliminar los que ya no están
        $idsAEliminar = $detallesActuales->keys()->diff($nuevosIds);
        if ($idsAEliminar->isNotEmpty()) {
            $pedidoExistente->detalles()->whereIn('producto_id', $idsAEliminar)->delete();
        }

        $abonosActuales = $pedidoExistente->abonos()->get()->keyBy('id');
        $nuevosIdsAbonos = collect($pedido['abonos'])->pluck('id')->all();
        // Actualizar o crear abonos
        foreach ($pedido['abonos'] as $abono) {
            $abonoExistente = $abonosActuales->get($abono['id']);
            if ($abonoExistente) {
                $abonoExistente->update([
                    'monto' => $abono['monto'],
                    'fecha' => $abono['fecha'],
                    'forma_pago_id' => $abono['forma_pago'],
                    'user_id' => $abono['user_id'] ?? auth()->id(),
                    'vendedor_id' => $abono['vendedor_id'] ?? null,
                ]);
            } else {
                $pedidoExistente->abonos()->create([
                    'monto' => $abono['monto'],
                    'fecha' => $abono['fecha'],
                    'forma_pago_id' => $abono['forma_pago'],
                    'user_id' => $abono['user_id'] ?? auth()->id(),
                    'vendedor_id' => $abono['vendedor_id'] ?? null,
                ]);
            }
        }
        //Eliminar los abonos que ya no están
        $idsAbonosAEliminar = $abonosActuales->keys()->diff($nuevosIdsAbonos);
        if ($idsAbonosAEliminar->isNotEmpty()) {
            $pedidoExistente->abonos()->whereIn('id', $idsAbonosAEliminar)->delete();
        }


        // Guardar la URL del PDF en la sesión para mostrar el botón en la modal
        session(['pedido_pdf_url' => route('pedidos.pdf.stream', $pedidoExistente->id)]);
        session(['pedido_facturado_pdf_url' => route('pedidosFacturados.pdf.stream', $pedidoExistente->id)]);
        $this->showConfirmModal = true;
        $this->confirmModalTitle = '¡Venta exitosa!';
        $this->confirmModalBody = 'El pedido fue ingresado exitosamente.';
    }

    #[On('actualizar-abono')]
    public function actualizarAbono(array $data): void
    {
        try {
            $abono = Abono::find($data['abono_id']);

            if (!$abono) {
                session()->flash('error', 'Abono no encontrado');
                return;
            }

            $abono->update([
                'monto' => $data['monto'],
                'fecha' => $data['fecha'],
                'descripcion' => $data['descripcion'] ?? null,
            ]);

            session()->flash('success', 'Abono actualizado exitosamente');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al actualizar el abono: ' . $e->getMessage());
        }
    }

    public function render(): View
    {
        return view('livewire.pedidos.pedido-form-livewire', [
            'clientes' => $this->clientes,
            'bodegas' => $this->bodegas,
            'productos' => $this->productos,
            'users' => $this->users,
            'pedidoEncontrado' => $this->pedido,
            'detalles' => $this->detalles,
            'pucs' => $this->pucs,
            'soloLectura' => $this->soloLectura,
        ]);
    }
}
