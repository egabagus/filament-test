<?php

namespace App\Filament\Resources\DashboardResource\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class AmountChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Total Amounts';

    protected function getData(): array
    {
        $data = Transaction::select(
            DB::raw('DATE(date)'),
            DB::raw('SUM(total_amount) AS amount')
        )->groupBy(DB::raw('DATE(date)'));

        $result = $data->pluck('amount')->toArray();
        $date = $data->pluck('DATE(date)')->toArray();
        // dd($result);
        return [
            'datasets' => [
                [
                    'label' => 'Amounts',
                    'data' => $result,
                ],
            ],
            'labels' => $date,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
