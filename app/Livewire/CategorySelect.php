<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Category;

class CategorySelect extends Component
{
    public $categories;
    public $selectedCategory;

    public function mount()
    {
        $this->categories = Category::all();
    }

    public function updatedSelectedCategory($value)
    {
        $this->emitUp('filterByCategory', $value); // Emitir el cambio de categoría al componente principal
    }

    public function render()
    {
        return view('livewire.category-select');
    }
}
