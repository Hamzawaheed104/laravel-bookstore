<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\PaymentController;
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

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', [BookController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::resource('books', BookController::class)->only(['index', 'show']);
    Route::resource('books.reviews', ReviewController::class)->scoped(['review' => 'book'])->only(['create','store']);

    Route::post('/checkout',[PaymentController::class, 'checkout'])->name('payment.checkout');
    Route::get('/success',[PaymentController::class, 'success'])->name('payment.success');
    Route::get('/purchase/{book_id}',[PaymentController::class, 'purchase'])->name('payment.purchase');
});

require __DIR__.'/auth.php';
