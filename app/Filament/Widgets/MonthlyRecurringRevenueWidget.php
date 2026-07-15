<?php

namespace App\Filament\Widgets;

use App\Models\Subscription;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MonthlyRecurringRevenueWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        // Calculate MRR from active subscriptions
        $currentMRR = Subscription::where('status', 'active')
            ->with('productPrice')
            ->get()
            ->sum(function ($subscription) {
                return $subscription->productPrice ? $subscription->productPrice->amount : 0;
            });

        // Calculate last month's MRR for comparison
        $lastMonthMRR = Subscription::where('status', 'active')
            ->where('created_at', '<=', now()->subMonth())
            ->with('productPrice')
            ->get()
            ->sum(function ($subscription) {
                return $subscription->productPrice ? $subscription->productPrice->amount : 0;
            });

        $mrrGrowth = $lastMonthMRR > 0
            ? round((($currentMRR - $lastMonthMRR) / $lastMonthMRR) * 100, 1)
            : 0;

        return [
            Stat::make('Monthly Recurring Revenue', '$'.number_format($currentMRR, 2))
                ->description($mrrGrowth >= 0 ? "Growth: +{$mrrGrowth}%" : "Decline: {$mrrGrowth}%")
                ->descriptionIcon($mrrGrowth >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->chart([
                    $lastMonthMRR,
                    $currentMRR,
                ])
                ->color($mrrGrowth >= 0 ? 'success' : 'warning'),
        ];
    }
}
