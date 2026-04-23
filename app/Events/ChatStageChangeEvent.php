<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatStageChangeEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $uuid;

    public function __construct($message,$uuid)
    {
        $this->message = $message;
        $this->uuid = $uuid;
    }

    public function broadcastOn()
    {
        return ['chat.stage.change.' . $this->uuid];
    }

}
