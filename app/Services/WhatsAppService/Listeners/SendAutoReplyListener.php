<?php

namespace App\Services\WhatsAppService\Listeners;

use App\Services\WhatsAppService\Events\WhatsAppMessageReceived;
use App\Services\WhatsAppService\Jobs\AssignConversationToEmployeeJob;
use App\Services\WhatsAppService\Models\AutomationSetting;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class SendAutoReplyListener implements ShouldQueue
{
    /**
     * Handle: send welcome/fallback auto-reply and schedule assign-after-minutes job.
     */
    public function handle(WhatsAppMessageReceived $event): void
    {
        $conversation = $event->conversation;
        $companyId = $conversation->company_id;

        $settings = AutomationSetting::query()
            ->forCompany($companyId)
            ->first();

        if (!$settings) {
            return;
        }

        $messageBody = $this->pickMessage($event->message, $settings);
        if ($messageBody) {
            // TODO: Send message via WhatsApp API (use a dedicated action or job)
            Log::channel('whatsapp')->info('Auto-reply would be sent', [
                'conversation_id' => $conversation->id,
                'message' => $messageBody,
            ]);
        }

        $minutes = (int) $settings->auto_assign_after_minutes;
        if ($minutes > 0) {
            AssignConversationToEmployeeJob::dispatch($conversation)
                ->delay(now()->addMinutes($minutes));
        }
    }

    protected function pickMessage($message, AutomationSetting $settings): ?string
    {
        // First message in conversation → welcome_message; else fallback_message
        $isFirst = $conversation->messages()->count() <= 1;
        return $isFirst ? $settings->welcome_message : $settings->fallback_message;
    }
}
