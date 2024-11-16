<div>
    <h1>Product List</h1>

    <div class="filters">
        <!-- Input para buscar productos por nombre -->
        <input type="text" wire:model.debounce.300ms="searchTerm" placeholder="Search by product name..."
            class="form-control" style="margin-bottom: 10px;">

        <!-- Select para filtrar productos por categoría -->
        <select wire:model="category" class="form-control" style="margin-bottom: 10px;">
            <option value="">All Categories</option>
            @foreach($categories as $category)
            <option value="{{ $category->id }}">{{ $category->category_name }}</option>
            @endforeach
        </select>
    </div>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>Price</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $product)
            <tr>
                <td>{{ $product->product_name }}</td>
                <td>${{ $product->price }}</td>
                <td>{{ $product->description }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="3">No products found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{ $products->links() }}
</div>
