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

class LivewireEditPedidosForm extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public Pedido $record;

    public ?array $data = [];

    public array $productos = [];
    public array $clientes = [];

    public function mount(): void
    {
        $this->form->fill($this->record->attributesToArray());
        $this->productos = \App\Models\Producto::all()->toArray();
        $this->clientes = \App\Models\Cliente::all()->toArray();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
            ])
            ->statePath('data')
            ->model($this->record);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $this->record->update($data);
    }

    public function render(): View
    {
        return view('livewire.livewire-edit-pedidos-form');
    }
}
