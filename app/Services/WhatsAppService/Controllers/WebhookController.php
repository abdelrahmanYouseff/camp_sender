<?php

namespace App\Services\WhatsAppService\Controllers;

use App\Http\Controllers\Controller;
use App\Services\WhatsAppService\Events\WhatsAppMessageReceived;
use App\Services\WhatsAppService\Helpers\WhatsAppHelper;
use App\Services\WhatsAppService\Repositories\ConversationRepository;
use App\Services\WhatsAppService\Repositories\MessageRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function __construct(
        protected ConversationRepository $conversationRepo,
        protected MessageRepository $messageRepo
    ) {}

    /**
     * GET: WhatsApp webhook verification (verify token).
     */
    public function verify(Request $request): Response
    {
        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        // Resolve company by verify_token and validate (or resolve per-company from DB)
        $verifyToken = config('services.whatsapp.webhook_verify_token');
        if ($mode === 'subscribe' && $token === $verifyToken) {
            return response($challenge, 200);
        }

        return response('Forbidden', 403);
    }

    /**
     * POST: Receive incoming WhatsApp messages.
     */
    public function handle(Request $request): Response
    {
        try {
            $payload = $request->all();

            Log::channel('whatsapp')->info('Webhook POST received', [
                'has_entry' => isset($payload['entry']),
                'entry_count' => isset($payload['entry']) ? count($payload['entry']) : 0,
            ]);

            // Validate webhook signature if configured
            // $this->validateSignature($request);

            if (isset($payload['entry'])) {
                foreach ($payload['entry'] as $entry) {
                    $this->processEntry($entry);
                }
            }

            return response('OK', 200);
        } catch (\Throwable $e) {
            Log::channel('whatsapp')->error('Webhook error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response('OK', 200); // Always 200 to prevent retries
        }
    }

    protected function processEntry(array $entry): void
    {
        $changes = $entry['changes'] ?? [];
        foreach ($changes as $change) {
            $value = $change['value'] ?? [];
            $messages = $value['messages'] ?? [];

            foreach ($messages as $message) {
                $this->processIncomingMessage($value, $message);
            }
        }
    }

    protected function processIncomingMessage(array $value, array $message): void
    {
        // Resolve company_id from value (e.g. phone_number_id / business account)
        $companyId = $this->resolveCompanyId($value);

        if (!$companyId) {
            $phoneNumberId = $value['metadata']['phone_number_id'] ?? null;
            Log::channel('whatsapp')->warning('No company found for webhook phone_number_id', [
                'phone_number_id' => $phoneNumberId,
                'hint' => 'تأكد أن رقم واتساب (Phone Number ID) في إعدادات الشركة يطابق القيمة التي ترسلها Meta بالضبط.',
            ]);
            return;
        }

        $conversation = $this->conversationRepo->findOrCreateByCustomerPhone(
            $companyId,
            $this->extractCustomerPhone($message)
        );

        $storedMessage = $this->messageRepo->createFromIncoming($companyId, $conversation->id, $message);

        $conversation->update(['last_message_at' => now()]);

        event(new WhatsAppMessageReceived($conversation, $storedMessage));
    }

    protected function resolveCompanyId(array $value): ?int
    {
        // TODO: Map phone_number_id or business account to company_id
        $phoneNumberId = $value['metadata']['phone_number_id'] ?? null;
        return WhatsAppHelper::getCompanyIdByPhoneNumberId($phoneNumberId);
    }

    protected function extractCustomerPhone(array $message): string
    {
        return $message['from'] ?? '';
    }
}
