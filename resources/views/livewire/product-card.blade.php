<div>
    <div class="flex flex-col justify-between p-4 border rounded-lg shadow">
        <h3 class="text-lg font-bold">{{ $product->product_name }}</h3>
        <p class="text-sm text-gray-500">{{ $product->description }}</p>
        <div class="flex items-center justify-between mt-2">
            <span class="text-xl font-semibold text-yellow-500">${{ number_format($product->price, 2) }}</span>
            <button wire:click="$emitUp('addProductToCart', {{ $product->id }})"
                class="px-3 py-1 text-white bg-green-500 rounded">
                Agregar
            </button>
        </div>
    </div>
</div>
