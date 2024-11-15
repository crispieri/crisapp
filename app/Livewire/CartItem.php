<?php

namespace App\Livewire;

use Livewire\Component;

class CartItem extends Component
{
    public $item;
    public $index;

    public function incrementQuantity()
    {
        $this->emitUp('updateQuantity', $this->index, $this->item['quantity'] + 1);
    }

    public function decrementQuantity()
    {
        if ($this->item['quantity'] > 1) {
            $this->emitUp('updateQuantity', $this->index, $this->item['quantity'] - 1);
        }
    }

    public function removeItem()
    {
        $this->emitUp('removeFromCart', $this->index);
    }

    public function render()
    {
        return view('livewire.cart-item');
    }
}
