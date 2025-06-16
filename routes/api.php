<?php

use App\Http\Controllers\PaymentController;
use App\Http\Controllers\WhatsAppController;
use Illuminate\Http\Request;
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

Route::post('/callback', [PaymentController::class, 'handleCallback'])
  ->name('payment.callback');

Route::prefix('whatsapp')->group(function () {
  Route::post('/send-text', [WhatsAppController::class, 'sendTextMessage']);
  Route::post('/upload-document', [WhatsAppController::class, 'uploadDocument']);
  Route::post('/send-document', [WhatsAppController::class, 'sendDocumentMessage']);
  Route::post('/register-webhook', [WhatsAppController::class, 'registerWebhook']);
  Route::post('/test-webhook', [WhatsAppController::class, 'testWebhook']);
  Route::post('/create-keyword', [WhatsAppController::class, 'createKeyword']);
  Route::post('/incoming-webhook', [WhatsAppController::class, 'handleIncomingWebhook']);
});
