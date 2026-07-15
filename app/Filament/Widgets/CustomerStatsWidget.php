<?php

namespace App\Filament\Widgets;

use App\Models\Subscription;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CustomerStatsWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $totalCustomers = User::count();
        $newCustomersThisMonth = User::whereBetween('created_at', [
            now()->startOfMonth(),
            now()->endOfMonth(),
        ])->count();

        $newCustomersLastMonth = User::whereBetween('created_at', [
            now()->subMonth()->startOfMonth(),
            now()->subMonth()->endOfMonth(),
        ])->count();

        $activeSubscribers = Subscription::where('status', 'active')->count();

        // Daily new customers for this month chart
        $dailyNewCustomers = [];
        for ($i = 1; $i <= now()->day; $i++) {
            $date = now()->startOfMonth()->addDays($i - 1);
            $dailyNewCustomers[] = User::whereDate('created_at', $date)->count();
        }

        $customerGrowth = $newCustomersLastMonth > 0
            ? round((($newCustomersThisMonth - $newCustomersLastMonth) / $newCustomersLastMonth) * 100, 1)
            : 0;

        return [
            Stat::make('Total Customers', number_format($totalCustomers))
                ->description('All registered users')
                ->descriptionIcon('heroicon-m-users')
                ->chart([
                    $totalCustomers - 100, // Approximation for growth trend
                    $totalCustomers,
                ])
                ->color('info'),

            Stat::make('New This Month', number_format($newCustomersThisMonth))
                ->description($customerGrowth >= 0 ? "Up {$customerGrowth}% from last month" : 'Down '.abs($customerGrowth).'% from last month')
                ->descriptionIcon($customerGrowth >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->chart($dailyNewCustomers)
                ->color($customerGrowth >= 0 ? 'success' : 'warning'),

            Stat::make('Active Subscribers', number_format($activeSubscribers))
                ->description('Customers with active subscriptions')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('success'),
        ];
    }
}
