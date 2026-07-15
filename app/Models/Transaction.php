<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_id',
        'subscription_id',
        'type',
        'status',
        'gateway',
        'amount',
        'currency',
        'gateway_transaction_id',
        'gateway_data',
        'meta',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'gateway_data' => 'array',
        'meta' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public static function getTypes(): array
    {
        return ['charge', 'refund', 'subscription_create', 'subscription_renew', 'subscription_update', 'subscription_cancel'];
    }

    public static function getStatuses(): array
    {
        return ['pending', 'completed', 'failed', 'refunded'];
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}
