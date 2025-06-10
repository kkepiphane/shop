<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ShippingAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function checkout(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'address_line_1' => 'required|string',
            'address_line_2' => 'nullable|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'postal_code' => 'required|string',
            'country' => 'required|string'
        ]);

        // Créer la commande
        $order = Order::create([
            'user_id' => auth()->id(),
            'order_number' => 'ORD-' . Str::upper(Str::random(10)),
            'total_amount' => $product->price * $request->quantity,
            'status' => 'pending'
        ]);

        // Ajouter l'article à la commande
        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => $request->quantity,
            'unit_price' => $product->price
        ]);

        // Ajouter l'adresse de livraison
        ShippingAddress::create([
            'order_id' => $order->id,
            'address_line_1' => $request->address_line_1,
            'address_line_2' => $request->address_line_2,
            'city' => $request->city,
            'state' => $request->state,
            'postal_code' => $request->postal_code,
            'country' => $request->country
        ]);

        // Rediriger vers le portail de paiement
        return $this->processPayment($order);
    }

    private function processPayment(Order $order)
    {
        // Intégration avec le système de paiement
        // Retourner la redirection ou la vue appropriée
    }
}