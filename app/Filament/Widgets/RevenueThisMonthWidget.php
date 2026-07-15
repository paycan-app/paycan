<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RevenueThisMonthWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $thisMonthRevenue = Transaction::where('status', 'completed')
            ->whereBetween('created_at', [
                now()->startOfMonth(),
                now()->endOfMonth(),
            ])
            ->sum('amount');

        $lastMonthRevenue = Transaction::where('status', 'completed')
            ->whereBetween('created_at', [
                now()->subMonth()->startOfMonth(),
                now()->subMonth()->endOfMonth(),
            ])
            ->sum('amount');

        // Get daily revenue for this month to show in chart
        $dailyRevenue = [];
        for ($i = 1; $i <= now()->day; $i++) {
            $date = now()->startOfMonth()->addDays($i - 1);
            $dailyRevenue[] = Transaction::where('status', 'completed')
                ->whereDate('created_at', $date)
                ->sum('amount');
        }

        $monthGrowth = $lastMonthRevenue > 0
            ? round((($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1)
            : 0;

        return [
            Stat::make('Revenue This Month', '$'.number_format($thisMonthRevenue, 2))
                ->description($monthGrowth >= 0 ? "Up {$monthGrowth}% from last month" : 'Down '.abs($monthGrowth).'% from last month')
                ->descriptionIcon($monthGrowth >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->chart($dailyRevenue)
                ->color($monthGrowth >= 0 ? 'success' : 'danger'),
        ];
    }
}
