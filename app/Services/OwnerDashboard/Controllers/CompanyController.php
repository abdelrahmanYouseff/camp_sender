<?php

namespace App\Services\OwnerDashboard\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    /**
     * List all companies (name, WhatsApp ID, subscription status, last activity).
     */
    public function index(): JsonResponse
    {
        $companies = Company::query()
            ->select([
                'id',
                'name',
                'email',
                'whatsapp_phone_number_id',
                'subscription_status',
                'subscription_end_date',
            ])
            ->withLastActivity()
            ->orderBy('name')
            ->get();

        return response()->json(['data' => $companies]);
    }

    /**
     * Show single company for editing.
     */
    public function show(Company $company): JsonResponse
    {
        return response()->json([
            'data' => $company->only([
                'id',
                'name',
                'email',
                'phone',
                'whatsapp_phone_number_id',
                'whatsapp_business_account_id',
                'whatsapp_access_token',
                'webhook_verify_token',
                'subscription_status',
                'subscription_end_date',
            ]),
        ]);
    }

    /**
     * Update company details.
     */
    public function update(Request $request, Company $company): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'string', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'whatsapp_phone_number_id' => ['nullable', 'string', 'max:255'],
            'whatsapp_business_account_id' => ['nullable', 'string', 'max:255'],
            'whatsapp_access_token' => ['nullable', 'string'],
            'webhook_verify_token' => ['nullable', 'string', 'max:255'],
            'subscription_status' => ['sometimes', 'string', 'in:active,suspended'],
            'subscription_end_date' => ['nullable', 'date'],
        ]);

        $company->update($validated);

        return response()->json([
            'message' => 'Updated',
            'data' => $company->fresh()->only([
                'id',
                'name',
                'email',
                'phone',
                'whatsapp_phone_number_id',
                'whatsapp_business_account_id',
                'whatsapp_access_token',
                'webhook_verify_token',
                'subscription_status',
                'subscription_end_date',
            ]),
        ]);
    }
}
