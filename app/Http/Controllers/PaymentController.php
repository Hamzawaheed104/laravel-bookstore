<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\StripeClient;
use Stripe\Checkout\Session;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function shipping(Request $request){
        $user = Auth::user();
        $cart = $user->cart;
        $cartItems = $cart->cartItems;
        $total = $cartItems->sum(function ($cartItem) {
            return $cartItem->item_total_price;
        });

        $addresses = $user->addresses;
        return view('payment.shipping', ['cartItems' => $cartItems, 'total' => $total, 'addresses' => $addresses]);
    } 

    public function checkout(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $user = Auth::user();
        $cart = $user->cart;
        $cartItems = $cart->cartItems;

        $lineItems = $cartItems->map(function ($item) {
            return [
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => $item->book->title,
                    ],
                    'unit_amount' => $item->item_total_price * 100,
                ],
                'quantity' => $item->quantity,
            ];
        })->all();

        $session = Session::create([
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => route('payment.success'),
            'cancel_url' => route('dashboard'),
            'metadata' => ['user_id' => $user->id],
        ]);

        return redirect()->away($session->url);
    }
    
    public function success(){
        return view('payment.success');
    }

    public function handleWebhook(Request $request)
    {
        Log::info('Webhook received', ['request' => $request->all()]);

        $stripe = new StripeClient(env('STRIPE_SECRET'));
        $endpoint_secret = 'whsec_3a07df9e3a8fd29a686f5aa20eaeb2e2b00576453be601c53cef7608b1bed1b1';

        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $event = null;
        
        Log::info('Payload', ['payload' => $payload]);
        
        try {
            $event = Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );
            Log::info('Stripe event', ['event' => $event]);
        } catch (\UnexpectedValueException $e) {
            Log::error('UnexpectedValueException', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (SignatureVerificationException $e) {
            Log::error('SignatureVerificationException', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        switch ($event->type) {
            case 'checkout.session.completed':
                $paymentIntent = $event->data->object;
                $this->handleCheckoutSession($paymentIntent);
                break;
            default:
                return response()->json(['error' => 'Received unknown event type ' . $event->type], 400);
        }
        return response()->json(['message' => 'Webhook handled successfully'], 200);

    }

    protected function handleCheckoutSession($paymentIntent)
    {
        Log::info('In function charge succeeded', ['data' => $paymentIntent]);
        \Cart::clear();

        $userId = $paymentIntent->metadata->user_id;
        Log::info('User Info', ['user' => $userId]);

        $cart = Cart::where('user_id', $userId)->first();
        Log::info('Cart info', ['cart' => $cart]);

        if($cart){
            $order = Order::create([
                'user_id' => $userId,
                'status' => 'processing'
            ]);
            Log::info('Order info', ['order' => $order]);

            $cartItems = $cart->cartItems->all();
            foreach($cartItems as $cartItem){
                $orderItem = new OrderItem();
                $orderItem->order_id = $order->id;
                $orderItem->book_id = $cartItem->book_id;
                $orderItem->quantity = $cartItem->quantity;
                $orderItem->item_price = $cartItem->item_total_price * $cartItem->quantity;
                $orderItem->save();
            }
            Log::info('cartitems',  ['cartItems' => $cartItems]);
        }
        $cartItems = $cart->cartItems;
        $user = User::find($userId);
        if ($user->cart) {
            $user->cart->delete();
        }
    }
}
