<?php

namespace App\Livewire;

use App\Models\Cliente;
use App\Models\DetallePedido;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\StockBodega;
use DragonCode\Contracts\Http\Builder;
use Exception;
use Filament\Notifications\Notification;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;
use App\Services\StockCalculoService;
use App\Services\CompraCalculoService;
use App\Services\PedidoCalculoService;

class POS extends Component
{
    use WithPagination;

    // Tema de paginación (Tailwind por Filament)
    protected $paginationTheme = 'tailwind';
    // Modal de confirmación de venta
    public $showConfirmModal = false;
    public $confirmModalTitle = '';
    public $confirmModalBody = '';
    //Propiedades
    public $productos;
    public $clientes = [];
    public $search = '';
    public $cart = [];
    //propiedades para validar
    public $cliente_id = null;
    public $valor_decuento = 0; //
    public $flete = 0; // Valor del flete
    public $metodo_pago = "CREDITO";
    public $tipo_precio = "FERRETERO";
    public $tipo_venta = "REMISIONADA";
    public $estado_venta = "VENTA";
    public $iva = 0;
    public $valor_producto = 0;
    // Comentarios
    public $primer_comentario = '';
    public $segundo_comentario = '';
    public $ciudad = '';
    public $direccion = '';
    public $cantidad = 1;
    public $perPage = 10;
    public $contador_impresiones = 0;
    public $ciudades = [];
    public $bodega = null;
    //public $bodegas = [];
    public $user_id = null;

    public function mount()
    {
        // Cargar datos persistentes desde la sesión
        $this->loadFromSession();

        // Si no hay user_id en sesión, usar el usuario logueado
        if (!$this->user_id) {
            $this->user_id = auth()->id();
        }

        $this->clientes = $this->getClientesQuery()->orderBy('razon_social')->get();
        $this->ciudades = Cliente::select('ciudad')->distinct()->orderBy('ciudad')->pluck('ciudad')->toArray();
        //$this->bodegas = \App\Models\Bodega::all();
    }

    /**
     * Cargar datos del POS desde la sesión
     */
    private function loadFromSession()
    {
        $posData = session()->get('pos_data', []);
        
        if (!empty($posData)) {
            $this->cart = $posData['cart'] ?? [];
            $this->cliente_id = $posData['cliente_id'] ?? null;
            $this->metodo_pago = $posData['metodo_pago'] ?? 'CREDITO';
            $this->tipo_precio = $posData['tipo_precio'] ?? 'FERRETERO';
            $this->tipo_venta = $posData['tipo_venta'] ?? 'REMISIONADA';
            $this->estado_venta = $posData['estado_venta'] ?? 'VENTA';
            $this->primer_comentario = $posData['primer_comentario'] ?? '';
            $this->segundo_comentario = $posData['segundo_comentario'] ?? '';
            $this->flete = $posData['flete'] ?? 0;
            $this->ciudad = $posData['ciudad'] ?? '';
            $this->direccion = $posData['direccion'] ?? '';
            $this->user_id = $posData['user_id'] ?? null;
        }
    }

    /**
     * Guardar datos del POS en la sesión
     */
    private function saveToSession()
    {
        $posData = [
            'cart' => $this->cart,
            'cliente_id' => $this->cliente_id,
            'metodo_pago' => $this->metodo_pago,
            'tipo_precio' => $this->tipo_precio,
            'tipo_venta' => $this->tipo_venta,
            'estado_venta' => $this->estado_venta,
            'primer_comentario' => $this->primer_comentario,
            'segundo_comentario' => $this->segundo_comentario,
            'flete' => $this->flete,
            'ciudad' => $this->ciudad,
            'direccion' => $this->direccion,
            'user_id' => $this->user_id,
        ];
        
        session()->put('pos_data', $posData);
    }

    /**
     * Limpiar datos del POS de la sesión
     */
    public function clearSession()
    {
        session()->forget('pos_data');
        $this->cart = [];
        $this->cliente_id = null;
        $this->metodo_pago = 'CREDITO';
        $this->tipo_precio = 'FERRETERO';
        $this->tipo_venta = 'REMISIONADA';
        $this->estado_venta = 'VENTA';
        $this->primer_comentario = '';
        $this->segundo_comentario = '';
        $this->flete = 0;
        $this->ciudad = '';
        $this->direccion = '';
        $this->user_id = auth()->id(); // Mantener el usuario logueado
    }

    // Actualizar ciudad y dirección cuando se selecciona un cliente
    public function updatedClienteId($value): void
    {
        // Reset campos
        $this->ciudad = '';
        $this->direccion = '';

        // Normaliza ID (evita '0', '', 'abc', etc.)
        $id = filter_var($value, FILTER_VALIDATE_INT) ?: null;
        if (!$id) {
            $this->saveToSession();
            return;
        }

        // Buscar en la colección primero (evita consulta a BD)
        if (!empty($this->clientes)) {
            $cliente = collect($this->clientes)->firstWhere('id', $id);
            if ($cliente) {
                $this->ciudad = $cliente['ciudad'] ?? $cliente['municipio'] ?? '';
                $this->direccion = $cliente['direccion'] ?? '';
                $this->saveToSession();
                return;
            }
        }

        // Fallback: consultar BD si no está en la colección
        $cliente = $this->getClientesQuery()
            ->select(['id', 'ciudad', 'municipio', 'direccion'])
            ->find($id);

        if ($cliente) {
            $this->ciudad = $cliente->ciudad ?: $cliente->municipio ?: '';
            $this->direccion = $cliente->direccion ?? '';
        }
        
        $this->saveToSession();
    }    

    // Interceptor global para auto-guardado y control de paginación
    public function updated($property, $value)
    {
        // Propiedades que deben guardarse automáticamente en sesión
        $autoSaveProps = [
            'metodo_pago', 
            'tipo_precio', 
            'tipo_venta', 
            'estado_venta',
            'primer_comentario', 
            'segundo_comentario',
            'cart',
            'ciudad',
            'direccion'
        ];
        
        if (in_array($property, $autoSaveProps)) {
            $this->saveToSession();
        }
        
        // Resetear paginación cuando cambia búsqueda o tamaño de página
        if (in_array($property, ['search', 'perPage'])) {
            $this->resetPage();
        }
    }

    #[Computed]
    public function filteredProducts() 
    {
        $bodegaId = $this->bodega ?? 1; // Usa la bodega seleccionada o bodega 1 por defecto

        return Producto::query()
            ->where('activo', 1)
            ->where('categoria_producto', 'PRODUCTO_TERMINADO')
            ->whereHas('stockBodegas', function ($q) use ($bodegaId) {
                $q->where('bodega_id', $bodegaId)
                  ->where('stock', '>', 0);
            })
            ->when(
                $this->search,
                function($q) {
                    // Dividir la búsqueda en palabras clave
                    $keywords = array_filter(explode(' ', trim($this->search)));
                    
                    $q->where(function($qq) use ($keywords) {
                        foreach ($keywords as $keyword) {
                            $qq->where(function($qqq) use ($keyword) {
                                $qqq->where('nombre_producto', 'like', "%{$keyword}%")
                                    ->orWhere('codigo_producto', 'like', "%{$keyword}%");
                            });
                        }
                    });
                }
            )
            ->orderBy('nombre_producto')
            ->paginate($this->perPage);
    }

    #[Computed]
    public function subtotal() 
    {
        // Subtotal CON IVA para mostrar en pantalla
        $subtotalProductos = collect($this->cart)->sum(function ($producto) {
            $precioBase = $precioBase = PedidoCalculoService::obtenerValorUnitario($producto, $this->tipo_precio);
            return PedidoCalculoService::calcularDetalles([
                'producto_id' => $producto['id'],
                'cantidad' => $producto['cantidad'],
                'precio_unitario' => $precioBase,
                'aplicar_iva' => true,
                'iva' => $producto['iva_producto'] ?? 0,
            ]);
        });
        return $subtotalProductos;
    }   

    // Agregar producto al carrito (comportamiento original)
    public function addToCart($productoId, $cantidad = 2)
    {
        $producto = Producto::find($productoId);       
        
        // Obtener el stock actualizado
        $inventario = StockBodega::where('producto_id', $productoId)
            ->where('bodega_id', $this->bodega ?? 1)
            ->first();
        if (!$inventario || $inventario->stock <= 0) {
            Notification::make()
                ->title('Este Proucto esta fuera de Stock!')
                ->danger()
                ->send();
            return;
        }
        if (isset($this->cart[$productoId])) {
            $currentQuantity = $this->cart[$productoId]['cantidad'];
            $nuevaCantidad = $currentQuantity + $cantidad;
            if ($nuevaCantidad > $inventario->stock) {
                Notification::make()
                    ->title("No se pueden agregar más productos. Solo {$inventario->stock} en stock")
                    ->danger()
                    ->send();
                return;
            }
            $this->cart[$productoId]['cantidad'] = $nuevaCantidad;
        } else {
            if ($cantidad > $inventario->stock) {
                Notification::make()
                    ->title("No se pueden agregar más productos. Solo {$inventario->stock} en stock")
                    ->danger()
                    ->send();
                return;
            }
            // Agregar nuevo producto al carrito (precios originales sin IVA)
            $this->cart[$productoId]  = [
                'id' => $producto->id,
                //Se agrega el id del producto
                'nombre_producto' => $producto->nombre_producto,
                'codigo_producto' => $producto->codigo_producto,
                'ubicacion_producto' => $producto->ubicacion_producto,

                'valor_detal_producto' => $producto->valor_detal_producto,
                'valor_ferretero_producto' => $producto->valor_ferretero_producto,
                'valor_mayorista_producto' => $producto->valor_mayorista_producto,
                'iva_producto' => $producto->iva_producto,
                'imagen_producto' => $producto->imagen_producto,
                'cantidad' => $cantidad,
            ];
        }
        
        // Ordenar el carrito por código y ubicación
        $this->sortCart();
        
        // Guardar en sesión después de agregar al carrito
        $this->saveToSession();
    }
    
    /**
     * Ordenar el carrito por codigo_producto y ubicacion_producto
     */
    private function sortCart()
    {
        if (empty($this->cart)) {
            return;
        }

        // Convertir el carrito a un array ordenado
        $cartArray = collect($this->cart)->sortBy([
            ['ubicacion_producto', 'asc'],
            ['codigo_producto', 'asc'],            
        ])->toArray();

        // Reconstruir el carrito manteniendo las claves (IDs de productos)
        $this->cart = [];
        foreach ($cartArray as $item) {
            $this->cart[$item['id']] = $item;
        }
    }

    //remover productos del carro
    public function removeFromCart($productoId)
    {
        unset($this->cart[$productoId]);
        
        // Guardar en sesión después de remover del carrito
        $this->saveToSession();
    }
    
    //Verificar que el carro no este vacio
    public function checkout()
    {
        //checkear si el carro no esta vacio
        if (empty($this->cart)) {
            Notification::make()
                ->title('Venta Fallida')
                ->body('Tu carro esta vacio')
                ->danger()
                ->send();
            return;
        }
        DB::beginTransaction();

        //crear la venta... db
        try {
            //crear la venta
            $pedido = Pedido::create([

                'cliente_id' => $this->cliente_id,
                'user_id' => $this->user_id,
                'alistador_id' => $this->user_id,
                'estado' => 'PENDIENTE',
                'metodo_pago' => $this->metodo_pago,
                'tipo_precio' => $this->tipo_precio,
                'tipo_venta' => $this->tipo_venta,
                'estado_venta' => $this->estado_venta,
                'primer_comentario' => $this->primer_comentario,
                'segundo_comentario' => $this->segundo_comentario,
                'flete' => $this->flete,
                'subtotal' => $this->subtotal(), // Guardar subtotal SIN IVA
                'total_a_pagar' => $this->subtotal() + $this->flete, // Total CON IVA
                'saldo_pendiente' => $this->subtotal() + $this->flete, // Inicialmente igual al total a pagar
                'ciudad' => $this->ciudad,
                //vamos a hacer que la fecha de vencimiento sea 30 dias despues de la fecha actual
                'fecha' => now()->toDateString(),
                'fecha_vencimiento' => now()->addDays(30)->toDateString(),
                'bodega_id' => 1, //por defecto bodega 1

            ]);

            //Crear Productos Vendidos

            foreach ($this->cart as $producto) {
                $precio_unitario = PedidoCalculoService::obtenerValorUnitario($producto, $this->tipo_precio); // Sin IVA para guardar en BD
                $ivaProducto = $producto['iva_producto'] ?? 0;
                
                DetallePedido::create([
                    'pedido_id' => $pedido->id,
                    'producto_id' => $producto['id'],
                    'cantidad' => $producto['cantidad'],
                    'precio_unitario' => $precio_unitario,
                    'iva' => $ivaProducto, // Guardamos el porcentaje de IVA (ej: 19 para 19%)
                    // El subtotal se calculará automáticamente en el modelo como: cantidad * (precio_unitario * factor_iva)

                ]);

                //actualizar ek stock
                $inventario = Producto::find($producto['id']);
                if ($inventario) {
                    //$inventario->stock -= $producto['cantidad'];
                    $inventario->save();
                }
            }
            
            // Recalcular stock después de crear todos los detalles
            foreach ($this->cart as $producto) {
                app(\App\Services\StockCalculoService::class)->recalcularStockPorProductoYBodega(
                    $producto['id'],
                    $pedido->bodega_id
                );
            }
            
            DB::commit();

            //reset cart y otras propiedades
            $this->cart = [];
            $this->search = '';
            $this->cliente_id = null;
            $this->metodo_pago = "CREDITO";
            $this->tipo_precio = "FERRETERO";
            $this->tipo_venta = "REMISIONADA";
            $this->estado_venta = "VENTA";
            $this->primer_comentario = '';
            $this->segundo_comentario = '';
            $this->ciudad = '';
            $this->direccion = '';
            $this->flete = 0;
            $this->user_id = auth()->id(); // Mantener el usuario logueado
            //$this->bodegaSeleccionada = '';

            // Limpiar datos de la sesión después de completar la venta
            $this->clearSession();

            // Guardar la URL del PDF en la sesión para mostrar el botón en la modal
            session(['pedido_pdf_url' => route('pedidos.pdf.download', $pedido->id)]);
            $this->showConfirmModal = true;
            $this->confirmModalTitle = '¡Venta exitosa!';
            $this->confirmModalBody = 'El pedido fue ingresado exitosamente.';

            // 🚀 Cerrar la modal del carrito
            $this->dispatch('cerrar-modal-carrito');

            // Limpiar la URL de PDF de la sesión después de mostrar la modal
        } catch (Exception $th) {
            DB::rollBack();
            session()->forget('pedido_pdf_url');
            Notification::make()
                ->title('Error al registrar')
                ->body('Error al completar la venta, intentelo de nuevo.\n' . $th->getMessage())
                ->danger()
                ->send();
        }
    }    
    
    /**
     * Calcula el precio del producto con o sin IVA usando PedidoCalculoService
     * @deprecated Usar getPrecioBase() y PedidoCalculoService::calcularDetalles() directamente
     */
    public function getPrecioProducto($producto, $conIva = true)
    {        
        $precioBase = PedidoCalculoService::obtenerValorUnitario($producto, $this->tipo_precio);
        
        if ($conIva) {
            return PedidoCalculoService::calcularDetalles([
                'producto_id' => $producto['id'],
                'cantidad' => 1,
                'precio_unitario' => $precioBase,
                'aplicar_iva' => true,
                'iva' => $producto['iva_producto'] ?? 0,
            ]);
        }
        
        return $precioBase;
    }

    /**
     * Obtener query de clientes filtrada según el rol del usuario
     */
    private function getClientesQuery()
    {
        $query = Cliente::query();
        
        // Si el usuario no es super_admin ni financiero, mostrar solo sus clientes
        if (!auth()->user()->hasRole(['super_admin', 'financiero'])) {
            $query->where('comercial_id', auth()->id());
        }
        
        return $query;
    }

    /**
     * Calcular el stock disponible de un producto en la bodega
     * (stock total - cantidad ya agregada al carrito)
     */
    public function getAvailableStock($productoId)
    {
        $bodegaId = $this->bodega ?? 1;
        
        // Buscar el stock en la bodega específica
        $stockBodega = \App\Models\StockBodega::where('producto_id', $productoId)
            ->where('bodega_id', $bodegaId)
            ->first();
        
        if (!$stockBodega) {
            return 0;
        }

        $stockTotal = (float) $stockBodega->stock;

        // Sumar cantidad del producto si ya está en el carrito
        $cantidadEnCarrito = isset($this->cart[$productoId]) 
            ? (float) $this->cart[$productoId]['cantidad'] 
            : 0;

        // Stock disponible = stock total - cantidad en carrito
        $stockDisponible = $stockTotal - $cantidadEnCarrito;

        return max(0, $stockDisponible);
    }

    public function render()
    {
        return view('livewire.p-o-s');
    }
}
