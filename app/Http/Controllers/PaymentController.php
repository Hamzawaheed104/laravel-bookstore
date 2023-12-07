<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Models\Book;

class PaymentController extends Controller
{
    public function purchase(Request $request){
        $book = Book::find($request->book_id);
        return view('payment.purchase', ['book' => $book]);
    }

    public function checkout(Request $request, Book $book){
        Stripe::setApiKey(env('STRIPE_SECRET')); 
        $productName = $request->input('book_name');
        $price = $request->input('price');
        $session = Session::create([
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'USD',
                        'product_data' => [
                            'name' => $productName,
                        ],
                        'unit_amount' => $price * 100,
                    ],
                    'quantity' => 1,
                ]
            ],
            'mode' => 'payment',
            'success_url' => route('payment.success'),
            'cancel_url' => route('dashboard')
        ]);

        return redirect()->away($session->url);
    }
    
    public function success(){
        return view('payment.success');
    }
}
