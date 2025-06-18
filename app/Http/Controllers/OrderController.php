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
        ]);

        // Créer la commande
        $order = Order::create([
            'user_id' => auth()->id(),
            'order_number' => 'ORD' . Str::upper(Str::random(10)),
            'total_amount' => $product->price * $request->quantity,
            'status' => 'pending'
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => $request->quantity,
            'unit_price' => $product->price
        ]);


        return $this->processPayment($order);
    }
}
