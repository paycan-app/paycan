<?php

namespace App\Filament\Resources\Subscriptions\Pages;

use App\Filament\Resources\Subscriptions\SubscriptionResource;
use App\Services\Payment\PaymentService;
use Filament\Actions\Action;
use Filament\Forms\Components\KeyValue;
use Filament\Resources\Pages\ViewRecord;

class ViewSubscription extends ViewRecord
{
    protected static string $resource = SubscriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('updateSubscriptionMeta')
                ->label('Update Subscription Metadata')
                ->color('primary')
                ->modalHeading('Update Subscription Metadata')
                ->form([
                    KeyValue::make('meta')
                        ->label('Subscription Metadata')
                        ->keyLabel('Attribute Name')
                        ->valueLabel('Attribute Value')
                        ->reorderable()
                        ->addActionLabel('Add Custom Field')
                        ->default($this->record->meta ?? []),
                ])
                ->action(function (array $data): void {
                    $this->record->meta = $data['meta'] ?? [];
                    $this->record->save();
                    $this->record->refresh();
                }),

            Action::make('cancelSubscription')
                ->label('Cancel Subscription')
                ->color('danger')
                ->requiresConfirmation()
                ->visible(fn () => $this->record->status !== 'canceled')
                ->action(function (): void {
                    $service = app(\App\Services\Payment\PaymentService::class);
                    $service->cancelSubscription($this->record);
                    $this->record->refresh();
                }),

            Action::make('resumeSubscription')
                ->label('Resume Subscription')
                ->color('success')
                ->visible(fn () => $this->record->status === 'canceled' && (! $this->record->ends_at || ! $this->record->ends_at->isPast()))
                ->action(function (): void {
                    $service = app(\App\Services\Payment\PaymentService::class);
                    $service->resumeSubscription($this->record);
                    $this->record->refresh();
                }),
        ];
    }
}
