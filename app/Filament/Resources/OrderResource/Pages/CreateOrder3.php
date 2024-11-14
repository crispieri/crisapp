<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Illuminate\Support\Collection;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use Filament\Forms;
use Filament\Resources\Pages\Page;

class CreateOrder3 extends Page implements Forms\Contracts\HasForms
{
    protected static string $resource = OrderResource::class;

    protected static string $view = 'filament.resources.order-resource.pages.create-order';

    use Forms\Concerns\InteractsWithForms;

    protected static ?string $title = 'Create Order';

    // Variables de la página
    public ?Collection $items = null;
    public $user_id;
    public $notes;
    public $grand_total = 0;

    // protected static string $view = 'filament.pages.create-order';

    public function mount(): void
    {
        $this->form->fill();
        $this->items = collect(); // Inicializa la colección de items de la orden como Collection
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Select::make('user_id')
                ->label('User')
                ->relationship('user', 'name')
                ->required(),

            // Forms\Components\Textarea::make('notes')
            //     ->label('Notes'),

            // Forms\Components\Repeater::make('items')
            //     ->label('Order Items')
            //     ->schema([
            //         Forms\Components\Select::make('product_id')
            //             ->label('Product')
            //             ->options(Product::where('in_stock', true)->pluck('product_name', 'id'))
            //             ->reactive()
            //             ->required()
            //             ->afterStateUpdated(fn($state, $set) => $set('unit_amount', Product::find($state)?->price)),

            //         Forms\Components\TextInput::make('quantity')
            //             ->label('Quantity')
            //             ->default(1)
            //             ->reactive()
            //             ->numeric()
            //             ->minValue(1)
            //             ->required()
            //             ->afterStateUpdated(fn($state, $get, $set) => $set('sub_total', $state * $get('unit_amount'))),

            //         Forms\Components\TextInput::make('unit_amount')
            //             ->label('Unit Price')
            //             ->numeric()
            //             ->prefix('$')
            //             ->disabled(),

            //         Forms\Components\TextInput::make('sub_total')
            //             ->label('Sub Total')
            //             ->numeric()
            //             ->prefix('$')
            //             ->disabled(),
            //     ])
            //     ->createItemButtonLabel('Add Item')
            //     ->collapsible()
            //     ->afterStateUpdated(function ($state, $set) {
            //         $set('items', collect($state)); // Convierte el estado en Collection y lo asigna a `items`
            //         $set('grand_total', collect($state)->sum('sub_total'));
            //     }),

            // Forms\Components\TextInput::make('grand_total')
            //     ->label('Total')
            //     ->numeric()
            //     ->prefix('$')
            //     ->disabled(),
        ];
    }

    public function createOrder()
    {
        $order = Order::create([
            'user_id' => $this->user_id,
            'notes' => $this->notes,
            'grand_total' => $this->grand_total,
            'status' => 'pending', // Ejemplo de estado inicial
        ]);

        foreach ($this->items as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_amount' => $item['unit_amount'],
                'sub_total' => $item['sub_total'],
            ]);
        }

        // Redirige o muestra una notificación de éxito
        $this->notify('success', 'Order created successfully');
        return redirect()->route('filament.pages.create-order');
    }

    public static function calculateGrandTotal($items)
    {
        $items = collect($items); // Asegura que $items sea una colección
        return $items->sum('sub_total');
    }
}
