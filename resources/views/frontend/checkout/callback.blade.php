@extends('layouts.app')

@section('title', 'Confirmation de commande')

@section('content')
<style>
    .card {
        border: none;
        border-radius: 10px;
        overflow: hidden;
    }

    .card-header {
        border-radius: 10px 10px 0 0 !important;
    }

    .order-details {
        border-left: 4px solid #28a745;
    }

    address {
        font-style: normal;
        border-left: 3px solid #6c757d;
    }

    .list-group-item {
        transition: all 0.3s;
    }

    .list-group-item:hover {
        background-color: #f8f9fa;
    }
</style>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg">
                <div class="card-header bg-success text-white">
                    <h3 class="mb-0">Commande confirmée</h3>
                </div>

                <div class="card-body">
                    <div class="text-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="#28a745" class="bi bi-check-circle-fill" viewBox="0 0 16 16">
                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z" />
                        </svg>
                        <h2 class="mt-3 text-success">Paiement réussi</h2>
                    </div>

                    <div class="alert alert-success">
                        <p class="mb-0">
                            Bonjour <strong>{{ $order->user->full_name }}</strong>,<br>
                            Votre commande a été reçue avec succès.
                        </p>
                    </div>

                    <div class="order-details bg-light p-4 rounded mb-4">
                        <h4 class="mb-3">Détails de la commande</h4>

                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Référence de paiement:</strong><br>
                                    {{ $paymentData->kpp_tx_reference ?? $order->transaction->kpp_tx_reference }}
                                </p>

                                <p><strong>Date de paiement:</strong><br>
                                    {{ now()->format('d/m/Y à H:i') }}
                                </p>
                            </div>

                            <div class="col-md-6">
                                <p><strong>Montant payé:</strong><br>
                                    {{ number_format($order->total_amount, 0, ',', ' ') }} XOF
                                </p>

                                <p><strong>Date de livraison prévue:</strong><br>
                                    {{ now()->addDays(5)->format('d/m/Y') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="delivery-info mb-4">
                        <h5>Informations de livraison</h5>
                        <p class="mb-1">Votre commande sera livrée à l'adresse suivante :</p>
                        <address class="bg-white p-3 rounded">
                            {{ $order->shipping_address }}<br>
                            {{ $order->shipping_city }}, {{ $order->shipping_country }}
                        </address>
                    </div>

                    <div class="next-steps">
                        <h5>Prochaines étapes</h5>
                        <ul class="list-group mb-4">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>1. Préparation de votre commande</span>
                                <span class="badge bg-secondary">En cours</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>2. Expédition</span>
                                <span class="badge bg-secondary">À venir</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>3. Livraison prévue le {{ now()->addDays(5)->format('d/m/Y') }}</span>
                                <span class="badge bg-secondary">À venir</span>
                            </li>
                        </ul>
                    </div>

                    <div class="alert alert-info">
                        <h5 class="alert-heading">Un email de confirmation vous a été envoyé</h5>
                        <p class="mb-0">
                            Vous recevrez également une notification WhatsApp/SMS avec les détails de votre commande.
                            Pour toute question, contactez notre service client.
                        </p>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('orders.show', $order->id) }}" class="btn btn-outline-primary">
                            <i class="bi bi-file-text"></i> Voir le détail de la commande
                        </a>
                        <a href="{{ route('home') }}" class="btn btn-primary">
                            <i class="bi bi-house"></i> Retour à l'accueil
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection