<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'product_price_id',
        'order_id',
        'title',
        'status',
        'gateway',
        'gateway_subscription_id',
        'gateway_status',
        'gateway_data',
        'trial_ends_at',
        'ends_at',
        'next_billing_date',
        'canceled_at',
        'meta',
    ];

    protected $casts = [
        'gateway_data' => 'array',
        'trial_ends_at' => 'datetime',
        'ends_at' => 'datetime',
        'next_billing_date' => 'datetime',
        'canceled_at' => 'datetime',
        'meta' => 'array',
    ];

    protected $appends = ['can_resume', 'current_period_end'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function productPrice(): BelongsTo
    {
        return $this->belongsTo(ProductPrice::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public static function getStatuses(): array
    {
        return ['active', 'trialing', 'past_due', 'canceled', 'incomplete', 'incomplete_expired'];
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isOnTrial(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    public function scopeCreatedAfter($query, $date)
    {
        return $query->where('created_at', '>=', $date);
    }

    public function scopeCreatedBefore($query, $date)
    {
        return $query->where('created_at', '<=', $date);
    }

    // Expose resume capability for frontend/API
    public function getCanResumeAttribute(): bool
    {
        // Only consider resumption for internally-canceled subscriptions
        if ($this->status !== 'canceled') {
            return false;
        }

        // General rule: canceled at period end → resumable until period end
        if ($this->ends_at && $this->ends_at->isFuture()) {
            return true;
        }

        // Gateway-specific overrides for resumability
        $gateway = strtolower((string) $this->gateway);
        $gatewayStatus = strtoupper((string) $this->gateway_status);

        switch ($gateway) {
            case 'paypal':
                // PayPal: SUSPENDED is resumable, CANCELLED is not
                return $gatewayStatus === 'SUSPENDED';

            case 'stripe':
                // Stripe: resumable only when canceled at period end (covered above)
                return false;

            default:
                // Unknown/other gateways: rely on general rule only
                return false;
        }
    }

    // Provide a unified period end field expected by some UIs
    public function getCurrentPeriodEndAttribute(): ?string
    {
        $date = $this->status === 'active' ? $this->next_billing_date : $this->ends_at;
        return $date ? $date->toIso8601String() : null;
    }
}
