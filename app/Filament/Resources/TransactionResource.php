<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use App\Filament\Resources\TransactionResource\Widgets\StatsOverview;
use App\Models\DetailTransaction;
use App\Models\Item;
use App\Models\PaymentMethod;
use App\Models\Transaction;
use Awcodes\TableRepeater\Components\TableRepeater;
use Awcodes\TableRepeater\Header;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Type\Integer;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static string $relationship = 'details';
    protected static int $price = 0;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('code')
                    ->placeholder('Auto')
                    ->readOnly(),

                DatePicker::make('date')
                    ->placeholder('Date')
                    ->required()
                    ->format('Y-m-d'),

                Select::make('payment_id')
                    ->label('Payment Method')
                    ->relationship(
                        name: 'payment',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn(Builder $query) => $query->where('status', 1),
                    )
                    ->required(),

                Select::make('cust_id')
                    ->label('Customer')
                    ->relationship(
                        name: 'cust',
                        titleAttribute: 'name'
                    )
                    ->required(),

                Section::make('Details')
                    ->schema([
                        TableRepeater::make('details')
                            ->relationship()
                            ->headers([
                                Header::make('item')->align('center')->label('Item'),
                                Header::make('qty')->align('center')->label('Quantity'),
                                Header::make('price')->align('center')->label('Price'),
                                Header::make('tax')->align('center')->label('Tax'),
                                Header::make('tax_amount')->align('center')->label('Tax Amount'),
                                Header::make('total_price')->align('center')->label('Total Price'),
                            ])
                            ->schema([
                                Select::make('item_id')
                                    ->label('Item')
                                    ->relationship(
                                        name: 'item',
                                        titleAttribute: 'name',
                                        modifyQueryUsing: fn(Builder $query) => $query->where('status', 1),
                                    )
                                    ->reactive()
                                    ->live()  // Menambahkan live() di Select
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        $item = Item::find($state);
                                        if ($item) {
                                            $set('price', $item->price);  // Menyimpan harga item
                                        }
                                    })
                                    ->required(),

                                TextInput::make('qty')
                                    ->reactive()
                                    ->live()  // Menambahkan live() di Qty
                                    ->afterStateUpdated(function ($state, callable $set, $get) {
                                        $price = $get('price');
                                        // Menghitung total_price berdasarkan qty dan price
                                        if ($price && $state) {
                                            $total = $state * $price;
                                            $set('total_price', $total);  // Mengupdate total_price
                                        } else {
                                            $set('total_price', 0);  // Jika qty atau harga kosong, set total_price ke 0
                                        }
                                    })
                                    ->numeric(),

                                TextInput::make('price')
                                    ->dehydrated()
                                    ->numeric()
                                    ->readOnly(),

                                Checkbox::make('tax')
                                    ->label('Tax')
                                    ->reactive()
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $set, $get) {
                                        if ($state == true) {
                                            $price = $get('price');
                                            $qty = $get('qty');
                                            $taxAmount = 0.12 * ($price * $qty);
                                            $set('tax_amount', $taxAmount);
                                            $totalPrice = ($price * $qty) - $taxAmount;
                                            $set('total_price', $totalPrice);
                                        } else {
                                            $price = $get('price');
                                            $qty = $get('qty');
                                            $taxAmount = 0;
                                            $set('tax_amount', $taxAmount);
                                            $totalPrice = ($price * $qty) - $taxAmount;
                                            $set('total_price', $totalPrice);
                                        }
                                    }),

                                TextInput::make('tax_amount')
                                    ->dehydrated()
                                    ->numeric()
                                    ->readOnly(),

                                TextInput::make('total_price')
                                    ->dehydrated()
                                    ->numeric()
                                    ->readOnly(),
                            ])
                            ->columnSpan('full')
                            ->reactive()
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                self::updateTotals($get, $set);
                            })
                    ]),

                Section::make('Payment Details')
                    ->label('Payment Details')
                    ->schema([
                        TextInput::make('subtotal')->dehydrated()
                            ->readOnly(),
                        TextInput::make('total_tax')->dehydrated()
                            ->readOnly(),
                        TextInput::make('total_amount')->dehydrated()
                            ->readOnly(),
                    ])
                    ->columns(2)
            ]);
        // ->afterSave(function (App\Filament\Resources\Forms $form) {
        //     Log::info('Successfully create by ' . auth()->email);
        // });
    }

    public static function updateTotals(Get $get, Set $set)
    {
        $selectedProducts = collect($get('details'))->filter(fn($item) => !empty($item['item_id']));
        $prices = Item::find($selectedProducts->pluck('item_id'))->pluck('price', 'id');

        $asubtotal = $selectedProducts->reduce(function ($subtotal, $product) use ($prices) {
            return $subtotal + ($prices[$product['item_id']] * $product['qty']);
        }, 0);

        $subtotal = $selectedProducts->reduce(function ($subtotal, $product) use ($prices) {
            return $subtotal + ($prices[$product['item_id']] * $product['qty']) - (0.12 * ($prices[$product['item_id']] * $product['qty']));
        }, 0);

        // dd($subtotal);
        $set('subtotal', $asubtotal);
        $set('total_amount', $subtotal);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')->searchable()->sortable(),
                TextColumn::make('date')->sortable()->dateTime(),
                TextColumn::make('total_amount')->sortable()->numeric(locale: 'id')->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.')),
                SelectColumn::make('payment_status')
                    ->label('Payment Status')
                    ->options([
                        0 => 'Unpaid',
                        1 => 'Paid',
                        2 => 'Canceled',
                    ])
            ])
            ->filters([
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('date')
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['date'],
                                fn(Builder $query, $date): Builder => $query->whereDate('date', '=', $date),
                            );
                    }),
                SelectFilter::make('payment_status')
                    ->label('Payment Status')
                    ->options([
                        0 => 'Unpaid',
                        1 => 'Paid',
                        2 => 'Canceled',
                    ])
            ])
            ->actions([
                Tables\actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                DeleteAction::make(),
                Tables\Actions\Action::make('pdf')
                    ->label('PDF')
                    ->color('success')
                    ->icon('heroicon-o-arrow-down-tray')
                    // ->url(route('invoice-page'))
                    // ->openUrlInNewTab(),
                    ->action(function (Model $record) {
                        return response()->streamDownload(function () use ($record) {
                            echo Pdf::loadHtml(
                                Blade::render('pdf.invoice', ['record' => $record])
                            )->stream();
                        }, 'TEST.pdf');
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
            'new-transaction' => Pages\NewTransaction::route('/new-transaction'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            StatsOverview::class,
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('payment_status', 0)->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }
}
