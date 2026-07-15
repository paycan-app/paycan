<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Database\QueryException;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function getRedirectUrl(): string
    {
        return ProductResource::getUrl('edit', ['record' => $this->getRecord()]) . '?tab=pricing';
    }

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        try {
            return parent::handleRecordCreation($data);
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
                        ->body('A product with this ID already exists. Please choose a different ID or leave it empty to auto-generate.')
                        ->danger()
                        ->send();
                        
                    $this->halt();
                }
            }
            
            // Re-throw the exception if it's not a handled constraint violation
            throw $e;
        }
    }
}
