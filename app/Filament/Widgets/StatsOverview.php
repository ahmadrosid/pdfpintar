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
            Stat::make('Total Users', User::count())
                ->description('Total registered users')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),
            Stat::make('Verified Users', User::where('email_verified_at', '!=', null)->count())
                ->description('Email verified users')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),
            Stat::make('Total Chats', Thread::count())
                ->description('Chat threads')
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color('warning'),

            // Second Row - Document Stats
            Stat::make('Total Documents', Document::count())
                ->description('All uploaded documents')
                ->descriptionIcon('heroicon-m-document')
                ->color('info'),
            Stat::make('Shared Documents', Document::where('is_public', true)->count())
                ->description('Publicly shared documents')
                ->descriptionIcon('heroicon-m-share')
                ->color('info'),
            Stat::make('Private Documents', Document::where('is_public', false)->count())
                ->description('Private documents')
                ->descriptionIcon('heroicon-m-lock-closed')
                ->color('info'),
        ];
    }
}
