@extends('app')

@section('content')

<link href="{{ asset('assets/css/checkout.css') }}" rel="stylesheet">
<!-- Start Hero Section -->
<div class="hero">
    <div class="container">
        <div class="row justify-content-between">
            <div class="col-lg-5">
                <div class="intro-excerpt">
                    <h1>Merci pour votre commande!</h1>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Hero Section -->

<div class="untree_co-section">
    <div class="container">
        <div class="card">
            <div class="row justify-content-center">
                <div class="col-md-8 text-center">
                    <div class="card-header bg-success text-white">
                       <h5> Commande #{{ $order->order_number }} <br> confirmée</h5>
                    </div>
                    <div class="card-body">
                        <span class="icon-check_circle display-3 text-success"></span>
                        <p class="lead mb-5">Nous avons bien reçu votre paiement. Vous recevrez un email de confirmation sous peu.</p>
                        <p><a href="{{ route('home') }}" class="btn btn-black btn-lg">Retour à l'accueil</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection