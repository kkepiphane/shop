<?php

// app/Http/Controllers/CheckoutController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsController extends Controller
{

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
                'response_url' => "https://9ddc-2c0f-2a80-3-208-9d1-d209-4c20-aaaa.ngrok-free.app/api/sms-callback"
            ]);
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
