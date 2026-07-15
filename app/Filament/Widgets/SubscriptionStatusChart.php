<?php

namespace App\Filament\Widgets;

use App\Models\Subscription;
use Filament\Widgets\ChartWidget;

class SubscriptionStatusChart extends ChartWidget
{
    protected static ?int $sort = 3;

    protected ?string $heading = 'Subscription Status Distribution';

    protected ?string $maxHeight = '10rem';

    protected function getData(): array
    {
        $statuses = Subscription::getStatuses();
        $data = [];
        $labels = [];
        $colors = [];

        foreach ($statuses as $status) {
            $count = Subscription::where('status', $status)->count();
            $data[] = $count;
            $labels[] = ucfirst($status);

            // Color mapping for different statuses
            $colors[] = match ($status) {
                'active' => '#10b981',
                'trialing' => '#3b82f6',
                'past_due' => '#f59e0b',
                'canceled' => '#ef4444',
                'incomplete' => '#6b7280',
                'incomplete_expired' => '#9ca3af',
                default => '#6b7280',
            };
        }

        return [
            'datasets' => [
                [
                    'label' => 'Subscriptions',
                    'data' => $data,
                    'backgroundColor' => $colors,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array|\Filament\Support\RawJs|null
    {
        return [
            'maintainAspectRatio' => false,
        ];
    }
}
