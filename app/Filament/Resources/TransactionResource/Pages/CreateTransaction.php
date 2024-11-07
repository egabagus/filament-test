<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use App\Models\DetailTransaction;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateTransaction extends CreateRecord
{
    protected static string $resource = TransactionResource::class;

    // protected function handleRecordCreation(array $data): Model
    // {
    //     $record =  static::getModel()::create($data);

    //     foreach ($data['item'] as $key => $value) {
    //         $detail = new DetailTransaction();
    //         $detail->transaction_code       = $record->code;
    //         $detail->item_code              = $value['item_code'];
    //     }
    // }
}
