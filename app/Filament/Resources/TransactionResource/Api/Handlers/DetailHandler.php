<?php

namespace App\Filament\Resources\TransactionResource\Api\Handlers;

use App\Filament\Resources\SettingResource;
use App\Filament\Resources\TransactionResource;
use App\Models\Transaction;
use Carbon\Carbon;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;

class DetailHandler extends Handlers
{
    public static string | null $uri = '/{id}';
    public static string | null $resource = TransactionResource::class;


    public function handler(Request $request)
    {
        $id = $request->route('id');

        $header = Transaction::with('details.item')->where('id', $id)->first();

        $data = [
            'code' => $header->code,
            'date' => Carbon::parse($header->date)->format('d-m-Y'),
            'total_amount' => $header->total_amount
        ];

        foreach ($header->details as $detail) {
            $data['details'][] = [
                'item_name' => $detail->item->name,
                'quantity' => $detail->qty,
                'price' => $detail->price,
                'total_price' => $detail->total_price,
            ];
        }

        return $data;

        // $query = static::getEloquentQuery();

        // $query = QueryBuilder::for(
        //     $query->where(static::getKeyName(), $id)
        // )
        //     ->first();

        // if (!$query) return static::sendNotFoundResponse();

        // $transformer = static::getApiTransformer();

        // return new $transformer($query);
    }
}
