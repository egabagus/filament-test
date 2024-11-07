<?php

namespace App\Filament\Resources\TransactionResource\Widgets;

use App\Models\Transaction;
use Carbon\Carbon;
use Filament\Forms\Components\Card;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card as StatsOverviewWidgetCard;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            StatsOverviewWidgetCard::make('Transaction Today', Transaction::whereDate('date', Carbon::now())->count())
                ->color('success')
                ->descriptionIcon('heroicon-o-calendar-days')
                ->description(Carbon::parse('today')->format('d-m-Y')),
            StatsOverviewWidgetCard::make('Transaction This Month', Transaction::whereMonth('date', Carbon::now()->format('m'))->count())
                ->color('primary')
                ->descriptionIcon('heroicon-o-calendar-days')
                ->description(Carbon::parse('today')->isoFormat('MMMM Y')),
            StatsOverviewWidgetCard::make('Total Amount Today', Transaction::whereDate('date', Carbon::now())->sum('total_amount'))
                // ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                ->description('3% increase')
                ->color('warning')
                ->descriptionIcon('heroicon-m-arrow-trending-up'),
        ];
    }
}
