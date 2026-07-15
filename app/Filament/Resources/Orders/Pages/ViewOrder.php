<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use App\Models\Fulfillment;
use App\Models\Order;
use App\Models\Transaction;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ViewRecord;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('createFulfillment')
                ->label('New Fulfillment')
                ->modalHeading('Create Fulfillment')
                ->form([
                    Select::make('status')
                        ->label('Status')
                        ->options(fn () => array_combine(Fulfillment::getStatuses(), array_map('ucfirst', Fulfillment::getStatuses())))
                        ->required()
                        ->default('pending'),
                    Select::make('type')
                        ->label('Type')
                        ->options(fn () => array_combine(Fulfillment::getTypes(), array_map('ucfirst', Fulfillment::getTypes())))
                        ->required(),
                    TextInput::make('tracking_id')
                        ->label('Tracking ID'),
                    Select::make('provider')
                        ->label('Provider')
                        ->options(fn () => array_combine(Fulfillment::getProviders(), Fulfillment::getProviders())),
                    DateTimePicker::make('fulfilled_at')
                        ->label('Fulfilled At'),
                    KeyValue::make('meta')
                        ->label('Metadata')
                        ->keyLabel('Attribute Name')
                        ->valueLabel('Attribute Value')
                        ->reorderable()
                        ->addActionLabel('Add Custom Field'),
                ])
                ->action(function (array $data): void {
                    Fulfillment::create([
                        'order_id' => $this->record->id,
                        'status' => $data['status'],
                        'type' => $data['type'] ?? null,
                        'tracking_id' => $data['tracking_id'] ?? null,
                        'provider' => $data['provider'] ?? null,
                        'fulfilled_at' => $data['fulfilled_at'] ?? null,
                        'meta' => $data['meta'] ?? [],
                    ]);
                }),

            Action::make('updateStatus')
                ->label('Update Status')
                ->modalHeading('Update Order Status')
                ->form([
                    Select::make('status')
                        ->label('Order Status')
                        ->options(fn () => array_combine(Order::getStatuses(), array_map('ucfirst', Order::getStatuses())))
                        ->required()
                        ->default($this->record->status),
                ])
                ->action(function (array $data): void {
                    $this->record->status = $data['status'];
                    $this->record->save();
                }),

            Action::make('updateMeta')
                ->label('Update Metadata')
                ->modalHeading('Update Order Metadata')
                ->color('primary')
                ->form([
                    KeyValue::make('meta')
                        ->label('Order Metadata')
                        ->keyLabel('Attribute Name')
                        ->valueLabel('Attribute Value')
                        ->reorderable()
                        ->addActionLabel('Add Custom Field')
                        ->default($this->record->meta ?? []),
                ])
                ->action(function (array $data): void {
                    $this->record->meta = $data['meta'] ?? [];
                    $this->record->save();
                }),

            Action::make('createTransaction')
                ->label('New Transaction')
                ->modalHeading('Create Transaction')
                ->color('primary')
                ->form([
                    Select::make('type')
                        ->label('Type')
                        ->options(fn () => array_combine(Transaction::getTypes(), array_map('ucfirst', Transaction::getTypes())))
                        ->required(),
                    Select::make('status')
                        ->label('Status')
                        ->options(fn () => array_combine(Transaction::getStatuses(), array_map('ucfirst', Transaction::getStatuses())))
                        ->required()
                        ->default('pending'),
                    TextInput::make('gateway')
                        ->label('Gateway')
                        ->placeholder('stripe, paypal')
                        ->default($this->record->gateway ?? null)
                        ->required(),
                    TextInput::make('amount')
                        ->label('Amount')
                        ->numeric()
                        ->required(),
                    TextInput::make('currency')
                        ->label('Currency')
                        ->default($this->record->currency ?? 'USD'),
                    TextInput::make('gateway_transaction_id')
                        ->label('Gateway Transaction ID')
                        ->required(),
                    KeyValue::make('gateway_data')
                        ->label('Gateway Data')
                        ->keyLabel('Key')
                        ->valueLabel('Value')
                        ->reorderable()
                        ->addActionLabel('Add Field'),
                    KeyValue::make('meta')
                        ->label('Metadata')
                        ->keyLabel('Key')
                        ->valueLabel('Value')
                        ->reorderable()
                        ->addActionLabel('Add Field'),
                ])
                ->action(function (array $data): void {
                    Transaction::create([
                        'user_id' => $this->record->user_id,
                        'order_id' => $this->record->id,
                        // Safely handle when the order has no subscription
                        'subscription_id' => $this->record->subscription?->id,
                        'type' => $data['type'],
                        'status' => $data['status'],
                        'gateway' => $data['gateway'],
                        'amount' => $data['amount'],
                        'currency' => $data['currency'] ?? 'USD',
                        'gateway_transaction_id' => $data['gateway_transaction_id'],
                        'gateway_data' => $data['gateway_data'] ?? [],
                        'meta' => $data['meta'] ?? [],
                    ]);
                }),
        ];
    }
}
