<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // Revenue metrics
        $totalRevenue = Transaction::where('status', 'completed')->sum('amount');
        $thisMonthRevenue = Transaction::where('status', 'completed')
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->sum('amount');
        $lastMonthRevenue = Transaction::where('status', 'completed')
            ->whereBetween('created_at', [
                now()->subMonth()->startOfMonth(),
                now()->subMonth()->endOfMonth(),
            ])
            ->sum('amount');

        // Customer metrics
        $totalCustomers = User::count();
        $newCustomersThisMonth = User::whereBetween('created_at', [
            now()->startOfMonth(),
            now()->endOfMonth(),
        ])->count();
        $activeSubscribers = Subscription::where('status', 'active')->count();

        // MRR calculation
        $currentMRR = Subscription::where('status', 'active')
            ->with('productPrice')
            ->get()
            ->sum(function ($subscription) {
                return $subscription->productPrice ? $subscription->productPrice->amount : 0;
            });

        // Orders
        $totalOrders = Order::count();

        // Growth calculations
        $revenueGrowth = $lastMonthRevenue > 0
            ? round((($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1)
            : 0;

        return [
            Stat::make('Total Revenue', '$'.number_format($totalRevenue, 2))
                ->description($revenueGrowth >= 0 ? "Up {$revenueGrowth}% this month" : 'Down '.abs($revenueGrowth).'% this month')
                ->descriptionIcon($revenueGrowth >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($revenueGrowth >= 0 ? 'success' : 'danger'),

            Stat::make('Monthly Recurring Revenue', '$'.number_format($currentMRR, 2))
                ->description('From active subscriptions')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('info'),

            Stat::make('Total Customers', number_format($totalCustomers))
                ->description("{$newCustomersThisMonth} new this month")
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Stat::make('Active Subscribers', number_format($activeSubscribers))
                ->description('Customers with active subscriptions')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Total Orders', number_format($totalOrders))
                ->description('All-time orders processed')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('warning'),

            Stat::make('Revenue This Month', '$'.number_format($thisMonthRevenue, 2))
                ->description('Current month performance')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info'),
        ];
    }
}
