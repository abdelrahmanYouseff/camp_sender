<?php

namespace App\Services\WhatsAppService\Repositories;

use App\Services\WhatsAppService\Models\Conversation;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ConversationRepository
{
    /**
     * Find or create conversation by company and customer phone (multi-tenant).
     */
    public function findOrCreateByCustomerPhone(int $companyId, string $customerPhone, ?string $customerName = null): Conversation
    {
        $conversation = Conversation::query()
            ->forCompany($companyId)
            ->where('customer_phone', $customerPhone)
            ->first();

        if ($conversation) {
            if ($customerName !== null) {
                $conversation->update(['customer_name' => $customerName]);
            }
            return $conversation;
        }

        return Conversation::create([
            'company_id' => $companyId,
            'customer_phone' => $customerPhone,
            'customer_name' => $customerName,
            'status' => 'new',
        ]);
    }

    public function getByCompany(int $companyId, int $perPage = 15): LengthAwarePaginator
    {
        return Conversation::query()
            ->forCompany($companyId)
            ->with(['assignedTo', 'messages' => fn ($q) => $q->latest()->limit(1)])
            ->latest('last_message_at')
            ->paginate($perPage);
    }
}
