<?php

namespace App\Services\WhatsAppService\Helpers;

use App\Models\Company;

class WhatsAppHelper
{
    /**
     * Resolve company_id by WhatsApp phone_number_id (multi-tenant).
     */
    public static function getCompanyIdByPhoneNumberId(?string $phoneNumberId): ?int
    {
        $normalized = $phoneNumberId !== null ? trim((string) $phoneNumberId) : '';
        if ($normalized === '') {
            return null;
        }

        $company = Company::query()
            ->whereNotNull('whatsapp_phone_number_id')
            ->whereRaw('TRIM(whatsapp_phone_number_id) = ?', [$normalized])
            ->first();

        return $company?->id;
    }

    /**
     * Get access token for a company (for API calls).
     */
    public static function getAccessToken(int $companyId): ?string
    {
        $company = Company::find($companyId);
        return $company?->whatsapp_access_token;
    }
}
