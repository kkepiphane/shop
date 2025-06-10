<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
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
    return view('welcome');
});

// Authentification
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/check-phone', [AuthController::class, 'checkPhone']);
Route::get('/verify-email/{token}', [AuthController::class, 'verifyEmail'])->name('verify.email');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Produits
Route::get('/products', [ProductController::class, 'index'])->name('products.index')->middleware('auth', 'verified');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show')->middleware('auth', 'verified');

// Commandes
Route::post('/products/{product}/checkout', [OrderController::class, 'checkout'])->name('orders.checkout')->middleware('auth', 'verified');
