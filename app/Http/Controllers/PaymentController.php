<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $smsController;

    public function __construct()
    {
        $this->smsController = new SmsController();
    }
    public function handleCallback(Request $request)
    {

        $status = $request->input('status');
        $data = $request->input('data', []);

        if (!$status || !isset($data['kpp_tx_reference'], $data['transaction_id'])) {
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
        $this->smsController->sendSMS($phone, $message, $country);
    }
}
