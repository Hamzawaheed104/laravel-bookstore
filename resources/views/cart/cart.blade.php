@extends('layouts.app')

@section('content')
    <main class="my-8">
        <div class="container mx-auto">
            <div class="flex justify-center my-6">
                <div class="w-full bg-white rounded-lg shadow-lg">
                    <div class="p-6">
                        <h3 class="text-3xl font-bold mb-4">Cart</h3>

                        @if(count($cartItems) == 0)
                            <p class="text-center text-lg">No items found</p>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full border border-gray-300">
                                    <thead>
                                        <tr class="bg-gray-100">
                                            <th class="py-2 px-4 border-b">Book Title</th>
                                            <th class="py-2 px-4 border-b lg:pl-5">Quantity</th>
                                            <th class="hidden py-2 px-4 border-b md:table-cell">Price</th>
                                            <th class="hidden py-2 px-4 border-b md:table-cell">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $isAnyBookOutOfStock = false;
                                        @endphp

                                        @foreach ($cartItems as $item)
                                            <tr class="border-b">
                                                <td class="py-2 px-4">
                                                    <a href="{{ route('books.show', $item->get('book')) }}" class="text-blue-500 hover:underline">
                                                        {{ $item->name }}
                                                    </a>
                                                    @if($item->get('book')->stock < $item->quantity)
                                                        @php
                                                            $isAnyBookOutOfStock = true;
                                                        @endphp
                                                        <span class="bg-red-500 text-white font-bold py-1 px-2 rounded-md">
                                                            Out of Stock
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="py-2 px-4 text-center">
                                                    <div class="flex items-center justify-center space-x-2">
                                                        <button type="button" class="update-quantity-btn bg-red-500 text-white rounded-full h-8 w-8 flex items-center justify-center" data-id="{{ $item->id }}" data-quantity="{{ $item->quantity - 1 }}">
                                                            &ndash;
                                                        </button>
                                                        <span class="mx-2 text-lg">{{ $item->quantity }}</span>
                                                        <button type="button" class="update-quantity-btn bg-green-500 text-white rounded-full h-8 w-8 flex items-center justify-center" data-id="{{ $item->id }}" data-quantity="{{ $item->quantity + 1 }}">
                                                            &#43;
                                                        </button>
                                                    </div>
                                                </td>                                                
                                                <td class="hidden py-2 px-4 text-center md:table-cell">
                                                    ${{ $item->price }}
                                                </td>
                                                <td class="hidden py-2 px-4 text-center md:table-cell">
                                                    <form action="{{ route('cart.remove') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" value="{{ $item->id }}" name="id">
                                                        <button class="px-4 py-2 text-white bg-red-600 rounded-md">Remove</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-6 flex justify-end items-center">
                                <div class="text-lg font-bold">Total: ${{ Cart::getTotal() }}</div>
                                @if (!$isAnyBookOutOfStock)
                                    <a href="{{ route('payment.shipping') }}" class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 ml-5 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Checkout</a>
                                @else
                                    <button class="focus:outline-none text-white bg-gray-500 cursor-not-allowed rounded-lg text-sm px-5 py-2.5 me-2 ml-5" disabled>Checkout (Out of Stock)</button>
                                @endif

                                <div class="ml-2">
                                    <form action="{{ route('cart.clear') }}" method="POST">
                                        @csrf
                                        <button class="px-6 py-2 text-white bg-red-600 rounded-md">Clear Cart</button>
                                    </form>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.update-quantity-btn').forEach(button => {
            button.addEventListener('click', function() {
                const itemId = this.getAttribute('data-id');
                const newQuantity = this.getAttribute('data-quantity');
                
                fetch("{{ route('cart.update') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ id: itemId, quantity: newQuantity })
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        window.location.reload();
                    } else {
                        debugger
                        const existingErrorMessage = document.querySelector('.error-message');
                        if (existingErrorMessage) {
                            existingErrorMessage.remove();
                        }
                        
                        const errorMessage = document.createElement('div');
                        errorMessage.classList.add('p-4', 'bg-red-500', 'text-white', 'mb-4', 'rounded-md');
                        errorMessage.textContent = data.message;
                        
                        const cartContainer = document.querySelector('.container');
                        cartContainer.insertBefore(errorMessage, cartContainer.firstChild);
                    }
                });
            });
        });
    });
    </script>
@endsection
