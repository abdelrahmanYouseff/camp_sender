<?php

namespace App\Services\WhatsAppService\Models;

use App\Services\WhatsAppService\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AutomationSetting extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'welcome_message',
        'fallback_message',
        'auto_assign_after_minutes',
        'default_employee_id',
    ];

    protected function casts(): array
    {
        return [
            'auto_assign_after_minutes' => 'integer',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Company::class);
    }
}
