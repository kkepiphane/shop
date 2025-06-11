<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{

  //Races 
  public function index()
  {
    $listsProducts = Product::orderBy('created_at', 'desc')->get();
    $products = ProductResource::collection($listsProducts);
    return view('frontend.shop.index', compact('products'));
  }
}
