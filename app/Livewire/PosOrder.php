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
    protected $queryString = ['searchTerm', 'category'];

    public function updating($property)
    {
        $this->resetPage();
    }

    public function getFilteredProductsProperty()
    {
        return Product::where('is_active', true)
            ->when(
                $this->searchTerm,
                fn($query) =>
                $query->where('product_name', 'like', '%' . $this->searchTerm . '%')
            )
            ->when(
                $this->category,
                fn($query) =>
                $query->where('category_id', $this->category)
            )
            ->paginate(12);
    }

    public function render()
    {
        return view('livewire.pos-order', [
            'categories' => Category::all(),
            'products' => $this->filteredProducts,
        ]);
    }
}
