<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;

class OrderRented implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function broadcastOn()
    {
        return new Channel('dashboard.orders');  // Public channel cho admin dashboard
    }

    public function broadcastWith()
    {
        $lat = $this->order->location_lat ?? 21.0285;  // Default Hà Nội
        $lng = $this->order->location_lng ?? 105.8542;

        return [
            'id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'status' => 'đang thuê',
            'lat' => $lat,
            'lng' => $lng,
            'merchant_name' => $this->order->merchant_name,
            'rental_shop' => $this->order->rental_shop,
        ];
    }
}
