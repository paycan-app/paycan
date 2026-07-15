<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TotalRevenueWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $totalRevenue = Transaction::where('status', 'completed')->sum('amount');
        $lastMonthRevenue = Transaction::where('status', 'completed')
            ->whereBetween('created_at', [
                now()->subMonth()->startOfMonth(),
                now()->subMonth()->endOfMonth(),
            ])
            ->sum('amount');
        $thisMonthRevenue = Transaction::where('status', 'completed')
            ->whereBetween('created_at', [
                now()->startOfMonth(),
                now()->endOfMonth(),
            ])
            ->sum('amount');

        $growthPercentage = $lastMonthRevenue > 0
            ? round((($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1)
            : 0;

        return [
            Stat::make('Total Revenue', '$'.number_format($totalRevenue, 2))
                ->description($growthPercentage >= 0 ? 'Growth this month' : 'Decline this month')
                ->descriptionIcon($growthPercentage >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->chart([
                    $lastMonthRevenue,
                    $thisMonthRevenue,
                ])
                ->color($growthPercentage >= 0 ? 'success' : 'danger'),
        ];
    }
}
