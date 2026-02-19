<?php

namespace App\Livewire;

use App\Models\Abono;
use App\Models\Pedido;
use App\Models\Cliente;
use App\Models\Puc;
use App\Models\User;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use function Laravel\Prompts\select;

class AbonoPedidoFormLivewire extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public array $clientes = [];
    public array $pedidos = [];
    public array $formasPago = [];
    public array $users = [];
    public array $abonos = [];
    public $showConfirmModal = false;
    public $confirmModalTitle = '';
    public $confirmModalBody = '';


    public function mount(): void
    {
        $this->clientes = Cliente::select('id', 'numero_documento', 'razon_social')->get()->toArray();
        $this->pedidos = Pedido::select('id', 'codigo')
        ->where('estado_pago', 'EN_CARTERA')
        ->get()->toArray();
        $this->formasPago = Puc::select('id', 'nombre_puc', 'concatenar_subcuenta_concepto')->get()->toArray();
        $this->users = User::select('id', 'name')->get()->toArray();
        $this->abonos = Abono::select('id', 'monto')->get()->toArray();
    }

    public function createAbonos(array $payload): void
    {
        logger()->info('createAbonos payload', $payload);
        $pedidos = $payload['pedidos'] ?? [];
        $abono = $payload['abono'] ?? [];

        if ($pedidos === [] || $abono === []) {
            return;
        }

        DB::transaction(function () use ($pedidos, $abono) {
            $pedidoIds = array_column($pedidos, 'id');
            $pedidosDb = Pedido::whereIn('id', $pedidoIds)->get();

            foreach ($pedidosDb as $pedido) {
                $montoAbono = $pedido->saldo_pendiente ?? $pedido->total_a_pagar;
                Abono::create([
                    'pedido_id' => $pedido->id,
                    'fecha' => $abono['fecha'] ?? null,
                    'monto' => $montoAbono,
                    'forma_pago' => $abono['forma_pago'] ?? null,
                    'descripcion' => $abono['descripcion'] ?? null,
                    'imagen' => $abono['imagen'] ?? null,
                    'user_id' => $abono['user_id'] ?? null,
                ]);

                Pedido::where('id', $pedido->id)->update([
                    'saldo_pendiente' => 0,
                    'estado_pago' => 'SALDADO',
                    'abono' => (float) ($pedido->abono ?? 0) + (float) $montoAbono,
                ]);
            }
        });
        $this->showConfirmModal = true;
        $this->confirmModalTitle = '¡Abonos Registrados!';
        $this->confirmModalBody = 'Los abonos fueron ingresados exitosamente.';
    }

    public function buscarPedidosEnCartera($clienteId)
    {
        $this->pedidos = Pedido::where('estado_pago', 'EN_CARTERA')
            ->where('cliente_id', $clienteId)
            ->select('id', 'codigo', 'fecha', 'total_a_pagar', 'saldo_pendiente')
            ->get()
            ->toArray();

        return $this->pedidos;
    }

    public function render(): View
    {
        return view('livewire.abono-pedido-form-livewire', [
            'clientes' => $this->clientes,
            'formasPago' => $this->formasPago,
            'users' => $this->users,
            'abonos' => $this->abonos,
        ]);
    }
}
