<?php

namespace App\Filament\Resources\TransactionResource\Api\Handlers;

use App\Filament\Resources\SettingResource;
use App\Filament\Resources\TransactionResource;
use App\Models\Customer;
use App\Models\PaymentMethod;
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
        $cust = Customer::where('id', $header->cust_id)->select('id', 'code', 'name', 'phone', 'address')->first();
        $paymment = PaymentMethod::where('id', $header->payment_id)->select('id', 'name')->first();

        $data = [
            'code' => $header->code,
            'date' => Carbon::parse($header->date)->format('d-m-Y h:i:s'),
            'total_amount' => $header->total_amount,
            'payment_status' => $header->payment_status,
            'payment' => $paymment,
            'customer' => $cust ?? ""
        ];

        foreach ($header->details as $detail) {
            $data['item'][] = [
                'item_name' => $detail->item->name,
                'quantity' => $detail->qty,
                'price' => $detail->price,
                'total_price' => $detail->total_price,
            ];
        }

        return response()->json([
            'status' => 'OK',
            'data' => $data
        ]);
    }
}
