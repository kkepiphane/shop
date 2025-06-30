<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\SmsController;
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


Route::get('/', [HomeController::class, 'index'])->name('home');

// Authentification
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/verify-email/{token}', [AuthController::class, 'verifyEmail'])->name('verify.email');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/shop', [ShopController::class, 'index'])->name(name: 'shop');
Route::get('/shop/detail/{id}', [ShopController::class, 'show'])->name(name: 'shop.show');
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::get('/email/verify/{id}/{token}', [AuthController::class, 'verifyEmail'])
    ->name('verification.verify');


Route::post('/cart/add', [CartController::class, 'addToCart'])->name('cart.add');
Route::get('/cart', [CartController::class, 'index'])->name('cart.view');
Route::post('/cart/update', [CartController::class, 'updateCart'])->name('cart.update');
Route::post('/cart/remove', [CartController::class, 'removeFromCart'])->name('cart.remove');
// Produits
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

Route::get('/checkout', [CheckoutController::class, 'showCheckout'])->name('checkout');
Route::post('/checkout/process', [CheckoutController::class, 'processPayment'])->name('checkout.process');
Route::get('/checkout/complete/{order}', [CheckoutController::class, 'paymentComplete'])->name('checkout.complete');
Route::post('/products/{product}/checkout', [OrderController::class, 'checkout'])->name('orders.checkout')->middleware('auth', 'verified');

Route::post('/sms/webhook', [SmsController::class, 'handleWebhook'])->name('sms.webhook');
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');
Route::get('/newsletter/unsubscribe/{email}', [NewsletterController::class, 'unsubscribe'])
    ->name('newsletter.unsubscribe');

Route::post('/newsletter/unsubscribe', [NewsletterController::class, 'processUnsubscribe'])
    ->name('newsletter.process-unsubscribe');
