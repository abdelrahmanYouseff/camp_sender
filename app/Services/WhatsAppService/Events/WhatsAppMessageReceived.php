<?php

namespace App\Services\WhatsAppService\Events;

use App\Services\WhatsAppService\Models\Conversation;
use App\Services\WhatsAppService\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WhatsAppMessageReceived
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Conversation $conversation,
        public Message $message
    ) {}
}
