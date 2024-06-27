@extends('layouts.app')

@section('content')
    <div class="py-8 px-4 lg:px-8">
        <h1 class="text-2xl font-semibold text-gray-900 mb-6">Order Confirmation</h1>

        <div class="bg-white shadow overflow-hidden rounded-lg mb-6">
            <div class="px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Order Summary</h2>
                <div class="flex justify-between border-b border-gray-200 pb-4 grid grid-cols-3 gap-4">
                    <h2 class="text-md font-semibold text-gray-900 col-span-1">Book Name</h2>
                    <h2 class="text-md font-semibold text-gray-900 col-span-1 text-right">Quantity</h2>
                    <h2 class="text-md font-semibold text-gray-900 col-span-1 text-right">Item Total Price</h2>
                </div>
                <div class="mt-4">
                    <div class="mt-4 grid grid-cols-3 gap-4">
                        @foreach ($cartItems as $cartItem)
                            <div class="col-span-1">{{$cartItem->book->title}}</div>
                            <div class="col-span-1 text-right">{{$cartItem->quantity}}</div>
                            <div class="col-span-1 text-right">${{$cartItem->item_total_price}}</div>
                        @endforeach
                    </div>
                    <div class="flex justify-between mt-4 pt-4 border-t border-gray-200">
                        <span class="font-semibold text-lg">Total</span>
                        <span></span>
                        <span class="font-semibold text-lg">${{$total}}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white shadow overflow-hidden rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Shipping Address</h3>
                <dl class="mt-5 grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-3">
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Street</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{$address->street}}</dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">City</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{$address->city}}</dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Region</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{$address->region}}</dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Country</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{$address->country}}</dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">ZIP</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{$address->zip}}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <div class="bg-white shadow overflow-hidden rounded-lg">
            <div class="px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Payment Method</h2>
                <div class="flex items-center">
                    <input id="paymentCard" name="paymentType" type="radio" class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300" checked>
                    <label for="paymentCard" class="ml-3 flex items-center">
                        <span class="block text-sm font-medium text-gray-700">Card</span>
                    </label>
                </div>
                <div class="flex items-center mt-4">
                    <input id="paymentCOD" name="paymentType" type="radio" class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300">
                    <label for="paymentCOD" class="ml-3 flex items-center">
                        <span class="block text-sm font-medium text-gray-700">Cash on Delivery</span>
                    </label>
                </div>
            </div>
        </div>

        <form id="orderForm" action="{{ route('payment.confirmOrder') }}" method="POST">
            @csrf
            <div class="mt-2" id="addressForm">
                <input type="hidden" id="addressId" name="addressId">
                <input type="hidden" id="paymentMethod" name="paymentMethod">
            </div>
            <div class="mt-4">
                <button type="button" onclick="history.back();" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2">Back</button>
                <button type="submit" id="confirmOrderButton" class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2">Confirm Order</button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
          document.querySelectorAll('input[name="paymentType"]').forEach((input) => {
            input.addEventListener('change', function() {
                const selectedPaymentMethod = document.querySelector('input[name="paymentType"]:checked').id;
                document.getElementById('paymentMethod').value = selectedPaymentMethod === 'paymentCard' ? 'Card' : 'CashOnDelivery';
            });
          });
        });
    </script>
@endsection
