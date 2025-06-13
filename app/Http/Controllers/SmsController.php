<?php

// app/Http/Controllers/CheckoutController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Log;

class SmsController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $data = $request->all();

        Log::info('SMS Webhook Received:', $data);

        // Traitez le statut du SMS ici
        // Vous pourriez mettre à jour la commande ou notifier l'utilisateur

        return response()->json(['status' => 'received']);
    }
}
