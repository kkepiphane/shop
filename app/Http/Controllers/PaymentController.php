<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function handleCallback(Request $request)
    {
        Log::info('Callback reçu:', $request->all());

        if (!$request->has(['status', 'kpp_tx_reference', 'transaction_id'])) {
            Log::error('Paramètres manquants dans le callback');
            return response()->json(['error' => 'Paramètres invalides'], 400);
        }

        $metaData = json_decode($request->custom_meta_data ?? '{}', true);
        $orderId = $metaData['order_id'] ?? null;
        $phone = $metaData['phone'] ?? null;

        if (!$orderId) {
            Log::error('Order ID manquant dans les métadonnées');
            return response()->json(['error' => 'Order ID required'], 400);
        }

        // Récupération de la commande
        $order = Order::find($orderId);
        if (!$order) {
            Log::error('Commande introuvable', ['order_id' => $orderId]);
            return response()->json(['error' => 'Order not found'], 404);
        }

        // Enregistrement de la transaction
        $transaction = Transaction::create([
            'order_id' => $order->id,
            'transaction_id' => $request->transaction_id,
            'kpp_tx_reference' => $request->kpp_tx_reference,
            'status' => $request->status,
            'type' => $request->type ?? null,
            'method' => $request->transaction_method ?? null,
            'amount' => $request->transaction_amount / 100,
            'fees' => $request->transaction_fees / 100,
            'currency' => $request->transaction_currency,
            'description' => $request->transaction_description,
            'payload' => $request->all(),
        ]);

        // Mise à jour du statut de la commande
        $order->update(['status' => $this->mapStatus($request->status)]);

        // Envoi des notifications si paiement réussi
        if ($request->status === 'success') {
            $this->sendNotifications($order, $request, $phone);
        }

        return response()->json(['status' => 'success']);
    }

    protected function mapStatus($paymentStatus)
    {
        return match (strtolower($paymentStatus)) {
            'success' => 'paid',
            'failed' => 'payment_failed',
            default => 'pending_payment',
        };
    }

    protected function sendNotifications($order, $paymentData, $phone)
    {
        // Préparation du message
        $deliveryDate = now()->addDays(5)->format('d/m/Y');
        $message = "Bonjour {$order->user->full_name},\n";
        $message .= "Votre paiement de {$order->total_amount} XOF a été confirmé.\n";
        $message .= "Référence: {$paymentData->kpp_tx_reference}\n";
        $message .= "Livraison prévue le {$deliveryDate}.";

        // Envoi SMS
        $this->sendSMS($phone, $message, $paymentData->country);

        // Envoi WhatsApp
        $this->sendWhatsApp($phone, $message, $paymentData->country);
    }

    protected function sendSMS($phone, $message, $country)
    {
        try {
            $response = Http::withHeaders([
                'token' => config('services.kprimesms.sms_token'),
                'key' => config('services.kprimesms.sms_key'),
                'Content-Type' => 'application/json',
            ])->post(config('services.kprimesms.sms_api_url') . '/sms/push', [
                'sender' => config('services.kprimesms.sender'),
                'country' => $country,
                'phone_number' => preg_replace('/\D/', '', $phone),
                'message' => $message,
                'response_url' => route('sms.webhook'),
            ]);

            Log::info('SMS envoyé', ['response' => $response->body()]);
        } catch (\Exception $e) {
            Log::error('Erreur envoi SMS', ['error' => $e->getMessage()]);
        }
    }

    protected function sendWhatsApp($phone, $message, $country)
    {
        try {
            $response = Http::withHeaders([
                'token' => config('services.kprimesms.token'),
                'key' => config('services.kprimesms.key'),
                'Content-Type' => 'application/json',
            ])->post(config('services.kprimesms.sms_api_url') . '/whatsapp/text-message', [
                'country' => $country,
                'phone_number' => preg_replace('/\D/', '', $phone),
                'content' => $message,
                'response_url' => route('whatsapp.webhook'),
            ]);

            Log::info('WhatsApp message sent', ['response' => $response->body()]);
        } catch (\Exception $e) {
            Log::error('WhatsApp send error', ['error' => $e->getMessage()]);
        }
    }
}
