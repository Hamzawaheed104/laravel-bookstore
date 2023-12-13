@extends('layouts.app')

@section('content')
  <h1 class="mb-10 text-2xl">Review Details</h1>
  <div class="book-item">
    <div class="book-title">Reviewer Name: <span class="ml-2 text-green-700">{{ $user->name }}<span></div>
    <div class="book-title">Review: <span class="ml-2 text-green-700"> {{ $review->review }}<span></div>
    <div class="book-title mb-5">Rating: 
        <span class="ml-2 text-green-700">
            <span class="mr-2 text-sm font-medium text-slate-700">
                <x-star-rating :rating="$review->rating" />
            </span>
        <span>
    </div>
    <a href="{{ url()->previous() }}" class="button-style text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Back</a>
    @if (Auth::user() == $user)
        <a href="{{ route('books.reviews.edit',['book' => $book->id, 'review' => $review->id]) }}" class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Edit</a>
        <a href="#" onclick="deleteReview('{{ route('books.reviews.destroy', ['book' => $book->id, 'review' => $review->id]) }}')" class="focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900">Delete</a>
    @endif
  </div>
@endsection

<script>
    function deleteReview(url) {
        if (confirm('Are you sure you want to delete this review?')) {
            fetch(url, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
            })
            .then(response => {
                if (response.ok) {
                    console.log('Review deleted successfully');
                    window.location.reload();
                } else {
                    console.error('Failed to delete review');
                }
            })
            .catch(error => {
                console.error(error);
            });
        }
    }
</script>