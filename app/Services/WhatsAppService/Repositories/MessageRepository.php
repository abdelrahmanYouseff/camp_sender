<?php

namespace App\Services\WhatsAppService\Repositories;

use App\Services\WhatsAppService\Models\Message;

class MessageRepository
{
    /**
     * Create a message from incoming webhook payload (customer).
     */
    public function createFromIncoming(int $companyId, int $conversationId, array $payload): Message
    {
        $text = $payload['text']['body'] ?? '';
        $type = isset($payload['image']) ? 'image' : (isset($payload['document']) ? 'document' : 'text');
        $body = $text ?: '[media]';

        return Message::create([
            'company_id' => $companyId,
            'conversation_id' => $conversationId,
            'sender_type' => 'customer',
            'sender_id' => null,
            'message_type' => $type,
            'message_body' => $body,
            'meta_message_id' => $payload['id'] ?? null,
            'status' => 'delivered',
        ]);
    }

    public function createAgentMessage(int $companyId, int $conversationId, int $userId, string $body, string $type = 'text'): Message
    {
        return Message::create([
            'company_id' => $companyId,
            'conversation_id' => $conversationId,
            'sender_type' => 'agent',
            'sender_id' => $userId,
            'message_type' => $type,
            'message_body' => $body,
            'meta_message_id' => null,
            'status' => 'sent',
        ]);
    }
}
