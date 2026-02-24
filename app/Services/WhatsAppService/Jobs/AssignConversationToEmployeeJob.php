<?php

namespace App\Services\WhatsAppService\Jobs;

use App\Models\User;
use App\Services\WhatsAppService\Models\Conversation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AssignConversationToEmployeeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Conversation $conversation
    ) {}

    /**
     * Assign conversation to an employee if still unassigned and no agent has replied.
     */
    public function handle(): void
    {
        $conversation = $this->conversation->fresh();
        if (!$conversation || $conversation->status === 'closed') {
            return;
        }
        if ($conversation->assigned_to !== null) {
            return; // Already assigned
        }

        // Check if any agent already replied
        $agentReplied = $conversation->messages()
            ->where('sender_type', 'agent')
            ->exists();
        if ($agentReplied) {
            return;
        }

        $employee = $this->findAvailableEmployee($conversation->company_id);
        if ($employee) {
            $conversation->update([
                'assigned_to' => $employee->id,
                'status' => 'assigned',
            ]);
            Log::channel('whatsapp')->info('Conversation auto-assigned', [
                'conversation_id' => $conversation->id,
                'user_id' => $employee->id,
            ]);
        }
    }

    protected function findAvailableEmployee(int $companyId): ?User
    {
        // TODO: Implement round-robin or least-loaded employee selection
        return User::query()
            ->where('company_id', $companyId)
            ->where('status', 'active')
            ->where('role', '!=', 'super_admin')
            ->inRandomOrder()
            ->first();
    }
}
