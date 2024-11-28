<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Carbon;

class UserGrowthChart extends ChartWidget
{
    protected static ?string $heading = 'User Growth';
    
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $maxHeight = '300px';

    protected static ?array $options = [
        'plugins' => [
            'legend' => [
                'display' => true,
                'position' => 'top',
            ],
        ],
        'responsive' => true,
        'maintainAspectRatio' => true,
        'scales' => [
            'y' => [
                'beginAtZero' => true,
                'grid' => [
                    'display' => true,
                    'color' => 'rgba(107, 114, 128, 0.1)',
                ],
            ],
            'x' => [
                'grid' => [
                    'display' => false,
                ],
            ],
        ],
    ];

    protected function getData(): array
    {
        $data = Trend::model(User::class)
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfMonth(),
            )
            ->perMonth()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'New Users',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                    'fill' => true,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'borderWidth' => 2,
                    'tension' => 0.3,
                    'pointRadius' => 4,
                    'pointBackgroundColor' => 'rgb(59, 130, 246)',
                    'pointBorderColor' => '#fff',
                    'pointBorderWidth' => 2,
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => Carbon::parse($value->date)->format('M Y')),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
