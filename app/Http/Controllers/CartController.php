<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CartController extends Controller
{

    //Races 
    public function index()
    {
        return view('frontend.cart.index');
    }
}
