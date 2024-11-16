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
use App\Models\OrderItem;
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

    // Función para calcular datos antes de crear una orden
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $this->processOrderCalculations($data);
    }

    // Función para calcular datos antes de guardar una orden
    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $this->processOrderCalculations($data);
    }

    // Función para procesar los cálculos de la orden
    private function processOrderCalculations(array $data): array
    {
        // Calcula el subtotal sumando los subtotales de los items
        $data['sub_total'] = $this->calculateSubTotal($data['orderItem'] ?? []);

        // Obtén el cupón válido si está presente
        $coupon = $this->getValidCoupon($data['coupon_id'] ?? null);

        if ($coupon) {
            // Aplica el descuento
            $data['discount_amount'] = $coupon->calculateDiscount($data['sub_total']);
            $data['total_after_discount'] = $data['sub_total'] - $data['discount_amount'];
        } else {
            $data['coupon_id'] = null;
            $data['discount_amount'] = 0;
            $data['total_after_discount'] = $data['sub_total'];
        }

        return $data;
    }

    // Función para calcular el subtotal de los OrderItems
    private function calculateSubTotal(array $orderItems): float
    {
        return array_reduce($orderItems, function ($carry, $item) {
            $orderItem = new OrderItem($item); // Crea una instancia temporal del modelo
            return $carry + $orderItem->calculateSubTotal(); // Usa el método del modelo
        }, 0);
    }

    // Función para obtener un cupón válido
    private function getValidCoupon(?int $couponId): ?Coupon
    {
        if (!$couponId) {
            return null;
        }

        $coupon = Coupon::find($couponId);

        return $coupon && $coupon->isValid() ? $coupon : null;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()->schema([
                    Forms\Components\Section::make()->schema([
                        Forms\Components\Select::make('user_id')
                            ->label(__('order.user_id'))
                            ->relationship('user', 'name')
                            ->required()
                            ->searchable()
                            ->reactive()
                            ->afterStateUpdated(fn($state, callable $set) => $set('address_id', null)),

                        Forms\Components\Select::make('address_id')
                            ->options(function (callable $get) {
                                $userId = $get('user_id');
                                return $userId ? \App\Models\Address::where('user_id', $userId)->pluck('street_address', 'id') : [];
                            })
                            ->placeholder('Seleccione')
                            ->disabled(fn(callable $get) => !$get('user_id')),
                    ])->columns(3),
                ])->columnSpan(2),
                Forms\Components\Section::make()->schema([
                    \Awcodes\TableRepeater\Components\TableRepeater::make('orderItem')
                        ->label('')
                        ->relationship()
                        ->headers([
                            \Awcodes\TableRepeater\Header::make('Nombre')->width('400px'),
                            \Awcodes\TableRepeater\Header::make('Cantidad'),
                            \Awcodes\TableRepeater\Header::make('Precio por Unidad'),
                            \Awcodes\TableRepeater\Header::make('Precio Oferta'),
                            \Awcodes\TableRepeater\Header::make('Sub Total'),
                        ])
                        ->schema([
                            Forms\Components\Select::make('product_id')
                                ->relationship('product', 'product_name')
                                ->required()
                                ->reactive()
                                ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                ->afterStateUpdated(function ($state, Set $set) {
                                    $product = Product::find($state);
                                    $set('unit_amount', $product->price ?? 0);
                                    $set('offer_price', $product->offer_price ?? 0);
                                    $set('sub_total', $product->offer_price ?? $product->price ?? 0);
                                }),

                            Forms\Components\TextInput::make('quantity')
                                ->reactive()
                                ->default(1)
                                ->minValue(1)
                                ->afterStateUpdated(fn($state, Set $set, Get $get) => $set('sub_total', $state * $get('unit_amount'))),

                            Forms\Components\TextInput::make('unit_amount')
                                ->numeric()
                                ->readOnly(),

                            Forms\Components\TextInput::make('offer_price')
                                ->numeric()
                                ->readOnly(),

                            Forms\Components\TextInput::make('sub_total')
                                ->numeric()
                                ->readOnly(),
                        ])
                        ->afterStateUpdated(function ($state, callable $set, callable $get) {
                            $subTotal = array_reduce(
                                $get('orderItem') ?? [],
                                fn($carry, $item) => $carry + ($item['sub_total'] ?? 0),
                                0
                            );
                            $set('sub_total', $subTotal);
                            $discountAmount = $get('discount_amount') ?? 0;
                            $set('total_after_discount', $subTotal - $discountAmount);
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
                            $subTotal = $get('sub_total') ?? 0;

                            if (!$coupon) {
                                $set('discount_amount', 0);
                                $set('total_after_discount', $subTotal);
                                return;
                            }

                            if (!$coupon->isValid()) {
                                $set('discount_amount', 0);
                                $set('total_after_discount', $subTotal);
                                $set('coupon_id', null);
                                Notification::make()
                                    ->title(__('The selected coupon is not valid.'))
                                    ->danger()
                                    ->send();
                                return;
                            }

                            $discount = $coupon->calculateDiscount($subTotal);
                            $set('discount_amount', $discount);
                            $set('total_after_discount', $subTotal - $discount);
                        }),

                    Forms\Components\Placeholder::make('sub_total')
                        ->label(__('order.sub_total'))
                        ->content(fn(Get $get) => number_format($get('sub_total') ?? 0, 2)),

                    Forms\Components\Placeholder::make('discount_amount')
                        ->label(__('order.discount_amount'))
                        ->content(fn(Get $get) => number_format($get('discount_amount') ?? 0, 2)),

                    Forms\Components\Placeholder::make('total_after_discount')
                        ->label(__('order.total_after_discount'))
                        ->content(fn(Get $get) => number_format($get('total_after_discount') ?? 0, 2)),
                ])->columns(4)
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
}
