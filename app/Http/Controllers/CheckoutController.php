<?php

// app/Http/Controllers/CheckoutController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    public function showCheckout()
    {
        
        if (\Cart::isEmpty()) {
            return redirect()->route('cart.view')->with('error', 'Votre panier est vide');
        }

        $cartItems = \Cart::getContent();

        // Si l'utilisateur est connecté, pré-remplir les champs
        $userData = [];
        if (auth()->check()) {
            $user = auth()->user();
            $userData = [
                'fullname' => $user->full_name,
                'email' => $user->email,
                'country' => $user->country,
                'phone' => $user->phone_number,
            ];
        }

        return view('frontend.checkout.index', compact('cartItems', 'userData'));
    }

    public function processPayment(Request $request)
    {
        // Validation des données
        $validated = $request->validate([
            'fullname' => 'required|string|max:255',
            'email' => 'required|email',
            'country' => 'required|string',
            'phone' => 'required|string',
            'c_address' => 'required|string',
        ]);

        // Créer l'utilisateur ou récupérer l'utilisateur existant
        $user = auth()->user();

        if (!$user) {
            $user = \App\Models\User::firstOrCreate(
                ['email' => $request->email],
                [
                    'full_name' => $request->fullname,
                    'password' => bcrypt(Str::random(16)),
                    'country' => $request->country,
                    'phone_number' => $request->phone,
                ]
            );
        }

        // Créer la commande
        $order = Order::create([
            'user_id' => $user->id,
            'order_number' => 'ORD' . strtoupper(Str::random(10)),
            'total_amount' => \Cart::getTotal(),
            'status' => 'pending',
        ]);

        // Ajouter les articles de la commande
        foreach (\Cart::getContent() as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->id,
                'quantity' => $item->quantity,
                'unit_price' => $item->price,
            ]);
        }


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
            "custom_meta_data" => ""
        ];
        Log::info($paymentData);

        $response = Http::withHeaders([
            'auth_token' => config('services.kprimepay.secret_key'),
            'Content-Type' => 'application/json',
        ])->post(config('services.kprimepay.api_url'), $paymentData);
        Log::info($response);

        // Traiter la réponse
        if ($response->successful() && $response->json('status')) {
            // Vider le panier
            \Cart::clear();

            // Rediriger vers la page de paiement
            return redirect()->away($response->json('data.checkout_url'));
        } else {
            // En cas d'erreur
            return back()->with('error', 'Erreur lors de l\'initialisation du paiement: ' . $response->json('message'));
        }
    }

    public function paymentComplete($orderId)
    {
        $order = Order::findOrFail($orderId);

        $order->update(['status' => 'completed']);

        return view('frontend.checkout.thankyou', compact('order'));
    }
}
