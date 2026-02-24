<?php

namespace App\Services\ClientDashboardService\Controllers;

use App\Http\Controllers\Controller;
use App\Services\WhatsAppService\Events\WhatsAppMessageReceived;
use App\Services\WhatsAppService\Models\Conversation;
use App\Services\WhatsAppService\Models\Message;
use App\Services\WhatsAppService\Repositories\ConversationRepository;
use App\Services\WhatsAppService\Repositories\MessageRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class InboxController extends Controller
{
    public function __construct(
        protected ConversationRepository $conversationRepo,
        protected MessageRepository $messageRepo
    ) {}

    public function index(Request $request): JsonResponse
    {
        $companyId = $request->user()->company_id;
        $query = Conversation::query()
            ->forCompany($companyId)
            ->with(['assignedTo:id,name', 'messages' => fn ($q) => $q->latest()->limit(1)]);

        $hasReadAtColumn = Schema::hasColumn('messages', 'read_at');
        if ($hasReadAtColumn) {
            $query->withCount(['messages as unread_count' => function ($q) {
                $q->where('sender_type', 'customer')->whereNull('read_at');
            }]);
        }

        if ($request->filled('name')) {
            $query->where('customer_name', 'like', '%' . $request->input('name') . '%');
        }
        if ($request->filled('phone')) {
            $query->where('customer_phone', 'like', '%' . $request->input('phone') . '%');
        }

        $conversations = $query->latest('last_message_at')->get()->map(function ($c) use ($hasReadAtColumn) {
            $lastMsg = $c->messages->first();
            $lastMessageSender = $lastMsg ? $lastMsg->sender_type : null;
            return [
                'id' => $c->id,
                'customer_name' => $c->customer_name,
                'customer_phone' => $c->customer_phone,
                'status' => $c->status,
                'assigned_employee' => $c->assignedTo ? ['id' => $c->assignedTo->id, 'name' => $c->assignedTo->name] : null,
                'last_message_at' => $c->last_message_at?->toIso8601String(),
                'last_message' => $lastMsg?->message_body ?? null,
                'last_message_sender' => $lastMessageSender,
                'conversation_created_at' => $c->created_at?->toIso8601String(),
                'has_unread' => $hasReadAtColumn ? (int) $c->unread_count > 0 : false,
            ];
        });

        return response()->json(['data' => $conversations]);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $companyId = $request->user()->company_id;
        $conversation = Conversation::query()
            ->forCompany($companyId)
            ->with('assignedTo:id,name')
            ->findOrFail($id);

        // ربط المحادثة بالموظف (agent) فقط عند فتحها — المدير يعرض فقط ولا يُعيَّن له
        if ($request->user()->role === 'agent') {
            $conversation->update(['assigned_to' => $request->user()->id]);
        }

        // تعليم رسائل العميل كمقروءة (إن وُجد عمود read_at)
        if (Schema::hasColumn('messages', 'read_at')) {
            Message::query()
                ->where('conversation_id', $conversation->id)
                ->where('sender_type', 'customer')
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        }

        $conversation->load('assignedTo:id,name');

        $messages = Message::query()
            ->where('conversation_id', $conversation->id)
            ->with('sender:id,name')
            ->orderBy('created_at')
            ->get()
            ->map(fn ($m) => [
                'id' => $m->id,
                'sender' => $m->sender_type === 'customer' ? 'customer' : 'employee',
                'message' => $m->message_body,
                'employee_name' => $m->sender_type !== 'customer' && $m->sender ? $m->sender->name : null,
                'read_at' => $m->read_at?->toIso8601String(),
                'created_at' => $m->created_at?->toIso8601String(),
            ]);

        return response()->json([
            'conversation' => [
                'id' => $conversation->id,
                'customer_name' => $conversation->customer_name,
                'customer_phone' => $conversation->customer_phone,
                'status' => $conversation->status,
                'closure_interest' => $conversation->closure_interest,
                'assigned_employee' => $conversation->assignedTo ? ['id' => $conversation->assignedTo->id, 'name' => $conversation->assignedTo->name] : null,
            ],
            'messages' => $messages,
        ]);
    }

    /**
     * إنهاء المحادثة (تغيير الحالة إلى closed + تسجيل مهتم / غير مهتم).
     */
    public function close(Request $request, int $id): JsonResponse
    {
        $companyId = $request->user()->company_id;
        $request->validate([
            'interest' => ['nullable', 'string', 'in:interested,not_interested'],
        ]);

        $conversation = Conversation::query()
            ->forCompany($companyId)
            ->findOrFail($id);

        $interest = $request->input('interest');
        $conversation->update([
            'status' => 'closed',
            'closure_interest' => $interest === 'interested' || $interest === 'not_interested' ? $interest : null,
        ]);

        return response()->json([
            'message' => 'تم إغلاق المحادثة',
            'conversation' => [
                'id' => $conversation->id,
                'status' => $conversation->status,
                'closure_interest' => $conversation->closure_interest,
            ],
        ]);
    }

    /**
     * Send a reply to the conversation (records employee_id automatically).
     */
    public function reply(Request $request, int $id): JsonResponse
    {
        $companyId = $request->user()->company_id;
        $request->validate(['message' => ['required', 'string', 'max:4096']]);

        $conversation = Conversation::query()
            ->forCompany($companyId)
            ->findOrFail($id);

        $message = Message::create([
            'company_id' => $companyId,
            'conversation_id' => $conversation->id,
            'sender_type' => 'agent',
            'sender_id' => $request->user()->id,
            'message_type' => 'text',
            'message_body' => $request->input('message'),
            'status' => 'sent',
        ]);

        $conversation->update(['last_message_at' => now()]);

        return response()->json([
            'message' => 'Sent',
            'data' => [
                'id' => $message->id,
                'sender' => 'employee',
                'message' => $message->message_body,
                'employee_name' => $request->user()->name,
                'created_at' => $message->created_at?->toIso8601String(),
            ],
        ], 201);
    }

    /**
     * Simulate an incoming customer message (for testing — appears in inbox).
     */
    public function simulateIncoming(Request $request): JsonResponse
    {
        $request->validate([
            'customer_phone' => ['required', 'string', 'max:32'],
            'customer_name' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:4096'],
        ]);

        $companyId = $request->user()->company_id;
        if ($companyId === null || $companyId === '') {
            return response()->json([
                'message' => 'الحساب غير مرتبط بشركة. لا يمكن إضافة رسالة تجريبية.',
            ], 422);
        }

        $conversation = $this->conversationRepo->findOrCreateByCustomerPhone(
            $companyId,
            $request->input('customer_phone'),
            $request->input('customer_name')
        );

        $payload = [
            'from' => $request->input('customer_phone'),
            'id' => 'sim.' . uniqid(),
            'type' => 'text',
            'text' => ['body' => $request->input('message')],
        ];

        $storedMessage = $this->messageRepo->createFromIncoming(
            $companyId,
            $conversation->id,
            $payload
        );

        $conversation->update(['last_message_at' => now()]);

        event(new WhatsAppMessageReceived($conversation, $storedMessage));

        return response()->json([
            'message' => 'تم إضافة الرسالة التجريبية',
            'conversation_id' => $conversation->id,
            'data' => [
                'id' => $storedMessage->id,
                'sender' => 'customer',
                'message' => $storedMessage->message_body,
                'created_at' => $storedMessage->created_at?->toIso8601String(),
            ],
        ], 201);
    }
}
