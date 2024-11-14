<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use Livewire\Component;
use App\Enums\OrderStatusEnum;
use Illuminate\Support\Collection; // Asegúrate de importar esta clase

class OrderCreateComponent extends Component
{
    public $user_id;
    public $status;
    public $grand_total = 0;
    public $notes;
    public $products = [];
    public $searchTerm = '';
    public Collection $availableProducts; // Para almacenar los productos disponibles basados en la búsqueda

    protected $rules = [
        'user_id' => 'required|exists:users,id',
        'products.*.product_id' => 'required|exists:products,id',
        'products.*.quantity' => 'required|integer|min:1',
    ];

    public function mount()
    {
        $this->products = [['product_id' => null, 'quantity' => 1]];
        $this->availableProducts = collect();
    }

    public function updatedSearchTerm()
    {
        // Actualiza los productos disponibles según el término de búsqueda
        $this->availableProducts = Product::where('product_name', 'like', '%' . $this->searchTerm . '%')->get();
    }

    public function addProduct()
    {
        $this->products[] = ['product_id' => null, 'quantity' => 1];
    }

    public function removeProduct($index)
    {
        unset($this->products[$index]);
        $this->products = array_values($this->products);
        $this->calculateTotal();
    }

    public function calculateTotal()
    {
        $this->grand_total = 0;
        foreach ($this->products as $product) {
            $productModel = Product::find($product['product_id']);
            if ($productModel) {
                $this->grand_total += $productModel->price * $product['quantity'];
            }
        }
    }

    public function submit()
    {
        $this->validate();

        $order = Order::create([
            'user_id' => $this->user_id,
            'status' => $this->status,
            'grand_total' => $this->grand_total,
            'notes' => $this->notes,
        ]);

        // Guardar los productos de la orden
        foreach ($this->products as $product) {
            $order->orderItems()->create([
                'product_id' => $product['product_id'],
                'quantity' => $product['quantity'],
                'unit_amount' => Product::find($product['product_id'])->price,
                'sub_total' => Product::find($product['product_id'])->price * $product['quantity'],
            ]);
        }

        session()->flash('message', 'Order successfully created.');
        $this->reset(['user_id', 'status', 'grand_total', 'notes', 'products']);
        $this->mount(); // Resetear productos y carga inicial
    }

    public function render()
    {
        $availableUsers = User::all();
        return view('livewire.order-create-component', compact('availableUsers'));
    }
}
