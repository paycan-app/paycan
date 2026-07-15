<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\Products\Widgets\ProductPricesWidget;
use Filament\Notifications\Notification;
use Illuminate\Database\QueryException;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
    
    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        try {
            return parent::handleRecordUpdate($record, $data);
        } catch (QueryException $e) {
            // Handle unique constraint violations
            if ($e->getCode() === '23000' || str_contains($e->getMessage(), 'UNIQUE constraint failed')) {
                if (str_contains($e->getMessage(), 'products.slug')) {
                    Notification::make()
                        ->title('Duplicate URL Slug')
                        ->body('A product with this URL slug already exists. Please choose a different slug.')
                        ->danger()
                        ->send();
                        
                    $this->halt();
                } elseif (str_contains($e->getMessage(), 'products.id')) {
                    Notification::make()
                        ->title('Duplicate Product ID')
                        ->body('A product with this ID already exists. Please choose a different ID.')
                        ->danger()
                        ->send();
                        
                    $this->halt();
                }
            }
            
            // Re-throw the exception if it's not a handled constraint violation
            throw $e;
        }
    }
    // Removed getHeaderWidgets() to avoid showing Prices in the header
}
