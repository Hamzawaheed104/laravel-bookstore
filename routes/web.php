<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CartController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/',[BookController::class, 'index']);

Route::get('/dashboard', [BookController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::resource('books', BookController::class)->only(['index', 'show']);
    Route::get('/search-books', [BookController::class, 'search'])->name('books.search');
    Route::resource('books.reviews', ReviewController::class)->scoped(['review' => 'book']);

    Route::get('cart', [CartController::class, 'cartList'])->name('cart.list');
    Route::post('cart', [CartController::class, 'addToCart'])->name('cart.store');
    Route::post('update-cart', [CartController::class, 'updateCart'])->name('cart.update');
    Route::post('remove', [CartController::class, 'removeCart'])->name('cart.remove');
    Route::post('clear', [CartController::class, 'clearAllCart'])->name('cart.clear');

    Route::get('/checkout', [PaymentController::class, 'checkout'])->name('cart.checkout');
    Route::post('/shipping', [PaymentController::class, 'shipping'])->name('payment.shipping');
    Route::get('/payment', [PaymentController::class, 'payment'])->name('payment.payment');
    Route::post('/confirmOrder',[PaymentController::class, 'confirmOrder'])->name('payment.confirmOrder');
    Route::get('/success',[PaymentController::class, 'success'])->name('payment.success');

});

Route::post('/stripe/webhook', [PaymentController::class, 'handleWebhook']);

require __DIR__.'/auth.php';
