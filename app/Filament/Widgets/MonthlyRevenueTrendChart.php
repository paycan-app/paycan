<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class MonthlyRevenueTrendChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = 'Monthly Revenue Trend (Last 12 Months)';

    protected string $color = 'success';

    protected ?string $maxHeight = '40rem';

    protected function getData(): array
    {
        $data = [];
        $labels = [];

        // Get last 12 months of revenue data
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthStart = $month->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();

            $revenue = Transaction::where('status', 'completed')
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->sum('amount');

            $data[] = $revenue;
            $labels[] = $month->format('M Y');
        }

        return [
            'datasets' => [
                [
                    'label' => 'Revenue',
                    'data' => $data,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array|\Filament\Support\RawJs|null
    {
        return [
            'maintainAspectRatio' => false,
        ];
    }
}
