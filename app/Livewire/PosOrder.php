<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;
use App\Models\Category;

class PosOrder extends Component
{
    use WithPagination;

    public $searchTerm = '';
    public $category = '';

    // Actualizar los métodos de filtrado para resetear la paginación al cambiar de filtro
    public function updatingSearchTerm()
    {
        $this->resetPage();
    }

    public function updatingCategory()
    {
        $this->resetPage();
    }

    public function render()
    {
        $categories = Category::all();
        $products = Product::where('is_active', true)
            ->when($this->searchTerm, function ($query) {
                $query->where('product_name', 'like', '%' . $this->searchTerm . '%');
            })
            ->when($this->category, function ($query) {
                $query->where('category_id', $this->category);
            })
            ->paginate(12);

        return view('livewire.pos-order', [
            'products' => $products,
            'categories' => $categories,
        ]);
    }
}
