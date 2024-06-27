<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Address;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\StripeClient;
use Stripe\Checkout\Session;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function checkout(Request $request){
        $user = Auth::user();
        $cart = $user->cart;
        $cartItems = $cart->cartItems;
        $total = $cartItems->sum(function ($cartItem) {
            return $cartItem->item_total_price;
        });

        $addresses = $user->addresses;
        return view('cart.shipping', ['cartItems' => $cartItems, 'total' => $total, 'addresses' => $addresses]);
    } 

    public function shipping(Request $request){
        $user = Auth::user();

        if ($request->has('selectedAddressId') && $request->input('selectedAddressId') != '') {
            $addressId = $request->input('selectedAddressId');
            $address = Address::find($addressId);

            if (!$address) {
                return redirect()->back()->withErrors(['selectedAddressId' => 'The selected address is invalid. Please try again with another address']);
            }
        } else {
            $validationRules = [
                'street' => 'required|string|max:255',
                'city' => 'required|string|max:255',
                'region' => 'required|string|max:255',
                'country' => 'required|string|max:255',
                'zip' => 'required|regex:/^\d{4,6}$/',
                'contact_number' => 'required|regex:/^[\d\s+\-()]{1,15}$/'
            ];
            $validatedData = $request->validate($validationRules);

            $address = $user->addresses()->create($validatedData); 

            if (!$address) {
                return redirect()->back()->withErrors(['address' => 'The address could not be created. Please try again']);
            }
        }

        session(['checkoutAddress' => $address->id]);
        return redirect()->route('payment.payment')->with('addressId', $address->id);
    }

    public function payment(Request $request){
        $checkoutAddressId = session('checkoutAddress');
        $address = null;

        if ($checkoutAddressId) {
            $address = Address::find($checkoutAddressId);
        }

        $user = Auth::user();
        $cart = $user->cart;
        $cartItems = $cart->cartItems;
        $total = $cartItems->sum(function ($cartItem) {
            return $cartItem->item_total_price;
        });

        return view('payment.payment', ['address' => $address, 'cartItems' => $cartItems, 'total' => $total]);
    }

    public function confirmOrder(Request $request)
    {
        $user = Auth::user();

        if($request->input('paymentMethod') == 'CashOnDelivery'){
            $paymentType = 'cash_on_delivery';
            $this->createOrder($user->id, $paymentType);
            $this->clearCart($user->id);
            return redirect()->route('payment.success');
        }else{
            Stripe::setApiKey(env('STRIPE_SECRET'));

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
        $userId = $paymentIntent->metadata->user_id;
        Log::info('User Info', ['user' => $userId]);
        $paymentType = 'card_payment';
        $this->createOrder($userId, $paymentType);
        $this->clearCart($userId);
    }

    protected function createOrder($userId, $paymentType){
        $cart = Cart::where('user_id', $userId)->first();
        Log::info('Cart info', ['cart' => $cart]);

        if($cart){
            $order = Order::create([
                'user_id' => $userId,
                'status' => 'processing',
                'payment_type' => $paymentType
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
            return $order->id;
        }
        
        return null;
    }

    private function clearCart($userId){
        $user = User::find($userId);
        $cart = $user->cart;
        $cartItems = $cart->cartItems;
        if ($user->cart) {
            Log::info('Cart is found inside condition');
            $cart->cartItems->each(function($cartItem) {
                $cartItem->delete();
            });
            $user->cart->delete();
        }
        \Cart::clear();
        Log::info('Cart Items', ['cartItems' => $user->cart->cartItems]);

        return true;
    }
}
