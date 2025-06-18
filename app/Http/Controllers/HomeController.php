<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{

  public function index()
  {
    $listsProducts = Product::orderBy('created_at', 'desc')->limit(3)->get();
    $products = ProductResource::collection($listsProducts);
    return view('frontend.home.index', compact('products'));
  }
}
