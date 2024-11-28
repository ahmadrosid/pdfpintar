<?php

namespace App\Filament\Widgets;

use App\Models\Document;
use App\Models\Thread;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected function getColumns(): int
    {
        return 3;
    }

    protected function getStats(): array
    {
        return [
            // First Row - User Stats
            Stat::make('Verified Users', User::where('email_verified_at', '!=', null)->count())
                ->description('Email verified users')
                ->color('success'),
            Stat::make('Total Chats', Thread::count())
                ->description('Chat threads')
                ->color('warning'),
            Stat::make('Total Users', User::count())
                ->description('Total registered users')
                ->color('success'),

            // Second Row - Document Stats
            Stat::make('Total Documents', Document::count())
                ->description('All uploaded documents')
                ->color('info'),
            Stat::make('Shared Documents', Document::where('is_public', true)->count())
                ->description('Publicly shared documents')
                ->color('info'),
            Stat::make('Private Documents', Document::where('is_public', false)->count())
                ->description('Private documents')
                ->color('info'),
        ];
    }
}
