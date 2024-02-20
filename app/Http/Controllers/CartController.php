<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Support\Facades\Auth;
use Darryldecode\Cart\CartCollection;

class CartController extends Controller
{
    public function cartList()
    {
        $cartItems = \Cart::getContent();
        foreach ($cartItems as $item) {
            $bookId = $item->get('id'); 
            $book = Book::find($bookId); 
            $item->put('book', $book);
        }
        return view('cart.cart', compact('cartItems'));
    }


    public function addToCart(Request $request)
    {
        $book = Book::find($request->id);
        if($book->stock >= $request->quantity){
            \Cart::add([
                'id' => $request->id,
                'name' => $request->title,
                'price' => $request->price,
                'quantity' => $request->quantity,
            ]);
            
            $user = Auth::user();
    
            $cart = Cart::where('user_id', $user->id)->first();
            if (!$cart) {
                $cart = Cart::create([
                    'user_id' => $user->id,
                ]);
            }
    
            $cartItem = $cart->cartItems->where('book_id', $request->id)->first();
            if($cartItem){
                $cartItem->quantity = $cartItem->quantity + 1;
                $cartItem->item_total_price = $cartItem->item_total_price + ($request->quantity * $request->price);
                $cartItem->save();
            }
            else{
                CartItem::create([
                    'user_id' => $user->id,
                    'cart_id' => $cart->id,
                    'book_id' => $request->id,
                    'quantity' => $request->quantity,
                    'item_total_price' => $request->quantity * $request->price,
                ]);
            }
            
            session()->flash('success', $book->title . ' is added to Cart Successfully !');
            return redirect()->route('dashboard');
        }else{
            session()->flash('error', 'Book is out of stock !');
            return redirect()->route('dashboard');
        }
        
    }

    public function updateCart(Request $request)
    {
        $user = Auth::user();
        $book = Book::find($request->id);

        if (!$book) {
            return response()->json(['success' => false, 'message' => 'Book not found'], 404);
        }

        if ($book->stock < $request->quantity) {
            return response()->json(['success' => false, 'message' => 'Insufficient stock. Available stock of '.$book->title.' is '.$book->stock], 400);
        }

        $cart = Cart::where('user_id', $user->id)->first();
        if (!$cart) {
            return response()->json(['success' => false, 'message' => 'Cart not found'], 404);
        }

        $cartItem = $cart->cartItems->where('book_id', $request->id)->first();

        if (!$cartItem) {
            return response()->json(['success' => false, 'message' => 'Item not in cart'], 404);
        }

        if ($request->quantity == 0) {
            $cartItem->delete();
            \Cart::remove($request->id);
        
            return response()->json(['success' => true, 'message' => 'Item removed from cart.']);

        } else {
            $cartItem->quantity = $request->quantity;
            $cartItem->item_total_price = $request->quantity * $book->price;
            $cartItem->save();

            \Cart::update(
                $request->id,
                [
                    'quantity' => [
                        'relative' => false,
                        'value' => $request->quantity
                    ],
                ]
            );

            $newTotal = \Cart::getTotal();

            return response()->json([
                'success' => true,
                'message' => 'Cart updated successfully',
                'item_total_price' => $cartItem->item_total_price,
                'new_total' => $newTotal,
                'item_id' => $request->id
            ]);
        }
    }


    public function removeCart(Request $request)
    {
        $user = Auth::user();
        $cart = $user->cart();
        \Cart::remove($request->id);
        if (\Cart::isEmpty()){
            $cart->delete();
            session()->flash('success', 'Cart is clear Successfully !');
            return redirect()->route('dashboard');
        }else{
            session()->flash('success', 'Cart item is removed Successfully !');
            return redirect()->route('cart.list');
        }
    }

    public function clearAllCart()
    {
        \Cart::clear();
        $user = Auth::user();
        
        if ($user->cart) {
            $user->cart->delete();
        }
        session()->flash('success', 'Cart is clear Successfully !');
        return redirect()->route('dashboard');
    }
}
