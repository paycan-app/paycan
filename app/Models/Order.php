<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'product_id',
        'product_price_id',
        'order_number',
        'status',
        'total',
        'currency',
        'tax',
        'billing_email',
        'billing_name',
        'billing_address',
        'billing_city',
        'billing_state',
        'billing_zipcode',
        'billing_country',
        'gateway',
        'gateway_order_id',
        'gateway_data',
        'customer_note',
        'quantity',
        'meta',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'tax' => 'decimal:2',
        'quantity' => 'integer',
        'gateway_data' => 'array',
        'meta' => 'array',
    ];

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

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function fulfillments(): HasMany
    {
        return $this->hasMany(Fulfillment::class);
    }

    public function subscription(): HasOne
    {
        return $this->hasOne(Subscription::class);
    }

    public static function getStatuses(): array
    {
        return ['pending', 'processing', 'paid', 'completed', 'failed', 'cancelled', 'refunded'];
    }

    public static function getGateways(): array
    {
        return ['stripe', 'paypal'];
    }

    public function scopeCreatedAfter($query, $date)
    {
        return $query->where('created_at', '>=', $date);
    }

    public function scopeCreatedBefore($query, $date)
    {
        return $query->where('created_at', '<=', $date);
    }

    /**
     * Generate a secure cancellation token for this order
     */
    public function generateCancellationToken(): string
    {
        $token = bin2hex(random_bytes(32));

        $this->update([
            'meta' => array_merge($this->meta ?? [], [
                'cancellation_token' => $token,
                'cancellation_token_created_at' => now()->toIso8601String(),
            ]),
        ]);

        return $token;
    }

    /**
     * Validate a cancellation token
     */
    public function validateCancellationToken(string $token): bool
    {
        $storedToken = $this->meta['cancellation_token'] ?? null;
        $tokenCreatedAt = $this->meta['cancellation_token_created_at'] ?? null;

        if (! $storedToken || ! $tokenCreatedAt) {
            return false;
        }

        // Token expires after 3 hours
        $expiresAt = \Carbon\Carbon::parse($tokenCreatedAt)->addHours(3);
        if (now()->greaterThan($expiresAt)) {
            return false;
        }

        // Use hash_equals to prevent timing attacks
        return hash_equals($storedToken, $token);
    }

    /**
     * Invalidate the cancellation token (one-time use)
     */
    public function invalidateCancellationToken(): void
    {
        $meta = $this->meta ?? [];
        unset($meta['cancellation_token']);
        unset($meta['cancellation_token_created_at']);

        $this->update(['meta' => $meta]);
    }

    /**
     * Get the cancellation token (or generate if not exists)
     */
    public function getCancellationToken(): string
    {
        $token = $this->meta['cancellation_token'] ?? null;

        if (! $token) {
            return $this->generateCancellationToken();
        }

        return $token;
    }
}
