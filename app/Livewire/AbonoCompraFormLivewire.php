<?php

namespace App\Livewire;

use App\Models\AbonoCompra;
use App\Models\Compra;
use App\Models\Proveedor;
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
use Livewire\WithFileUploads;

class AbonoCompraFormLivewire extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use WithFileUploads;

    public array $proveedores = [];
    public array $compras = [];
    public array $formasPago = [];
    public array $users = [];
    public array $abonos = [];
    public $showConfirmModal = false;
    public $confirmModalTitle = '';
    public $confirmModalBody = '';
    public $imagenAbono = null;
    public $imagenAbonoPath = null;

    public function mount(): void
    {
        $this->proveedores = Proveedor::select('id', 'nombre_proveedor')->get()->toArray();
        $this->compras = Compra::select('id', 'numero_compra')
        ->where('estado_pago', 'EN_CARTERA')
        ->get()->toArray();
        $this->formasPago = Puc::select('id', 'concatenar_subcuenta_concepto')->get()->toArray();
        $this->users = User::select('id', 'name')->get()->toArray();
        $this->abonos = AbonoCompra::select('id', 'monto_abono_compra')->get()->toArray();
    }

    public function createAbonos(array $payload): void
    {
        logger()->info('createAbonos payload', $payload);
        $compras = $payload['compras'] ?? [];
        $abono = $payload['abono'] ?? [];

        if ($compras === [] || $abono === []) {
            return;
        }
        // Guardar la imagen si existe
        $rutaImagen = null;
        if ($this->imagenAbono) {
            $rutaImagen = $this->imagenAbono->store('abonos', 'public');
        }

        DB::transaction(function () use ($compras, $abono, $rutaImagen) {
            $compraIds = array_column($compras, 'id');
            $comprasDb = Compra::whereIn('id', $compraIds)->get();

            foreach ($comprasDb as $compra) {
                $montoAbono = $compra->saldo_pendiente ?? $compra->total_a_pagar;
                AbonoCompra::create([
                    'compra_id' => $compra->id,
                    'fecha_abono_compra' => $abono['fecha_abono_compra'] ?? null,
                    'monto_abono_compra' => $montoAbono,
                    'forma_pago_abono_compra' => $abono['forma_pago_abono_compra'] ?? null,
                    'descripcion_abono_compra' => $abono['descripcion_abono_compra'] ?? null,
                    'imagen_abono_compra' => $rutaImagen,
                    'user_id' => $abono['user_id'] ?? null,
                ]);

                Compra::where('id', $compra->id)->update([
                    'saldo_pendiente' => 0,
                    'estado_pago' => 'SALDADO',
                    'abono' => (float) ($compra->abono ?? 0) + (float) $montoAbono,
                ]);
            }
        });
        $this->imagenAbono = null;
        $this->imagenAbonoPath = null;

        $this->showConfirmModal = true;
        $this->confirmModalTitle = '¡Abonos Registrados!';
        $this->confirmModalBody = 'Los abonos fueron ingresados exitosamente.';
    }

    public function actualizarImagenPrevia(): void
    {
        if ($this->imagenAbono) {
            $this->imagenAbonoPath = $this->imagenAbono->temporaryUrl();
        }
    }

    public function eliminarImagen(): void
    {
        $this->imagenAbono = null;
        $this->imagenAbonoPath = null;
    }

    public function buscarComprasEnCartera($proveedorId)
    {
        $this->compras = Compra::where('estado_pago', 'EN_CARTERA')
            ->where('proveedor_id', $proveedorId)
            ->select('id', 'item_compra', 'factura', 'categoria_compra', 'total_a_pagar', 'saldo_pendiente')
            ->get()
            ->toArray();

        return $this->compras;
    }

    public function render(): View
    {
        return view('livewire.abono-compra-form-livewire', [
            'proveedores' => $this->proveedores,
            //'compras' => $this->compras,
            'formasPago' => $this->formasPago,
            'users' => $this->users,
            'abonos' => $this->abonos,
        ]);
    }
}
