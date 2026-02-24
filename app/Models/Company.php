<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'whatsapp_phone_number_id',
        'whatsapp_business_account_id',
        'whatsapp_access_token',
        'webhook_verify_token',
        'subscription_status',
        'subscription_end_date',
    ];

    protected function casts(): array
    {
        return [
            'subscription_end_date' => 'date',
        ];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'company_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Scope: add last_activity from latest conversation message.
     */
    public function scopeWithLastActivity(Builder $query): Builder
    {
        return $query->addSelect([
            'last_activity' => \App\Services\WhatsAppService\Models\Conversation::query()
                ->selectRaw('MAX(last_message_at)')
                ->whereColumn('conversations.company_id', 'companies.id'),
        ]);
    }
}
