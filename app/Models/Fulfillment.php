<?php

namespace App\Models;

use App\Traits\HasStringId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Fulfillment extends Model
{
    use HasFactory, HasStringId;

    protected $fillable = [
        'id',
        'order_id',
        'status',
        'type',
        'tracking_id',
        'provider',
        'meta',
        'fulfilled_at',
    ];

    protected $casts = [
        'meta' => 'array',
        'fulfilled_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public static function getStatuses(): array
    {
        return ['pending', 'processing', 'completed', 'failed'];
    }

    public static function getTypes(): array
    {
        return ['digital', 'physical', 'service', 'subscription_access'];
    }

    public static function getProviders(): array
    {
        return ['UPS', 'FedEx', 'USPS', 'DHL', 'Other'];
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}
