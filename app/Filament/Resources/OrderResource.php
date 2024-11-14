<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Order;
use App\Models\Coupon;
use App\Models\Product;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Support\RawJs;
use App\Enums\OrderStatusEnum;
use Filament\Resources\Resource;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\OrderResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\OrderResource\RelationManagers;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

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
                    Forms\Components\Section::make(__('order.sectionProduct'))->schema([
                        \Awcodes\TableRepeater\Components\TableRepeater::make('orderItem')
                            ->label('')
                            ->relationship()
                            ->headers([
                                \Awcodes\TableRepeater\Header::make('Nombre')->width('300px'),
                                \Awcodes\TableRepeater\Header::make('Cantidad'),
                                \Awcodes\TableRepeater\Header::make('Precio por Unidad'),
                                \Awcodes\TableRepeater\Header::make('Sub Total'),
                            ])
                            ->schema([
                                Forms\Components\Select::make('product_id')
                                    ->relationship('product', 'product_name')
                                    ->required()
                                    ->distinct()
                                    ->searchable()
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
                                    ->numeric()
                                    ->dehydrated()
                                    ->readOnly(),

                            ])->afterStateUpdated(function ($state, callable $set, callable $get) {
                                // Recalcula el subtotal cada vez que se actualiza un elemento
                                $total = array_reduce(
                                    $get('orderItem') ?? [],
                                    fn($carry, $item) => $carry + ($item['sub_total'] ?? 0),
                                    0
                                );
                                $set('grand_total', $total);
                            })
                            ->defaultItems(1)
                            ->columnSpan('full'),
                        Forms\Components\Select::make('coupon_id')
                            ->label(__('order.coupon'))
                            ->relationship('coupon', 'code')
                            ->searchable()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $coupon = Coupon::find($state);

                                // Si no hay cupón, restablece el descuento
                                if (!$coupon) {
                                    $set('discount_amount', 0);
                                    $set('total_after_discount', $get('sub_total'));
                                    Notification::make()
                                        ->title(__('Coupon removed.'))
                                        ->send();
                                    return;
                                }

                                // Si el cupón no es válido, reinicia los valores
                                if (!$coupon->isValid()) {
                                    $set('discount_amount', 0);
                                    $set('total_after_discount', $get('sub_total'));
                                    $set('coupon_id', null);
                                    Notification::make()
                                        ->title(__('The selected coupon is not valid.'))
                                        ->danger()
                                        ->send();
                                    return;
                                }

                                // Calcula y aplica el descuento
                                $subTotal = $get('sub_total') ?? 0;
                                $discount = $coupon->calculateDiscount($subTotal);
                                $set('discount_amount', $discount);
                                $set('total_after_discount', $subTotal - $discount);
                            }),

                        Forms\Components\Placeholder::make('sub_total')
                            ->label(__('order.sub_total'))
                            ->content(fn(Get $get) => number_format($get('sub_total') ?? 0, 0, ',', '.')),

                        Forms\Components\Placeholder::make('discount_amount')
                            ->label(__('order.discount_amount'))
                            ->content(fn(Get $get) => number_format($get('discount_amount') ?? 0, 0, ',', '.')),

                        Forms\Components\Placeholder::make('total_after_discount')
                            ->label(__('order.total_after_discount'))
                            ->content(fn(Get $get) => number_format($get('total_after_discount') ?? 0, 0, ',', '.')),
                    ]),
                ])->columnSpan(2),

                //Grupo 2
                Forms\Components\Group::make()->schema([
                    Forms\Components\Section::make(__('order.sectionCustomer'))->schema([
                        // Campo para seleccionar el usuario
                        Forms\Components\Select::make('user_id')
                            ->label(__('order.user_id'))
                            ->relationship('user', 'name')
                            ->required()
                            ->searchable()
                            ->reactive() // Hace que este campo sea reactivo
                            ->afterStateUpdated(fn($state, callable $set) => $set('address_id', null))
                            ->columnSpanFull(), // Resetea address_id al cambiar user_id

                        // Campo para seleccionar la dirección filtrada por el usuario seleccionado
                        Forms\Components\Select::make('address_id')
                            // ->label(__('order.address'))
                            ->options(function (callable $get) {
                                $userId = $get('user_id'); // Obtiene el id del usuario seleccionado

                                if ($userId) {
                                    return \App\Models\Address::where('user_id', $userId)
                                        ->pluck('street_address', 'id'); // Carga solo las direcciones del usuario seleccionado
                                }

                                return []; // Devuelve un array vacío si no hay usuario seleccionado
                            })
                            ->placeholder('Selecione')
                            ->native(false)
                            ->disabled(fn(callable $get) => !$get('user_id'))
                            ->columnSpanFull(), // Deshabilita si no se ha seleccionado un usuario

                        Forms\Components\Select::make('status')
                            ->label(__('order.status'))
                            ->options(OrderStatusEnum::class)
                            ->default(OrderStatusEnum::NEW)
                            ->required(),
                    ])->columns(2),

                    Forms\Components\Section::make(__('order.sectionTotal'))->schema([
                        Forms\Components\MarkdownEditor::make('notes')
                            ->label(__('order.notes')),
                    ])->columns(1),

                ])->columnSpan(1)
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
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

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
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

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $this->processCouponAndTotals($data);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $this->processCouponAndTotals($data);
    }

    private function processCouponAndTotals(array $data): array
    {
        // Calcula subtotal sumando subtotales de items
        $subTotal = array_reduce($data['orderItem'] ?? [], fn($carry, $item) => $carry + ($item['sub_total'] ?? 0), 0);
        $data['sub_total'] = $subTotal;

        // Revisa si el cupón es válido
        $coupon = !empty($data['coupon_id']) ? Coupon::find($data['coupon_id']) : null;

        if ($coupon && $coupon->isValid()) {
            $data['discount_amount'] = $coupon->calculateDiscount($subTotal);
            $data['total_after_discount'] = $subTotal - $data['discount_amount'];
        } else {
            $data['coupon_id'] = null;
            $data['discount_amount'] = 0;
            $data['total_after_discount'] = $subTotal;
        }

        return $data;
    }
}
