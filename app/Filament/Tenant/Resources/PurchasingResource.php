<?php

namespace App\Filament\Tenant\Resources;

use App\Filament\Tenant\Resources\PurchasingResource\Pages;
use App\Models\Tenants\Product;
use App\Models\Tenants\Purchasing;
use App\Models\Tenants\Supplier;
use Awcodes\TableRepeater\Components\TableRepeater;
use Awcodes\TableRepeater\Header;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PurchasingResource extends Resource
{
    protected static ?string $model = Purchasing::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box-arrow-down';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('supplier_id')
                    ->label(__('Supplier'))
                    ->options(Supplier::pluck('name', 'id'))
                    ->native(false)
                    ->searchable()
                    ->required()
                    ->createOptionForm([
                        TextInput::make('name')
                            ->required(),
                        TextInput::make('phone_number')
                            ->rule('regex:/^(\+?\d{1,3}[-.\s]?)?(\(?\d{3}\)?[-.\s]?)?\d{3}[-.\s]?\d{4}$/')
                            ->required(),
                    ])
                    ->createOptionUsing(function (array $data): int {
                        $category = new Supplier();
                        $category->fill($data);
                        $category->save();

                        return $category->getKey();
                    })
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('supplier_phone_number', Supplier::find($state)?->phone_number ?? ''))
                    ->live(),
                TextInput::make('supplier_phone_number')
                    ->label(__('Supplier Phone Number'))
                    ->readOnly(),
                DatePicker::make('due_date')
                    ->label(__('Due Date'))
                    ->native(false)
                    ->required(),
                DatePicker::make('date')
                    ->label(__('Selling Date'))
                    ->native(false)
                    ->required(),
                FileUpload::make('image')
                    ->label(__('Attachment')),
                TableRepeater::make('stocks')
                    ->headers([
                        Header::make('product_name')
                            ->label(__('Product Name'))
                            ->width('150px'),
                        Header::make('quantity')
                            ->label(__('Quantity'))
                            ->width('150px'),
                        Header::make('initial_price')
                            ->label(__('Initial Price'))
                            ->width('150px'),
                        Header::make('selling_price')
                            ->label(__('Selling Price'))
                            ->width('150px'),
                        Header::make('total_initial_price')
                            ->label(__('Total Initial Price'))
                            ->width('150px'),
                        Header::make('total_selling_price')
                            ->label(__('Total Selling Price'))
                            ->width('150px'),
                    ])
                    ->schema([
                        Select::make('product_id')
                            ->native(false)
                            ->placeholder(__('Search...'))
                            ->options(Product::pluck('name', 'id'))
                            ->searchable()
                            ->live()
                            ->afterStateUpdated(function (Set $set, ?string $state) {
                                $product = Product::find($state);
                                $set('initial_price', $product->initial_price);
                                $set('selling_price', $product->selling_price);
                            }),
                        TextInput::make('stock')
                            ->afterStateUpdated(function (Set $set, Get $get, ?string $state) {
                                $product = Product::find($get('product_id'));
                                $set('total_initial_price', $product->initial_price * (float) $state);
                                $set('total_selling_price', $product->selling_price * (float) $state);
                            })
                            ->live(onBlur: true),
                        TextInput::make('initial_price')
                            ->mask(RawJs::make('$money($input)'))
                            ->lte('selling_price')
                            ->stripCharacters(',')
                            ->numeric()
                            ->required(),
                        TextInput::make('selling_price')
                            ->mask(RawJs::make('$money($input)'))
                            ->gte('initial_price')
                            ->stripCharacters(',')
                            ->numeric()
                            ->required(),
                        TextInput::make('total_selling_price')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->numeric()
                            ->readOnly(),
                        TextInput::make('total_initial_price')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->numeric()
                            ->readOnly(),
                    ])
                    ->columnSpan('full'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('supplier.name')
                    ->searchable(),
                TextColumn::make('number')
                    ->searchable(),
                TextColumn::make('date')
                    ->date(),
                TextColumn::make('item_amounts')->counts([
                    'stocks' => fn (Builder $builder) => $builder,
                ]),
            ])
            ->filters([
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListPurchasings::route('/'),
            'create' => Pages\CreatePurchasing::route('/create'),
            'edit' => Pages\EditPurchasing::route('/{record}/edit'),
        ];
    }
}
