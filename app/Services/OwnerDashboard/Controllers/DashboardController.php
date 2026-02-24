<?php

namespace App\Services\OwnerDashboard\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    /**
     * Dashboard summary: total, active, suspended companies + last activity per company.
     */
    public function index(): JsonResponse
    {
        $total = Company::query()->count();
        $active = Company::query()->where('subscription_status', 'active')->count();
        $suspended = Company::query()->where('subscription_status', 'suspended')->count();

        $companies_activity = Company::query()
            ->select(['id', 'name', 'subscription_status'])
            ->withLastActivity()
            ->orderByDesc('last_activity')
            ->limit(10)
            ->get()
            ->map(fn ($c) => [
                'id' => $c->id,
                'name' => $c->name,
                'subscription_status' => $c->subscription_status,
                'last_activity' => $c->last_activity
                    ? \Carbon\Carbon::parse($c->last_activity)->format('Y-m-d H:i')
                    : null,
            ]);

        return response()->json([
            'total_companies' => $total,
            'active_companies' => $active,
            'suspended_companies' => $suspended,
            'companies_activity' => $companies_activity,
        ]);
    }
}
