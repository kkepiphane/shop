@extends('app')

@section('content')

<link href="{{ asset('assets/css/checkout.css') }}" rel="stylesheet">
<!-- Start Hero Section -->
<div class="hero">
    <div class="container">
        <div class="row justify-content-between">
            <div class="col-lg-5">
                <div class="intro-excerpt">
                    <h1>Votre commande est enregistrée !</h1>
                    <p class="mb-4">Nous traitons votre paiement</p>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Hero Section -->

<div class="untree_co-section">
    <div class="container">
        <div class="card shadow-sm">
            <div class="row justify-content-center">
                <div class="col-md-10 col-lg-8 text-center">
                    <div class="card-header bg-success text-white py-3">
                        <h5 class="mb-0">Commande #{{ $order->order_number }} en cours de traitement</h5>
                    </div>
                    <div class="card-body py-5">
                        <div class="mb-4">
                            <span class="icon-hourglass_empty display-3 text-success mb-3 d-block"></span>
                            <h6 class="text-primary">Validation du paiement en cours</h6>
                        </div>

                        <div class="alert alert-info mb-5">
                            <p class="mb-3"><strong>Que se passe-t-il maintenant ?</strong></p>
                            <ul class="text-start list-unstyled mx-auto" style="max-width: 400px;">
                                <li class="mb-2">✓ Votre commande est enregistrée</li>
                                <li class="mb-2">✓ Nous vérifions votre paiement</li>
                                <li>✓ Vous recevrez une confirmation par email/SMS</li>
                            </ul>
                        </div>

                        <div class="order-details mb-5 p-4 bg-light rounded">
                            <h5 class="mb-3">Récapitulatif de votre commande</h5>
                            <p class="mb-1">Montant: <strong>{{ number_format($order->total_amount, 2) }} FCFA</strong></p>
                            <p>Date: <strong>{{ $order->created_at->format('d/m/Y H:i') }}</strong></p>
                        </div>

                        <p class="text-muted mb-4">
                            <small>Si vous ne recevez pas de confirmation sous 30 minutes, vérifiez vos spams ou contactez notre service client.</small>
                        </p>

                        <div class="d-flex justify-content-center gap-3">
                            <a href="{{ route('home') }}" class="btn btn-outline-primary btn-lg">Retour à l'accueil</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection