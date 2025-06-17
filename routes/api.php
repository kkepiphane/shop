<?php

use App\Http\Controllers\PaymentController;
use App\Http\Controllers\WhatsAppController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::post('/callback', [PaymentController::class, 'handleCallback']);
Route::post('/sms-callback', [PaymentController::class, 'handleSmsResponse']);
Route::get('/commande/confirmation/{order}', [PaymentController::class, 'showConfirmation'])->name('order.confirmation');

Route::prefix('whatsapp')->group(function () {
  Route::post('/send-text', [WhatsAppController::class, 'sendTextMessage']);
  Route::post('/upload-document', [WhatsAppController::class, 'uploadDocument']);
  Route::post('/send-document', [WhatsAppController::class, 'sendDocumentMessage']);
  Route::post('/register-webhook', [WhatsAppController::class, 'registerWebhook']);
  Route::post('/test-webhook', [WhatsAppController::class, 'testWebhook']);
  Route::post('/create-keyword', [WhatsAppController::class, 'createKeyword']);
  //Route::post('/whatsapp-callback', [WhatsAppController::class, 'handleIncomingWebhook']);
});

// Route::get('/whatsapp-callback', function (Request $request) {
//   $expectedSecret = config('services.kprimesms.webhook_secret');

//   if ($request->query('webhook_secret') === $expectedSecret && $request->query('webhook_type') === 'webhook_register') {
//     return response('Webhook validated successfully', 200);
//   }


//   return response('Unauthorized', 401);
// });

// Route::match(['get', 'post'], '/whatsapp-callback', function (Request $request) {
//     $expectedSecret = '@PrimeSoft1234'; // ou config('services.kprimesms.webhook_secret')

//     $secret = $request->input('webhook_secret') ?? $request->query('webhook_secret');
//     $type = $request->input('webhook_type') ?? $request->query('webhook_type');

//     Log::info('Webhook callback received', [
//         'method' => $request->method(),
//         'secret' => $secret,
//         'type' => $type,
//         'data' => $request->all()
//     ]);


//         return response()->json(['description' => 'Webhook confirmed OK'], 200);

// });

// routes/api.php
// Route::post('/whatsapp-callback', function (Request $request) {
//   $data = $request->all();
//   Log::info('Message reçu:', $data);

//   // Vérifier si c'est un keyword
//   if (str_starts_with(haystack: $data['message'], 'TEST123')) {
//     // Répondre automatiquement
//     return response()->json([
//       'reply' => "Vous avez utilisé le mot-clé TEST123. Message reçu : " . $data['message']
//     ], 200);
//   }

//   return response()->json(['status' => 'ignored']);
// });
