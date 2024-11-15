<div>
    <select wire:model="selectedCategory" class="w-full p-2 border rounded">
        <option value="">Todas las Categorías</option>
        @foreach ($categories as $category)
        <option value="{{ $category->id }}">{{ $category->category_name }}</option>
        @endforeach
    </select>
</div>
