@extends('layouts.app')

@section('content')
  <h1 class="mb-10 text-2xl">Add Review for {{ $book->title }}</h1>

  <form method="POST" id="create-review-form" action="{{ route('books.reviews.store', $book) }}">
    @csrf
    <label for="review">Review</label>
    <textarea name="review" id="review" required class="input mb-4"></textarea>

    <label for="rating">Rating</label>

    <select name="rating" id="rating" class="input mb-4" required>
      <option value="">Select a Rating</option>
      @for ($i = 1; $i <= 5; $i++)
        <option value="{{ $i }}">{{ $i }}</option>
      @endfor
    </select>

    <a href="{{ url()->previous() }}" class="button-style text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Back</a>
    <a href="#" onclick="document.getElementById('create-review-form').submit();" class="button-style text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Create</a>
   </form>
@endsection 