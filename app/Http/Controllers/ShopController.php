<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ShopController extends Controller
{

  //Races 
  public function index()
  {
    // $lists = Couleur::orderBy('created_at', 'desc')->get();
    // $colors = CouleurResource::collection($lists);
    return view('frontend.shop.index');
  }

}