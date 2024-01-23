@extends('layouts.app')

@section('content')
  <h1 class="mb-10 text-2xl">Payment Confirmation</h1>
  @foreach ($cartItems as $cartItem)
    <div class="book-item">
        <div class="book-title">Book Name: <span class="ml-2 text-green-700">{{ $cartItem->book->title }}<span></div>
        <div class="book-title">Quantity: <span class="ml-2 text-green-700">${{$cartItem->quantity}}<span></div>
        <div class="book-title">Item Total Price: <span class="ml-2 text-green-700">${{$cartItem->item_total_price}}<span></div>
        <div class="book-title">Payment Gateway: <span class="ml-2 text-green-700">Stripe<span></div>
    </div>
  @endforeach

  <div class="book-title">Total: <span class="ml-2 text-green-700">${{ 100 }}<span></div>

  <form action="/checkout" method="POST" class="mt-5">
    @csrf
    <a href="{{ url()->previous() }}" class="button-style text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Back</a>
    <button type="submit" class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Confirm</button>
  </form>
@endsection
