<?php

namespace App\Services\WhatsAppService\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait BelongsToCompany
{
    /**
     * Scope: filter by company for multi-tenant isolation.
     */
    public function scopeForCompany(Builder $query, ?int $companyId): Builder
    {
        if ($companyId === null) {
            return $query;
        }

        return $query->where('company_id', $companyId);
    }
}
