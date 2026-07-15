<?php

namespace App\Filament\Resources\Subscriptions\Pages;

use App\Filament\Resources\Subscriptions\SubscriptionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListSubscriptions extends ListRecords
{
    protected static string $resource = SubscriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getDefaultActiveTab(): string
    {
        return 'all';
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All')
                ->badge(fn () => $this->getModel()::query()->count())
                ->badgeColor('info'),

            'active' => Tab::make('Active')
                ->badge(fn () => $this->getModel()::query()->where('status', 'active')->count())
                ->badgeColor('success')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'active')),

            'trialing' => Tab::make('Trialing')
                ->badge(fn () => $this->getModel()::query()->where('status', 'trialing')->count())
                ->badgeColor('info')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'trialing')),

            'past_due' => Tab::make('Past Due')
                ->badge(fn () => $this->getModel()::query()->where('status', 'past_due')->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'past_due')),

            'canceled' => Tab::make('Canceled')
                ->badge(fn () => $this->getModel()::query()->where('status', 'canceled')->count())
                ->badgeColor('danger')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'canceled')),

            'incomplete' => Tab::make('Incomplete')
                ->badge(fn () => $this->getModel()::query()->where('status', 'incomplete')->count())
                ->badgeColor('gray')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'incomplete')),

            'incomplete_expired' => Tab::make('Incomplete Expired')
                ->badge(fn () => $this->getModel()::query()->where('status', 'incomplete_expired')->count())
                ->badgeColor('danger')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'incomplete_expired')),
        ];
    }
}
