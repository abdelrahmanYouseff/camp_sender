<?php

namespace App\Services\WhatsAppService\Listeners;

use App\Models\Lead;
use App\Services\WhatsAppService\Events\WhatsAppMessageReceived;
use Illuminate\Support\Facades\Log;

class CreateLeadFromIncomingMessageListener
{
    /**
     * عند وصول رسالة جديدة من العميل، التأكد من وجود عميل محتمل (Lead) في لوحة المدير.
     */
    public function handle(WhatsAppMessageReceived $event): void
    {
        $message = $event->message;
        if ($message->sender_type !== 'customer') {
            return;
        }

        $conversation = $event->conversation;

        $existing = Lead::query()
            ->where('company_id', $conversation->company_id)
            ->where('conversation_id', $conversation->id)
            ->first();

        if ($existing) {
            if ($conversation->customer_name !== null && $conversation->customer_name !== $existing->customer_name) {
                $existing->update(['customer_name' => $conversation->customer_name]);
            }
            return;
        }

        $firstMessageAt = $conversation->messages()
            ->where('sender_type', 'customer')
            ->orderBy('created_at')
            ->value('created_at');

        Lead::create([
            'company_id' => $conversation->company_id,
            'conversation_id' => $conversation->id,
            'customer_phone' => $conversation->customer_phone,
            'customer_name' => $conversation->customer_name,
            'source' => 'whatsapp',
            'first_message_at' => $firstMessageAt ?? now(),
            'status' => 'new',
        ]);

        Log::channel('whatsapp')->info('Lead created from incoming message', [
            'conversation_id' => $conversation->id,
            'company_id' => $conversation->company_id,
        ]);
    }
}
