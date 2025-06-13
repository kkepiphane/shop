<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    /**
     * Handle SMS webhook notifications
     */
    public function handleSmsWebhook(Request $request)
    {
        Log::info('SMS Webhook Received:', $request->all());

        // Validez la requête si nécessaire (signature, etc.)

        // Exemple de traitement pour un statut SMS
        if ($request->has('status')) {
            $status = $request->input('status');
            $messageId = $request->input('message_id');

            Log::info("SMS Status Update", [
                'message_id' => $messageId,
                'status' => $status
            ]);

            // Ici vous pourriez mettre à jour votre base de données
            // si vous stockez les envois SMS
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Handle WhatsApp webhook notifications
     */
    public function handleWhatsAppWebhook(Request $request)
    {
        Log::info('WhatsApp Webhook Received:', $request->all());

        // Validez la requête si nécessaire

        // Exemple de traitement pour un statut WhatsApp
        if ($request->has('status')) {
            $status = $request->input('status');
            $messageId = $request->input('message_id');

            Log::info("WhatsApp Status Update", [
                'message_id' => $messageId,
                'status' => $status
            ]);
        }

        return response()->json(['status' => 'success']);
    }
}
