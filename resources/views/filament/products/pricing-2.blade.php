@php
    // Resolve $product robustly in case it is not a Product instance.
    $resolvedProduct = $product instanceof \App\Models\Product
        ? $product
        : (function () {
            $routeRecord = request()->route('record');
            return $routeRecord instanceof \App\Models\Product
                ? $routeRecord
                : (\App\Models\Product::query()->whereKey($routeRecord)->first());
        })();
@endphp

@if ($resolvedProduct)
    @livewire(\App\Filament\Resources\Products\Widgets\ProductPricesWidget::class, ['record' => $resolvedProduct])
@else
    <div class="text-sm text-gray-500">
        Pricing is available after the product is created.
    </div>
@endif