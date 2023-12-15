<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\book;
use App\Models\Review;

class ReviewController extends Controller
{

    public function __construct()
    {
        $this->middleware('throttle:reviews')->only(['store']);
    }
    
    /**
     * Show the form for creating a new resource.
     */
    public function create(Book $book)
    {
        return view('books.reviews.create', ['book' => $book]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Book $book)
    {
        $data = $request->validate([
            'review' => 'required|min:15',
            'rating' => 'required|min:1|max:5|integer',
        ]);
        $user = Auth::user();
        $review = new Review($data);
        $review->user()->associate($user);

        $book->reviews()->save($review);
        return redirect()->route('books.show', $book)->with('success', 'Review created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $book, $review)
    {
        $review = Review::find($review);
        $book = $review->book;
        $user = $review->user;
        return view('books.reviews.show', ['book' => $book, 'review' => $review, 'user' => $user]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($book, $review)
    {
        $review = Review::find($review);
        return view('books.reviews.edit', ['review' => $review]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $book, $review)
    {
        $data = $request->validate([
            'review' => 'required|min:15',
            'rating' => 'required|min:1|max:5|integer',
        ]);

        $review = Review::find($review);

        if ($review) {
            $review->update([
                'review' => $data['review'],
                'rating' => $data['rating'],
            ]);
            return redirect()->route('books.show', ['book' => $book])->with('success', 'Review updated successfully');
        } else {
            return redirect()->route('books.show', ['book' => $book])->with('error', 'Review not updated, Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $book, $review)
    {
        $review = Review::find($review);
        if ($review) {
            $review->delete();
            return redirect()->route('books.show', ['book' => $book])->with('success', 'Review deleted successfully');
        } else {
            return redirect()->route('books.show', ['book' => $book])->with('error', 'Review not deleted, Please try again.');
        }
    }
}
