@extends('layouts.app')

@section('content')
  <h1 class="mb-10 text-2xl">Order Confirmation</h1>
  <table class="table-auto w-full">
    <thead>
        <tr>
            <th class="px-4 py-2">Book Name</th>
            <th class="px-4 py-2">Quantity</th>
            <th class="px-4 py-2">Item Total Price</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($cartItems as $cartItem)
            <tr>
                <td class="border px-4 py-2">{{ $cartItem->book->title }}</td>
                <td class="border px-4 py-2">{{ $cartItem->quantity }}</td>
                <td class="border px-4 py-2">${{ $cartItem->item_total_price }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

  <div class="mt-10 book-title">Total Items: <span class="ml-2 text-green-700">{{ count($cartItems) }}<span></div>
  <div class="book-title">Total Amount: <span class="ml-2 text-green-700">${{ $total }}<span></div>
  <div class="book-title">Payment Method: <span class="ml-2 text-green-700">Stripe<span></div>

    <form action="{{ route('payment.checkout') }}" method="POST" class="mt-5">
        @csrf
        <a href="{{ url()->previous() }}" class="button-style text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Back</a>
        <button type="submit" class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Confirm Order</button>
    </form>
    
@endsection
