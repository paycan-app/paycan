@php
    $record = $getRecord();
@endphp

<div>
    @if($record)
        @livewire(\App\Filament\Resources\Products\RelationManagers\PricesRelationManager::class, [
            'ownerRecord' => $record,
            'pageClass' => \App\Filament\Resources\Products\Pages\EditProduct::class,
        ])
    @else
        <div class="text-center py-8 text-gray-500">
            <p>Save the product first to add prices.</p>
        </div>
    @endif
</div>
