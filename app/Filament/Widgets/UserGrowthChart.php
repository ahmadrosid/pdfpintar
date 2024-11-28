<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class UserGrowthChart extends ChartWidget
{
    protected static ?string $heading = 'User Growth';
    
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $periods = [
            '1 month' => Carbon::now()->subMonth(),
            '3 months' => Carbon::now()->subMonths(3),
            '6 months' => Carbon::now()->subMonths(6),
            '1 year' => Carbon::now()->subYear(),
        ];

        $datasets = [];
        foreach ($periods as $label => $startDate) {
            $userCount = User::where('created_at', '>=', $startDate)->count();
            $datasets[] = $userCount;
        }

        return [
            'datasets' => [
                [
                    'label' => 'New Users',
                    'data' => $datasets,
                    'fill' => true,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.5)',
                    'borderColor' => 'rgb(59, 130, 246)',
                ],
            ],
            'labels' => array_keys($periods),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
            ],
        ];
    }
}
