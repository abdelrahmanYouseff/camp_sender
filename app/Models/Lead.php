<?php

namespace App\Models;

use App\Services\WhatsAppService\Models\Conversation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lead extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'company_id',
        'conversation_id',
        'customer_phone',
        'customer_name',
        'source',
        'first_message_at',
        'assigned_to',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'first_message_at' => 'datetime',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
