<?php

namespace App\Services\ClientDashboardService\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();
        $company = Company::find($user->company_id);
        if (!$company) {
            return response()->json(['data' => null], 404);
        }

        $isAgent = $user->role === 'agent';

        $data = [
            'company_name' => $company->name,
            'user_email' => $user->email,
            'user_name' => $user->name,
            'role' => $user->role,
        ];

        if (!$isAgent) {
            $data['company'] = $company->only([
                'id', 'name', 'email', 'phone',
                'whatsapp_phone_number_id', 'whatsapp_business_account_id', 'whatsapp_access_token',
                'webhook_verify_token', 'subscription_status', 'subscription_end_date',
            ]);
        } else {
            $data['company'] = null;
        }

        return response()->json(['data' => $data]);
    }

    public function update(Request $request): JsonResponse
    {
        if ($request->user()->role === 'agent') {
            return response()->json(['message' => 'غير مسموح بتعديل بيانات الحساب.'], 403);
        }

        $company = Company::findOrFail($request->user()->company_id);

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'string', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'whatsapp_phone_number_id' => ['nullable', 'string', 'max:255'],
            'whatsapp_access_token' => ['nullable', 'string'],
        ]);

        $company->update($validated);

        $company->refresh();
        $data = [
            'company_name' => $company->name,
            'user_email' => $request->user()->email,
            'user_name' => $request->user()->name,
            'role' => $request->user()->role,
            'company' => $company->only(['id', 'name', 'email', 'phone', 'whatsapp_phone_number_id', 'whatsapp_access_token']),
        ];

        return response()->json([
            'message' => 'Updated',
            'data' => $data,
        ]);
    }
}
