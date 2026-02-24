<?php

namespace App\Services\PaymentsService\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentsController extends Controller
{
    /**
     * Revenue Overview + Upcoming Payments (for Payments.vue).
     */
    public function index(Request $request): JsonResponse
    {
        $name = $request->query('name');
        $subscriptionStatus = $request->query('subscription_status');

        $totalRevenue = Payment::query()
            ->where('status', 'paid')
            ->sum('amount');

        $pendingRevenue = Payment::query()
            ->whereIn('status', ['pending', 'overdue'])
            ->sum('amount');

        $payingCompaniesCount = Payment::query()
            ->where('status', 'paid')
            ->distinct('company_id')
            ->count('company_id');

        $pendingCompaniesCount = Payment::query()
            ->whereIn('status', ['pending', 'overdue'])
            ->distinct('company_id')
            ->count('company_id');

        $paymentsQuery = Payment::query()
            ->with('company:id,name,subscription_status')
            ->orderByRaw("CASE WHEN status = 'overdue' THEN 0 WHEN status = 'pending' THEN 1 ELSE 2 END")
            ->orderBy('next_payment_date');

        if ($name) {
            $paymentsQuery->whereHas('company', fn ($q) => $q->where('name', 'like', '%' . $name . '%'));
        }

        if ($subscriptionStatus) {
            $paymentsQuery->whereHas('company', fn ($q) => $q->where('subscription_status', $subscriptionStatus));
        }

        $payments = $paymentsQuery->get()->map(fn (Payment $p) => [
            'id' => $p->id,
            'company_id' => $p->company_id,
            'company_name' => $p->company?->name,
            'subscription_status' => $p->company?->subscription_status,
            'amount' => (float) $p->amount,
            'status' => $p->status,
            'payment_date' => $p->payment_date?->format('Y-m-d'),
            'next_payment_date' => $p->next_payment_date?->format('Y-m-d'),
        ]);

        return response()->json([
            'revenue_summary' => [
                'total_revenue' => (float) $totalRevenue,
                'pending_revenue' => (float) $pendingRevenue,
                'paying_companies_count' => $payingCompaniesCount,
                'pending_companies_count' => $pendingCompaniesCount,
            ],
            'payments' => $payments,
        ]);
    }

    /**
     * Mark payment as paid.
     */
    public function markPaid(Payment $payment): JsonResponse
    {
        $payment->update([
            'status' => 'paid',
            'payment_date' => $payment->payment_date ?? now(),
        ]);

        return response()->json([
            'message' => 'Marked as paid',
            'data' => [
                'id' => $payment->id,
                'status' => $payment->status,
                'payment_date' => $payment->payment_date?->format('Y-m-d'),
            ],
        ]);
    }
}
