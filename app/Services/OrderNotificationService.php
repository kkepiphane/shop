<?php

namespace App\Services;

class OrderNotificationService
{
    public static function getOrderConfirmationMessage($order, $user, $paymentReference)
    {
        $deliveryDate = now()->addDays(5)->format('d/m/Y');

        $message = "Bonjour {$user->full_name},\n";
        $message .= "Votre commande #{$order->order_number} est confirmée.\n";
        $message .= "Réf. paiement: {$paymentReference}\n";
        $message .= "Livraison prévue: {$deliveryDate}\n";
        $message .= "Merci pour votre confiance!";

        return $message;
    }
}
