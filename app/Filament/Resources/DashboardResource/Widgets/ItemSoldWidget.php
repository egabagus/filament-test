<?php

namespace App\Filament\Resources\DashboardResource\Widgets;

use App\Models\DetailTransaction;
use Filament\Support\Colors\Color;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ItemSoldWidget extends ChartWidget
{
    protected static ?string $heading = 'Item Sold';

    protected function getData(): array
    {
        $data = DetailTransaction::select(
            'item_id',
            DB::raw('SUM(qty) AS qty')
        )->groupBy('item_id')->get();
        // dd($data);

        $result = $data->pluck('qty')->toArray();
        $item = $data->pluck('item_id')->toArray();
        // dd($item);
        return [
            'datasets' => [
                [
                    'label' => 'Amounts',
                    'data' => $result,
                    'backgroundColor' => [
                        'rgb(20, 184, 166)',
                        'rgb(139, 92, 246)'
                    ],
                ],
            ],
            'labels' => $item,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
