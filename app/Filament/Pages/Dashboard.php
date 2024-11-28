<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\StatsOverview;
use App\Models\User;
use Filament\Pages\Dashboard as BaseDashboard;
use Flowframe\Trend\Trend;
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
            ->between(
                start: now()->subMonths(6)->startOfMonth(),
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
                'labels' => $data->map(fn (TrendValue $value) => $value->date)->toArray(),
            ],
            'chartOptions' => [
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
            ],
        ];
    }
}
