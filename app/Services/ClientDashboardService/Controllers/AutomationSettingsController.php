<?php

namespace App\Services\ClientDashboardService\Controllers;

use App\Http\Controllers\Controller;
use App\Services\WhatsAppService\Models\AutomationSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AutomationSettingsController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $companyId = $request->user()->company_id;
        $settings = AutomationSetting::query()->forCompany($companyId)->first();

        if (!$settings) {
            return response()->json(['data' => null]);
        }

        return response()->json([
            'data' => [
                'id' => $settings->id,
                'welcome_message' => $settings->welcome_message,
                'auto_assign_after_minutes' => $settings->auto_assign_after_minutes,
                'default_employee_id' => $settings->default_employee_id,
            ],
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $companyId = $request->user()->company_id;
        $validated = $request->validate([
            'welcome_message' => ['nullable', 'string'],
            'auto_assign_after_minutes' => ['sometimes', 'integer', 'min:0'],
            'default_employee_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $settings = AutomationSetting::query()->forCompany($companyId)->first();
        if (!$settings) {
            $settings = AutomationSetting::create(['company_id' => $companyId] + $validated);
        } else {
            $settings->update($validated);
        }

        return response()->json([
            'message' => 'Updated',
            'data' => $settings->fresh()->only(['id', 'welcome_message', 'auto_assign_after_minutes', 'default_employee_id']),
        ]);
    }
}
