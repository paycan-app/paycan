<?php

namespace App\Rules;

use App\Models\ProductPrice;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PriceBelongsToProduct implements ValidationRule
{
    public function __construct(private ?string $productId) {}

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($this->productId === null) {
            return;
        }

        $price = ProductPrice::find($value);

        if (! $price || $price->product_id !== $this->productId) {
            $fail('The selected :attribute does not belong to the specified product.');
        }
    }
}
