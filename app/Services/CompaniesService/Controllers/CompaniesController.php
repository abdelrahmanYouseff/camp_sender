<?php

namespace App\Services\CompaniesService\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CompaniesController extends Controller
{
    /**
     * List all companies with optional search by name and filter by subscription_status.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Company::query()
            ->select([
                'id',
                'name',
                'email',
                'whatsapp_phone_number_id',
                'subscription_status',
                'subscription_end_date',
            ])
            ->withLastActivity()
            ->orderBy('name');

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->input('name') . '%');
        }

        if ($request->filled('subscription_status')) {
            $query->where('subscription_status', $request->input('subscription_status'));
        }

        $companies = $query->get();

        return response()->json(['data' => $companies]);
    }

    /**
     * Show create form (SPA handles UI; this endpoint not required for form data).
     */
    public function create(): JsonResponse
    {
        return response()->json(['message' => 'Use POST /admin/api/companies to create']);
    }

    /**
     * Store a new company.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'whatsapp_phone_number_id' => ['nullable', 'string', 'max:255'],
            'whatsapp_access_token' => ['nullable', 'string'],
            'subscription_status' => ['required', 'string', 'in:active,suspended'],
            'subscription_end_date' => ['nullable', 'date'],
        ]);

        $company = Company::create($validated);

        return response()->json([
            'message' => 'Created',
            'data' => $company->only([
                'id',
                'name',
                'email',
                'phone',
                'whatsapp_phone_number_id',
                'whatsapp_access_token',
                'subscription_status',
                'subscription_end_date',
            ]),
        ], 201);
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
            'email' => ['sometimes', 'nullable', 'string', 'email', 'max:255'],
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
