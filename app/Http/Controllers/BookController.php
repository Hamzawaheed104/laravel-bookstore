<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\Book;
use Illuminate\Support\Facades\Auth;


class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (Auth::check()) {
            $title = $request->input('title');
            $filter = $request->input('filter', '');
    
            $books = Book::when(
                $title,
                fn($query, $title) => $query->title($title)
            );
    
            $books = $books->when(
                $filter === 'highest_rated_last_month',
                fn($query) => $query->highestRatedLastMonth()
            )->when(
                $filter === 'highest_rated_last_6months',
                fn($query) => $query->highestRatedLast6Months()
            )->latest()->withAvgRating()->withReviewsCount();
    
            return view('books.index', ['books' => $books->get()]);
        } else {
            return view('auth.login');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $book = Book::with([
            'reviews' => fn($query) => $query->latest()
        ])->withAvgRating()->withReviewsCount()->findOrFail($id);
    
        return view('books.show', ['book' => $book]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
