<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Order;
use App\Models\Product;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Enums\OrderStatusEnum;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\OrderResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\OrderResource\RelationManagers;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getModelLabel(): string
    {
        return __('order.modelLabel');
    }

    public static function getPluralModelLabel(): string
    {
        return __('order.pluralModelLabel');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()->schema([
                    Forms\Components\Section::make(__('order.section'))->schema([

                        Forms\Components\Select::make('user_id')
                            ->label(__('order.user_id'))
                            ->relationship('user', 'name'),
                        Forms\Components\Select::make('status')
                            ->label(__('order.status'))
                            ->options(OrderStatusEnum::class)
                            ->default(OrderStatusEnum::New)
                            ->required(),
                        Forms\Components\MarkdownEditor::make('notes')
                            ->label(__('order.notes'))
                            ->columnSpanFull(),
                    ])->columns(2),
                    Forms\Components\Section::make(__('order.section'))->schema([
                        \Awcodes\TableRepeater\Components\TableRepeater::make('items')
                            ->relationship()
                            ->headers([
                                \Awcodes\TableRepeater\Header::make('Nombre'),
                                \Awcodes\TableRepeater\Header::make('Cantidad'),
                                \Awcodes\TableRepeater\Header::make('Unidad'),
                                \Awcodes\TableRepeater\Header::make('Sub Total'),
                            ])
                            ->schema([
                                Forms\Components\Select::make('product_id')
                                    ->relationship('product', 'product_name')
                                    ->required()
                                    ->distinct()
                                    ->columnSpanFull()
                                    ->reactive()
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                    ->afterStateUpdated(fn($state, Set $set) => $set('unit_amount', Product::find($state)?->price ?? 0))
                                    ->afterStateUpdated(fn($state, Set $set) => $set('sub_total', Product::find($state)?->price ?? 0)),
                                Forms\Components\TextInput::make('quantity')
                                    ->reactive()
                                    ->default(1)
                                    ->minValue(1)
                                    ->afterStateUpdated(fn($state, Set $set, Get $get) => $set('sub_total', $state * $get('unit_amount'))),
                                Forms\Components\TextInput::make('unit_amount')
                                    ->numeric()
                                    ->readOnly(),
                                Forms\Components\TextInput::make('sub_total')
                                    ->dehydrated()
                                    ->readOnly(),
                            ])
                            ->columnSpan('full')
                    ])
                ])->columnSpan(2),
                // Forms\Components\TextInput::make('grand_total')
                //     ->label(__('order.grand_total'))
                //     ->numeric(),
                Forms\Components\Placeholder::make('grand_total_placeholder')
                    ->label(__('order.grand_total'))
                    ->content(function (Get $get, Set $set) {
                        $total = 0;
                        if (!$repeaters = $get('items')) {
                            return $total;
                        }

                        foreach ($repeaters as $key => $repeater) {
                            $total += $get("items.{$key}.sub_total");
                        }
                        $set('grand_total', $total);
                        return number_format($total, 0, ',', '.');
                    }),
                Forms\Components\Hidden::make('grand_total')->default(0)
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('grand_total')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
