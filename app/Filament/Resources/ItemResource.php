<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ItemResource\Api\Transformers\ItemTransformer;
use App\Filament\Resources\ItemResource\Pages;
use App\Filament\Resources\ItemResource\RelationManagers;
use App\Models\Item;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class ItemResource extends Resource
{
    protected static ?string $model = Item::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Section::make()
                    ->columns([
                        'sm' => 1,
                        'xl' => 2,
                    ])
                    ->schema([
                        TextInput::make('code')
                            ->label('Code')
                            ->placeholder('Auto')
                            ->readOnly(),
                        // ->required(),

                        TextInput::make('name')
                            ->label('Item Name')
                            ->placeholder('Item Name')
                            ->required(),

                        TextInput::make('price')
                            ->label('Price')
                            ->placeholder('Price')
                            ->required()
                            ->integer(),

                        TextInput::make('stock')
                            ->label('Stock')
                            ->placeholder('Item Stock')
                            ->integer()
                            ->required(),

                        //category
                        Select::make('category_id')
                            ->label('Category')
                            ->relationship('category', 'name')
                            ->required(),

                        Textarea::make('desc')
                            ->label('Description')
                            ->placeholder('Description'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')->searchable()->sortable(),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('desc'),
                TextColumn::make('category.name'),
                TextColumn::make('price')->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.')),
                TextColumn::make('stock'),

                ToggleColumn::make('status')
                    ->label('Is Active'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        1 => 'Active',
                        0 => 'Non Active',
                    ])
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                DeleteAction::make()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    ExportBulkAction::make(),
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
            'index' => Pages\ListItems::route('/'),
            'create' => Pages\CreateItem::route('/create'),
            'edit' => Pages\EditItem::route('/{record}/edit'),
        ];
    }

    public static function getApiTransformer()
    {
        return ItemTransformer::class;
    }
}
