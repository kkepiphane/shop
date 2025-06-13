<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContactController extends Controller
{

  //Races 
  public function index()
  {
    return view('frontend.contact.index');
  }

}