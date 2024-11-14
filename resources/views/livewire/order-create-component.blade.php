<div class="w-full mx-auto bg-gray-50 p-6 rounded-lg shadow-md mt-8">
    <!-- Grid Principal de 2 Columnas -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Columna de Productos y Pagos (2/3) -->
        <div class="md:col-span-2 space-y-6">
            <!-- Sección de Productos -->
            <div class="bg-white p-6 rounded shadow w-full">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-gray-700 font-semibold">Products</h2>
                    <p class="text-sm text-gray-500">Items reserved until <span class="text-blue-600">today at 11:59
                            pm</span></p>
                    <x-filament::button color="primary" wire:click="addProduct">Add custom item</x-filament::button>
                </div>

                <!-- Listado de Productos Seleccionados -->
                @foreach ($products as $index => $product)
                <div class="flex items-center mb-4 bg-gray-100 p-4 rounded-lg">
                    @if ($product['product_id'])
                    @php
                    $selectedProduct = App\Models\Product::find($product['product_id']);
                    @endphp
                    <x-filament::avatar src="{{ $selectedProduct->images[0] ?? 'https://via.placeholder.com/100' }}"
                        alt="Product Image" size="lg" class="mr-4" />
                    @endif

                    <div class="flex-1">
                        <x-filament::input.wrapper>
                            <x-filament::input.select wire:model="products.{{ $index }}.product_id" wire:change="calculateTotal" searchable
                                placeholder="-- Search or select a product --" class="w-full">
                                @foreach ($availableProducts as $availableProduct)
                                <option value="{{ $availableProduct->id }}">
                                    {{ $availableProduct->product_name }} - ${{ number_format($availableProduct->price, 2) }}
                                </option>
                                @endforeach
                            </x-filament::input.select>
                        </x-filament::input.wrapper>

                        <p class="text-gray-500 mt-1">Collection Name</p>
                        <h3 class="text-lg font-semibold">{{ $selectedProduct->product_name ?? '' }}</h3>
                        <p class="text-sm text-gray-500">Medium Gray</p>
                    </div>

                    <div class="ml-auto text-right">
                        <x-filament::input type="number" wire:model="products.{{ $index }}.quantity" min="1"
                            class="w-20" wire:change="calculateTotal" placeholder="Qty" />
                        <p class="text-sm mt-2">{{ $product['quantity'] ?? 0 }} x ${{ $selectedProduct->price ?? 0 }}
                        </p>
                        <p class="text-lg font-semibold">
                            ${{ number_format(($selectedProduct->price ?? 0) * ($product['quantity'] ?? 0), 2) }}
                        </p>
                    </div>

                    <x-filament::button color="danger" icon="heroicon-o-trash" wire:click="removeProduct({{ $index }})"
                        class="ml-4">
                        Remove
                    </x-filament::button>
                </div>
                @endforeach
            </div>

            <!-- Sección de Pago -->
            <div class="bg-white p-6 rounded shadow w-full">
                <h2 class="text-lg font-semibold text-gray-700 mb-4">Payment</h2>
                <p class="text-sm text-gray-500 mb-4">Use this personalized guide to get your store up and running.</p>
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <p>Subtotal</p>
                        <p>{{ count($products) }} item(s)</p>
                        <p>${{ number_format($grand_total, 2) }}</p>
                    </div>
                    <div class="flex justify-between text-sm">
                        <p>Discount</p>
                        <p>New customer</p>
                        <p>-$1.00</p>
                    </div>
                    <div class="flex justify-between text-sm">
                        <p>Shipping</p>
                        <p>Free shipping (0.0 lb)</p>
                        <p>$0.00</p>
                    </div>
                    <div class="flex justify-between font-bold text-lg">
                        <p>Total</p>
                        <p>${{ number_format($grand_total - 1, 2) }}</p>
                    </div>
                    <div class="flex justify-between text-sm">
                        <p>Paid by customer</p>
                        <p>$0.00</p>
                        <x-filament::button size="sm" color="primary" class="text-sm">Edit</x-filament::button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Columna de Información del Cliente (1/3) -->
        <div class="space-y-6">
            <!-- Selección de Cliente -->
            <div class="bg-white p-6 rounded shadow">
                <h2 class="text-lg font-semibold text-gray-700 mb-4">Customers</h2>
                <x-filament::input.wrapper>
                    <x-filament::input.select wire:model="user_id" class="w-full">
                        <option value="">-- Select Customer --</option>
                        @foreach ($availableUsers as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </x-filament::input.select>
                </x-filament::input.wrapper>
                @error('user_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Información de Notas -->
            <div class="bg-white p-6 rounded shadow">
                <h2 class="text-lg font-semibold text-gray-700 mb-4">Notes</h2>
                <p class="text-sm text-gray-500">No notes</p>
            </div>

            <!-- Información de Contacto -->
            <div class="bg-white p-6 rounded shadow">
                <h2 class="text-lg font-semibold text-gray-700 mb-4">Contact Information</h2>
                <p class="text-sm text-gray-500">alexjander@gmail.com</p>
                <p class="text-sm text-gray-500">No orders</p>
                <p class="text-sm text-gray-500">No phone number</p>
            </div>

            <!-- Dirección de Envío -->
            <div class="bg-white p-6 rounded shadow">
                <h2 class="text-lg font-semibold text-gray-700 mb-4">Shipping address</h2>
                <p class="text-sm text-gray-500">Alex Jander</p>
                <p class="text-sm text-gray-500">1226 University Drive</p>
                <p class="text-sm text-gray-500">Menlo Park CA 94025</p>
                <p class="text-sm text-gray-500">United States</p>
                <button class="text-blue-600 text-sm">View Map</button>
            </div>
        </div>
    </div>
</div>
