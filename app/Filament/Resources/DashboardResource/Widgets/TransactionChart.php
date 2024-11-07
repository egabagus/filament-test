<?php

namespace App\Filament\Resources\DashboardResource\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class TransactionChart extends ChartWidget
{
    protected static ?string $heading = 'Transaction Counter';

    protected function getData(): array
    {
        $data = Transaction::select(
            DB::raw('DATE(date)'),
            DB::raw('COUNT(*) AS total')
        )->groupBy(DB::raw('DATE(date)'));

        $result = $data->pluck('total')->toArray();
        $date = $data->pluck('DATE(date)')->toArray();
        // dd($result);
        return [
            'datasets' => [
                [
                    'label' => 'Number of Transactions',
                    'data' => $result,
                ],
            ],
            'labels' => $date,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
