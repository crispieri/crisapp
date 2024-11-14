<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Models\Store;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserResource\RelationManagers;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getModelLabel(): string
    {
        return __('user.modelLabel');
    }

    public static function getPluralModelLabel(): string
    {
        return __('user.pluralModelLabel');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make([
                    Forms\Components\Section::make([
                        Forms\Components\TextInput::make('name')
                            ->label(__('user.name'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label(__('user.email'))
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone_number')
                            ->tel(),
                        Forms\Components\DateTimePicker::make('email_verified_at')
                            ->label(__('user.email_verified_at')),
                        Forms\Components\TextInput::make('password')
                            ->label(__('user.password'))
                            ->password()
                            ->dehydrateStateUsing(fn(string $state): string => Hash::make($state))
                            ->dehydrated(fn(?string $state): bool => filled($state))
                            ->required(fn(string $operation): bool => $operation === 'create')
                            ->revealable(),
                        Forms\Components\Select::make('stores')->multiple() // Permitir seleccionar múltiples tiendas si es necesario
                            ->relationship('stores', 'name')
                            ->preload()
                            ->searchable()
                            ->options(Store::all()->pluck('name', 'id')),
                    ])->columns(2),

                    Forms\Components\Section::make([
                        Forms\Components\Repeater::make('addresses')
                            ->label(__('user.addressSection'))
                            ->relationship()
                            ->schema([
                                Forms\Components\TextInput::make('street_address')
                                    ->label(__('user.addressSection'))
                                    ->required()
                                    ->label('Street Address')
                                    ->columnSpanFull(),

                                Forms\Components\TextInput::make('commune')
                                    ->label(__('user.commune'))
                                    ->label('Commune')
                                    ->required(),

                                Forms\Components\TextInput::make('city')
                                    ->label(__('user.city'))
                                    ->label('City')
                                    ->required(),

                                Forms\Components\TextInput::make('region')
                                    ->label(__('user.region'))
                                    ->label('Region')
                                    ->required(),
                            ])->columns(3)->columnSpanFull()
                    ])->columns(2)
                ])->columnSpan(2),

                Forms\Components\Group::make([
                    Forms\Components\Section::make([
                        Forms\Components\Toggle::make('is_active')
                            ->label(__('user.is_active'))
                            ->required(),


                    ])->columns(2)
                ])->columnSpan(1)

            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('user.name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label(__('user.email'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('stores')
                    ->label(__('user.store'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->label(__('user.email_verified_at'))
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
