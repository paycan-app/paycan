<?php

namespace App\Models;

use App\Traits\HasStringId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, HasStringId, SoftDeletes;

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug) && ! empty($product->title)) {
                $baseSlug = Str::slug($product->title);
                $slug = $baseSlug;
                $counter = 1;
                
                // Ensure the slug is unique
                while (static::where('slug', $slug)->exists()) {
                    $slug = $baseSlug . '-' . $counter;
                    $counter++;
                }
                
                $product->slug = $slug;
            }
        });

        static::updating(function ($product) {
            // If slug is being updated, ensure it's unique
            if ($product->isDirty('slug') && ! empty($product->slug)) {
                $baseSlug = $product->slug;
                $slug = $baseSlug;
                $counter = 1;
                
                // Ensure the slug is unique (excluding current record)
                while (static::where('slug', $slug)->where('id', '!=', $product->id)->exists()) {
                    $slug = $baseSlug . '-' . $counter;
                    $counter++;
                }
                
                $product->slug = $slug;
            }
        });
    }

    protected $fillable = [
        'id',
        'title',
        'slug',
        'description',
        'type',
        'image',
        'file',
        'is_active',
        'meta',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'meta' => 'array',
    ];

    public function prices(): HasMany
    {
        return $this->hasMany(ProductPrice::class);
    }

    public function activePrices()
    {
        return $this->prices()->where('is_active', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public static function getTypes(): array
    {
        return ['physical', 'digital', 'service', 'subscription'];
    }
}
