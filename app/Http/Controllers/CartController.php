<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = \Cart::getContent();
        return view('frontend.cart.index', compact('cartItems'));
    }

    public function addToCart(Request $request)
    {
        $product = Product::findOrFail($request->product_id);

        \Cart::add([
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'quantity' => 1,
            'attributes' => [
                'image' => $product->image_path,
            ],
            'associatedModel' => $product
        ]);

        return response()->json([
            'success' => true,
            'cart_count' => \Cart::getTotalQuantity()
        ]);
    }

    public function updateCart(Request $request)
    {
        if ($request->quantity < 1) {
            return response()->json([
                'success' => true,
                'cart_count' => \Cart::getTotalQuantity()
            ]);
        }
        \Cart::update($request->product_id, [
            'quantity' => [
                'relative' => false,
                'value' => $request->quantity
            ],
        ]);

        // return redirect()->back()->with('success', 'Cart updated successfully');
        return response()->json([
            'success' => true,
            'cart_count' => \Cart::getTotalQuantity()
        ]);
    }

    public function removeFromCart(Request $request)
    {
        \Cart::remove($request->product_id);
        return redirect()->back()->with('success', 'Item removed from cart');
    }
}
