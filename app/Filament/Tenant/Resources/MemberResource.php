<?php

namespace App\Filament\Tenant\Resources;

use App\Filament\Tenant\Resources\MemberResource\Pages;
use App\Models\Tenants\Member;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MemberResource extends Resource
{
    protected static ?string $model = Member::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('identity_type')
                    ->options([
                        'sim' => 'Sim',
                        'ktp' => 'Ktp',
                        'other' => __('Other'),
                    ]),
                TextInput::make('identity_number')
                    ->label(__('Identity Number'))
                    ->required(),
                TextInput::make('name')
                    ->label(__('Name'))
                    ->required(),
                TextInput::make('code')
                    ->label(__('Code')),
                TextInput::make('address')
                    ->label(__('Address')),
                TextInput::make('email')
                    ->label(__('Contact'))
                    ->placeholder(__('Please provide a valid email address or whatsapp/phone number.'))
                    ->email(),
                DatePicker::make('joined_date'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('code')
                    ->label(__('Code'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('address')
                    ->label(__('Address'))
                    ->searchable(),
                TextColumn::make('email')
                    ->label(__('Contact'))
                    ->searchable(),

                TextColumn::make('identity_number')
                    ->label(__('Identity Number'))
                    ->searchable()
                    ->sortable(),

            ])
            ->filters([
                //
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
            'index' => Pages\ListMembers::route('/'),
            'create' => Pages\CreateMember::route('/create'),
            'edit' => Pages\EditMember::route('/{record}/edit'),
        ];
    }
}
