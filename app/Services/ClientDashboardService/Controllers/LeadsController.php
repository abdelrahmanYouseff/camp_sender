<?php

namespace App\Services\ClientDashboardService\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\User;
use App\Services\WhatsAppService\Models\Conversation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LeadsController extends Controller
{
    /**
     * إنشاء عملاء محتملين من المحادثات الحالية التي لا تملك Lead (لمدير الشركة).
     */
    public function syncFromConversations(Request $request): JsonResponse
    {
        if ($request->user()->role === 'agent') {
            return response()->json(['message' => 'غير مسموح.'], 403);
        }

        $companyId = $request->user()->company_id;
        $conversationIdsWithLead = Lead::query()
            ->where('company_id', $companyId)
            ->whereNotNull('conversation_id')
            ->pluck('conversation_id');

        $existingPhones = Lead::query()
            ->where('company_id', $companyId)
            ->pluck('customer_phone')
            ->map(fn ($p) => (string) $p)
            ->unique()
            ->values()
            ->all();

        $conversations = Conversation::query()
            ->forCompany($companyId)
            ->whereNotIn('id', $conversationIdsWithLead)
            ->get();

        $created = 0;
        foreach ($conversations as $conversation) {
            $phone = (string) $conversation->customer_phone;
            if ($phone !== '' && in_array($phone, $existingPhones, true)) {
                continue;
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
            $created++;
            $existingPhones[] = $phone;
        }

        return response()->json([
            'message' => $created > 0 ? "تم تسجيل {$created} عميل محتمل من المحادثات." : 'لا توجد محادثات جديدة لتسجيلها.',
            'created' => $created,
        ]);
    }

    public function index(Request $request): JsonResponse
    {
        $companyId = $request->user()->company_id;
        $interestFilter = $request->input('interest'); // interested | not_interested | null (الكل)

        $query = Lead::query()
            ->where('company_id', $companyId)
            ->with(['assignedTo:id,name', 'conversation:id,closure_interest']);

        if ($interestFilter === 'interested' || $interestFilter === 'not_interested') {
            $query->whereHas('conversation', fn ($q) => $q->where('closure_interest', $interestFilter));
        }

        $leads = $query->orderBy('created_at', 'desc')
            ->get()
            ->map(fn ($l) => [
                'id' => $l->id,
                'customer_phone' => $l->customer_phone,
                'customer_name' => $l->customer_name,
                'status' => $l->assigned_to ? 'assigned' : 'unassigned',
                'assigned_employee' => $l->assignedTo ? ['id' => $l->assignedTo->id, 'name' => $l->assignedTo->name] : null,
                'conversation_id' => $l->conversation_id ?? null,
                'interest' => $l->conversation?->closure_interest ?? null,
                'created_at' => $l->created_at?->toIso8601String(),
            ]);

        return response()->json(['data' => $leads]);
    }

    public function assign(Request $request, int $id): JsonResponse
    {
        $companyId = $request->user()->company_id;
        $request->validate(['employee_id' => ['required', 'integer', 'exists:users,id']]);

        $lead = Lead::query()->where('company_id', $companyId)->findOrFail($id);
        $employee = User::query()->where('company_id', $companyId)->findOrFail($request->input('employee_id'));

        $lead->update(['assigned_to' => $employee->id, 'status' => 'contacted']);

        return response()->json(['message' => 'Assigned', 'data' => ['id' => $lead->id, 'assigned_to' => $employee->id]]);
    }

    /**
     * تعديل التقييم (مهتم / غير مهتم) للعميل المحتمل عبر المحادثة المرتبطة.
     */
    public function updateInterest(Request $request, int $id): JsonResponse
    {
        $companyId = $request->user()->company_id;
        $request->validate(['interest' => ['required', 'string', 'in:interested,not_interested']]);

        $lead = Lead::query()
            ->where('company_id', $companyId)
            ->with('conversation')
            ->findOrFail($id);

        if (! $lead->conversation) {
            return response()->json(['message' => 'لا توجد محادثة مرتبطة بهذا العميل المحتمل.'], 422);
        }

        $lead->conversation->update(['closure_interest' => $request->input('interest')]);

        return response()->json([
            'message' => 'تم تحديث التقييم.',
            'data' => ['id' => $lead->id, 'interest' => $request->input('interest')],
        ]);
    }
}
