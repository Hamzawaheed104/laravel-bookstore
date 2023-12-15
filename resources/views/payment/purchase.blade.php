@extends('layouts.app')

@section('content')
  <h1 class="mb-10 text-2xl">Payment Confirmation</h1>
  <div class="book-item">
    <div class="book-title">Book Name: <span class="ml-2 text-green-700">{{ $book->title }}<span></div>
    <div class="book-title">Price: <span class="ml-2 text-green-700">${{$book->price}}<span></div>
    <div class="book-title">Payment Gateway: <span class="ml-2 text-green-700">Stripe<span></div>
  </div>

  <form action="/checkout" method="POST" class="mt-5">
    @csrf
    <input type="hidden" name="book_name" value="{{ $book->title }}">
    <input type="hidden" name="price" value="{{ $book->price }}">
    <a href="{{ url()->previous() }}" class="button-style text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Back</a>
    <button type="submit" class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Confirm</button>
  </form>
@endsection
