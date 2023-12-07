@extends('layouts.app')

@section('content')
  <h1 class="mb-10 text-2xl">Payment</h1>
  <div class="book-item">
    <div class="book-title">Book Name: <span class="ml-2 text-green-700">{{ $book->title }}<span></div>
    <div class="book-title">Price: <span class="ml-2 text-green-700">${{$book->price}}<span></div>
    <div class="book-title">Payment Gateway: <span class="ml-2 text-green-700">Stripe<span></div>
  </div>

  <form action="/checkout" method="POST" class="mt-5">
    @csrf
    <input type="hidden" name="book_name" value="{{ $book->title }}">
    <input type="hidden" name="price" value="{{ $book->price }}">
    <button type="submit" class="btn">Confirm</button>
  </form>
@endsection
