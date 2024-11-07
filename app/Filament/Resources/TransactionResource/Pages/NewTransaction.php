<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use Filament\Resources\Pages\Page;

class NewTransaction extends Page
{
    protected static string $resource = TransactionResource::class;

    protected static string $view = 'filament.resources.transaction-resource.pages.transaction';

    protected static ?string $title = 'New Transaction';

    // protected ?string $heading = 'Custom Page Heading';
}
