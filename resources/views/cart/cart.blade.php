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
                                            <th class="hidden py-2 px-4 border-b md:table-cell">Item unit Price</th>
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
                                                        <button type="submit" class="p-1 rounded-md hover:bg-red-600">
                                                            <svg class="w-6 h-6 fill-current text-red-500" viewBox="0 0 448 512" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M135.2 17.7C140.6 6.8 151.7 0 163.8 0H284.2c12.1 0 23.2 6.8 28.6 17.7L320 32h96c17.7 0 32 14.3 32 32s-14.3 32-32 32H32C14.3 96 0 81.7 0 64S14.3 32 32 32h96l7.2-14.3zM32 128H416V448c0 35.3-28.7 64-64 64H96c-35.3 0-64-28.7-64-64V128zm96 64c-8.8 0-16 7.2-16 16V432c0 8.8 7.2 16 16 16s16-7.2 16-16V208c0-8.8-7.2-16-16-16zm96 0c-8.8 0-16 7.2-16 16V432c0 8.8 7.2 16 16 16s16-7.2 16-16V208c0-8.8-7.2-16-16-16zm96 0c-8.8 0-16 7.2-16 16V432c0 8.8 7.2 16 16 16s16-7.2 16-16V208c0-8.8-7.2-16-16-16z"></path>
                                                            </svg>                                                       
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-6 flex justify-end items-center">
                                <div class="text-lg font-bold">Total: $<span id="totalPrice">{{ Cart::getTotal() }}<span></div>
                                @if (!$isAnyBookOutOfStock)
                                    <a href="{{ route('cart.checkout') }}" class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 ml-5 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Checkout</a>
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

@section('footerScripts')
    <script src="{{ asset('js/cart.js') }}"></script>
@endsection
