<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All')
                ->badge(fn () => $this->getModel()::query()->count())
                ->badgeColor('info'),

            'pending' => Tab::make('Pending')
                ->badge(fn () => $this->getModel()::query()->where('status', 'pending')->count())
                ->badgeColor('gray')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'pending')),

            'processing' => Tab::make('Processing')
                ->badge(fn () => $this->getModel()::query()->where('status', 'processing')->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'processing')),

            'completed' => Tab::make('Completed')
                ->badge(fn () => $this->getModel()::query()->where('status', 'completed')->count())
                ->badgeColor('success')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'completed')),

            'failed' => Tab::make('Failed')
                ->badge(fn () => $this->getModel()::query()->where('status', 'failed')->count())
                ->badgeColor('danger')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'failed')),

            'cancelled' => Tab::make('Cancelled')
                ->badge(fn () => $this->getModel()::query()->where('status', 'cancelled')->count())
                ->badgeColor('gray')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'cancelled')),

            'refunded' => Tab::make('Refunded')
                ->badge(fn () => $this->getModel()::query()->where('status', 'refunded')->count())
                ->badgeColor('info')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'refunded')),
        ];
    }
}
