<?php

namespace App\Http\Controllers;

use App\Mail\EmailVerificationMail;
use App\Models\EmailVerificationToken;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CheckoutController extends Controller
{
  public function showCheckout()
  {

    if (\Cart::isEmpty()) {
      return redirect()->route('cart.view')->with('error', 'Votre panier est vide');
    }

    if (!auth()->check()) {
      session(['redirect_to' => route('checkout')]);
      return redirect()->route('login')->with('info', 'Veuillez vous connecter ou créer un compte pour continuer.');
    }

    $cartItems = \Cart::getContent();

    $userData = [];
    if (auth()->check()) {
      $user = auth()->user();
      $userData = [
        'country' => $user->country ?? '',
        'phone' => $user->phone_number ?? '',
      ];
    }

    return view('frontend.checkout.index', compact('cartItems', 'userData'));
  }

  public function processPayment(Request $request)
  {
    // Validation des données
    $validated = $request->validate([
      'country' => 'required|string|size:2',
      'c_address' => 'required|string',
      'phone' => 'required|string',
    ]);

    $user = auth()->user();

    $user->update([
      'country' => $request->country,
      'phone_number' => $request->phone,
      'address' => $request->c_address,
    ]);

    // Création de la commande 
    $order = Order::create([
      'user_id' => $user->id,
      'order_number' => 'ORD' . strtoupper(Str::random(10)),
      'total_amount' => \Cart::getTotal(),
      'status' => 'pending',
    ]);

    // Ajout des articles
    foreach (\Cart::getContent() as $item) {
      OrderItem::create([
        'order_id' => $order->id,
        'product_id' => $item->id,
        'quantity' => $item->quantity,
        'unit_price' => $item->price,
      ]);
    }

    // Préparation des données de paiement
    $paymentData = [
      "merchant_number" => config('services.kprimepay.merchant_number'),
      "transaction_id" => $order->order_number,
      "currency" => "XOF",
      "amount" => \Cart::getTotal(),
      "with_fees" => 0,
      "mode" => 1,
      "description" => "Paiement pour la commande " . $order->order_number,
      "return_url" => route('checkout.complete', $order->id),
      "locale" => "fr",
      "custom_meta_data" => [
        'order_id' => $order->id,
        'user_id' => $user->id,
        'phone' => $request->phone
      ],
    ];

    $response = Http::withHeaders([
      'auth_token' => config('services.kprimepay.secret_key'),
      'Content-Type' => 'application/json',
    ])->post(config('services.kprimepay.api_url'), $paymentData);

    //Log::info($response);

    if ($response->successful() && $response->json('status')) {
      \Cart::clear();
      return redirect()->away($response->json('data.checkout_url'));
    }

    // Gestion des erreurs
    return back()->with('error', 'Erreur lors de l\'initialisation du paiement');
  }

  public function paymentComplete($orderId)
  {
    $order = Order::findOrFail($orderId);

    $order->update(['status' => 'completed']);

    return view('frontend.checkout.thankyou', compact('order'));
  }

  private function sendVerificationEmail(User $user)
  {
    $token = \Str::random(60);

    EmailVerificationToken::create([
      'user_id' => $user->id,
      'token' => $token,
      'expires_at' => now()->addHours(24)
    ]);

    Mail::to($user->email)->send(new EmailVerificationMail($user, $token));
  }
}
