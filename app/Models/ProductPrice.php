<?php

namespace App\Models;

use App\Traits\HasStringId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ProductPrice extends Model
{
    use HasFactory, HasStringId, SoftDeletes;

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($productPrice) {
            if (empty($productPrice->slug) && ! empty($productPrice->title)) {
                $productPrice->slug = Str::slug($productPrice->title);
            }
        });
    }

    protected $fillable = [
        'id',
        'product_id',
        'title',
        'slug',
        'amount',
        'currency',
        'billing_period',
        'trial_days',
        'gateway_data',
        'is_active',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'trial_days' => 'integer',
        'gateway_data' => 'array',
        'is_active' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public static function getBillingPeriods(): array
    {
        return ['once', 'daily', 'weekly', 'monthly', 'yearly'];
    }
}
