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
        $data = DetailTransaction::select('item_id', DB::raw('SUM(qty) AS qty'))
            ->with(['item' => function ($query) {
                $query->select('id', 'name');
            }])
            ->groupBy('item_id')
            ->get();

        $result = $data->pluck('qty')->toArray();
        $item = $data->pluck('item.name')->toArray();

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
