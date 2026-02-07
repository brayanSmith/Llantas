<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StockActualizado implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $productos;
    public int $bodegaId;

    /**
     * @param array $productos IDs de productos afectados
     * @param int $bodegaId ID de la bodega afectada
     */
    public function __construct(array $productos, int $bodegaId)
    {
        $this->productos = $productos;
        $this->bodegaId = $bodegaId;
    }


    public function broadcastOn()
    {
        return new Channel('stock');
    }


    public function broadcastAs()
    {
        return 'StockActualizado';
    }
}
