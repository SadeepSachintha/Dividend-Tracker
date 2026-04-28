<?php

namespace App\Events;

use App\Models\Dividend;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DividendReceived implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $dividend;
    public $message;

    public function __construct(Dividend $dividend, string $message)
    {
        $this->dividend = $dividend;
        $this->message = $message;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('dividend-alerts'),
        ];
    }
    
    public function broadcastAs(): string
    {
        return 'dividend.received';
    }
}
