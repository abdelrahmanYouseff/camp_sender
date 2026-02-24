<?php

namespace App\Services\ClientDashboardService\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\User;
use App\Services\WhatsAppService\Models\Conversation;
use App\Services\WhatsAppService\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    /**
     * لوحة التحكم: إجماليات + الرسائل كل شهر + جدول التفاعلات (محادثات مع أعداد الرسائل والردود).
     */
    public function dashboard(Request $request): JsonResponse
    {
        $companyId = $request->user()->company_id;

        $totalMessages = Message::query()->where('company_id', $companyId)->count();
        $totalEmployees = User::query()->where('company_id', $companyId)->count();
        $totalLeads = Lead::query()->where('company_id', $companyId)->count();

        $totalCustomerMessages = Message::query()->where('company_id', $companyId)->where('sender_type', 'customer')->count();
        $totalAgentMessages = Message::query()->where('company_id', $companyId)->where('sender_type', 'agent')->count();

        $messagesPerMonth = Message::query()
            ->where('company_id', $companyId)
            ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as count')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->map(fn ($r) => [
                'year' => (int) $r->year,
                'month' => (int) $r->month,
                'count' => (int) $r->count,
                'label' => $this->monthLabel($r->year, $r->month),
            ])
            ->values()
            ->all();

        $interactions = Conversation::query()
            ->forCompany($companyId)
            ->withCount(['messages as customer_messages' => fn ($q) => $q->where('sender_type', 'customer')])
            ->withCount(['messages as agent_messages' => fn ($q) => $q->where('sender_type', 'agent')])
            ->orderByDesc('last_message_at')
            ->limit(50)
            ->get()
            ->map(fn ($c) => [
                'id' => $c->id,
                'customer_name' => $c->customer_name,
                'customer_phone' => $c->customer_phone,
                'customer_messages' => $c->customer_messages_count ?? $c->customer_messages ?? 0,
                'agent_messages' => $c->agent_messages_count ?? $c->agent_messages ?? 0,
                'last_message_at' => $c->last_message_at?->toIso8601String(),
            ])
            ->values()
            ->all();

        return response()->json([
            'total_messages' => $totalMessages,
            'total_employees' => $totalEmployees,
            'total_leads' => $totalLeads,
            'total_customer_messages' => $totalCustomerMessages,
            'total_agent_messages' => $totalAgentMessages,
            'messages_per_month' => $messagesPerMonth,
            'interactions' => $interactions,
        ]);
    }

    private function monthLabel(int $year, int $month): string
    {
        $months = ['', 'يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'];
        return ($months[$month] ?? $month) . ' ' . $year;
    }

    public function index(Request $request): JsonResponse
    {
        $companyId = $request->user()->company_id;

        $totalConversations = Conversation::query()->forCompany($companyId)->count();
        $pendingLeads = \App\Models\Lead::query()->where('company_id', $companyId)->whereNull('assigned_to')->count();
        $conversationsPerEmployee = Conversation::query()
            ->forCompany($companyId)
            ->whereNotNull('assigned_to')
            ->selectRaw('assigned_to as user_id, count(*) as count')
            ->groupBy('assigned_to')
            ->get();
        $userIds = $conversationsPerEmployee->pluck('user_id')->unique()->filter()->values();
        $users = $userIds->isNotEmpty() ? \App\Models\User::query()->whereIn('id', $userIds)->get()->keyBy('id') : collect();
        $conversationsPerEmployee = $conversationsPerEmployee->map(fn ($r) => [
            'employee_id' => $r->user_id,
            'employee_name' => $users->get($r->user_id)?->name ?? '—',
            'count' => $r->count,
        ])->values();

        $totalMessages = Message::query()->where('company_id', $companyId)->count();
        $readMessages = Message::query()->where('company_id', $companyId)->whereNotNull('read_at')->count();

        return response()->json([
            'total_conversations' => $totalConversations,
            'pending_leads' => $pendingLeads,
            'conversations_per_employee' => $conversationsPerEmployee,
            'total_messages' => $totalMessages,
            'read_messages' => $readMessages,
            'pending_messages' => $totalMessages - $readMessages,
        ]);
    }
}
