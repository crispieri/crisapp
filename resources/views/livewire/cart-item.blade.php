<div>
    <div class="flex items-center justify-between py-2 border-b">
        <span class="w-2/5 font-semibold">{{ $item['name'] }}</span>
        <div class="flex items-center space-x-2">
            <button wire:click="decrementQuantity" class="px-2 bg-gray-300 rounded">-</button>
            <span>{{ $item['quantity'] }}</span>
            <button wire:click="incrementQuantity" class="px-2 bg-gray-300 rounded">+</button>
        </div>
        <span class="font-semibold">${{ number_format($item['sub_total'], 2) }}</span>
        <button wire:click="removeItem" class="text-red-500">x</button>
    </div>
</div>
