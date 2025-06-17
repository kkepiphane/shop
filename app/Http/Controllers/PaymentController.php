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

        $status = $request->input('status');
        $data = $request->input('data', []);

        if (!$status || !isset($data['kpp_tx_reference'], $data['transaction_id'])) {
            // Log::error('Paramètres manquants dans le callback');
            return response()->json(['error' => 'Paramètres invalides'], 400);
        }

        $metaData = $data['custom_meta_data'] ?? [];
        $orderId = $metaData['order_id'] ?? null;
        $phone = $metaData['phone'] ?? null;

        if (!$orderId) {
            return response()->json(['error' => 'Order ID required'], 400);
        }

        $order = Order::find($orderId);
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        Transaction::create([
            'order_id' => $order->id,
            'transaction_id' => $data['transaction_id'],
            'kpp_tx_reference' => $data['kpp_tx_reference'],
            'status' => $status,
            'type' => $request->input('type'),
            'method' => $data['transaction_method'] ?? null,
            'amount' => $data['transaction_amount'],
            'fees' => $data['transaction_fees'],
            'currency' => $data['transaction_currency'],
            'description' => $data['transaction_description'],
            'payload' => $request->all(),
        ]);

        $order->update(['status' => $this->mapStatus($status)]);

        if ($status === 'success') {
            $this->sendNotifications($order, $request, $phone);
            return response()->json(['status' => 'success']);
        } else {
            return response()->json(['status' => 'failed']);
        }
    }


    protected function mapStatus($paymentStatus)
    {
        return match (strtolower($paymentStatus)) {
            'success' => 'paid',
            'failed' => 'unpaid',
            default => 'pending_payment',
        };
    }

    public function showConfirmation($orderId)
    {
        $order = Order::with(relations: ['user', 'transaction'])->findOrFail($orderId);

        $kpp_tx_reference  = $order->transaction->kpp_tx_reference;
        $transaction_amount = $order->total_amount;

        return view('frontend.checkout.callback', compact('order', 'kpp_tx_reference', 'transaction_amount'));
    }

    protected function sendNotifications($order, $request, $phone)
    {
        // Préparation du message
        $data = $request->input('data', []);
        $deliveryDate = now()->addDays(5)->format('d/m/Y');
        $message = "Bonjour {$order->user->full_name},\n";
        $message .= "Votre paiement de {$order->total_amount} XOF a été confirmé.\n";
        $message .= "Référence: {$data['kpp_tx_reference']}\n";
        $message .= "Livraison prévue le {$deliveryDate}.";
        $country = $data['customer_details']['country'] ?? 'TG';

        // Envoie de sms
        $this->sendSMS($phone, $message, $country);
    }


    public function sendSMS($phone, $message, $country)
    {
        try {

            Http::withHeaders([
                'token' => config('services.kprimesms.token_sms'),
                'key' => config('services.kprimesms.key_sms'),
                'Content-Type' => 'application/json',
            ])->post(config('services.kprimesms.sms_api_url') . '/sms/push', [
                'sender' => config('services.kprimesms.sender'),
                'country' => $country,
                'phone_number' => preg_replace('/\D/', '', $phone),
                'message' => $message,
                'response_url' => "https://52c6-2c0f-2a80-3-208-a4f6-ea9d-9d48-a2e8.ngrok-free.app/api/sms-callback"
            ]);

            //Log::info('SMS envoyé', ['response' => $response->body()]);
        } catch (\Exception $e) {
            Log::error('Erreur envoi SMS', ['error' => $e->getMessage()]);
        }
    }


    public function handleSmsResponse(Request $request)
    {
        $data = $request->all();

        if ($data['status']) {
            return response()->json([
                'status' => 'success',
            ], 200);
        } else {
            return response()->json([
                'error' => 'Unprocessable request: missing or invalid parameters',
                'details' => $data
            ], 422);
        }
    }
}
