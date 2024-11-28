<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\StatsOverview;
use App\Models\User;
use Flowframe\Trend\Trend;
use Filament\Pages\Dashboard as BaseDashboard;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Carbon;

class Dashboard extends BaseDashboard
{
    public static ?string $title = "Dashboard";

    protected static string $view = 'filament.pages.dashboard';

    protected function getHeaderWidgets(): array
    {
        return [
            StatsOverview::class,
        ];
    }

    public function getColumns(): int | array
    {
        return 1;
    }

    protected function getViewData(): array
    {
        $data = Trend::model(User::class)
            ->dateColumn('created_at')
            ->between(
                start: now()->subMonths(3)->startOfMonth(),
                end: now()->endOfMonth(),
            )
            ->perWeek()
            ->count();

        return [
            'chartData' => [
                'datasets' => [
                    [
                        'label' => 'New Users',
                        'data' => $data->map(fn (TrendValue $value) => $value->aggregate)->toArray(),
                        'fill' => false, // Changed to false to remove fill
                        'borderColor' => '#ff8c37', // Changed to orange color
                        'borderWidth' => 2,
                        'tension' => 0.4, // Increased tension for smoother curves
                        'pointRadius' => 3,
                        'pointBackgroundColor' => '#ff8c37',
                        'pointBorderColor' => '#ff8c37',
                        'pointBorderWidth' => 0, // Removed point border
                    ],
                ],
                'labels' => $data->map(fn (TrendValue $value) => $value->date)->toArray(),
            ],
            'chartOptions' => [
                'plugins' => [
                    'legend' => [
                        'display' => false, // Hide legend
                    ],
                ],
                'responsive' => true,
                'maintainAspectRatio' => true,
                'scales' => [
                    'y' => [
                        'beginAtZero' => true,
                        'grid' => [
                            'color' => 'rgba(255, 255, 255, 0.1)', // Lighter grid lines
                            'drawBorder' => false,
                        ],
                        'ticks' => [
                            'color' => 'rgba(255, 255, 255, 0.5)', // Light colored ticks
                        ],
                    ],
                    'x' => [
                        'grid' => [
                            'display' => false,
                        ],
                        'ticks' => [
                            'color' => 'rgba(255, 255, 255, 0.5)', // Light colored ticks
                        ],
                    ],
                ],
                'layout' => [
                    'padding' => 20
                ],
            ],
        ];
    }
}
