<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class WhatsAppController extends Controller
{
    protected $baseUrl;
    protected $headers;

    public function __construct()
    {
        $this->baseUrl = config('services.kprimesms.sms_api_url');
        $this->headers = [
            'token' => config('services.kprimesms.token_sms'),
            'key' => config('services.kprimesms.key_sms'),
        ];
    }


    public function sendTextMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'country' => 'required|string|size:2',
            'phone_number' => 'required|string|min:8',
            'content' => 'required|string|max:4000',
            'response_url' => 'required|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $response = Http::withHeaders(array_merge($this->headers, [
                'Content-Type' => 'application/json',
            ]))->post("{$this->baseUrl}/whatsapp/text-message", $request->all());

            return $this->handleApiResponse($response);
        } catch (\Exception $e) {
            Log::error('WhatsApp text message send error', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to send WhatsApp message'
            ], 500);
        }
    }

    public function handleWhatsappResponse(Request $request)
    {
        $data = $request->all();

        if ($data['status']) {
            return response()->json([
                'status' => 'success',
            ]);
        } else {
            return response()->json([
                'error' => 'Unprocessable request: missing or invalid parameters',
                'details' => $data
            ], 422);
        }
    }


    public function uploadDocument(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'attachment_file' => 'required|file|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $response = Http::withHeaders(array_merge($this->headers, [
                'Content-Type' => 'multipart/form-data',
            ]))->attach(
                'attachment_file',
                $request->file('attachment_file')->get(),
                $request->file('attachment_file')->getClientOriginalName()
            )->post("{$this->baseUrl}/whatsapp/template/document-upload");

            return $this->handleApiResponse($response);
        } catch (\Exception $e) {
            Log::error('WhatsApp document upload error', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to upload document'
            ], 500);
        }
    }


    public function sendDocumentMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'media_id' => 'required|string',
            'country' => 'required|string|size:2',
            'phone_number' => 'required|string|min:8',
            'content' => 'required|string|max:4000',
            'response_url' => 'required|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $response = Http::withHeaders(array_merge($this->headers, [
                'Content-Type' => 'application/json',
            ]))->post("{$this->baseUrl}/whatsapp/template/document-message", $request->all());

            return $this->handleApiResponse($response);
        } catch (\Exception $e) {
            Log::error('WhatsApp document message send error', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to send document message'
            ], 500);
        }
    }

    public function handleDocumentResponse(Request $request)
    {
        $data = $request->all();

        if ($data['status']) {
            return response()->json([
                'status' => 'success',
            ]);
        } else {
            return response()->json([
                'error' => 'Unprocessable request: missing or invalid parameters',
                'details' => $data
            ], 422);
        }
    }


    public function registerWebhook(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'webhook_url' => 'required|url|starts_with:https',
            'webhook_secret' => 'required|string|min:8',
            'webhook_type' => 'required|in:webhook_register,wa_phone_register',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $response = Http::withHeaders(array_merge($this->headers, [
                'Content-Type' => 'application/json',
            ]))->post("{$this->baseUrl}/whatsapp/webhook/register", $request->all());

            return $this->handleApiResponse($response);
        } catch (\Exception $e) {
            Log::error('WhatsApp webhook registration error', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to register webhook'
            ], 500);
        }
    }

    public function handleRegisterWebhookResponse(Request $request)
    {
        $expectedSecret = config('services.kprimesms.webhook_secret');

        if ($request->query('webhook_secret') === $expectedSecret && $request->query('webhook_type') === 'webhook_register') {
            return response('Webhook validated successfully', 200);
        }


        return response('Unauthorized', 401);
    }


    public function testWebhook(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'webhook_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $response = Http::withHeaders(array_merge($this->headers, [
                'Content-Type' => 'application/json',
            ]))->post("{$this->baseUrl}/whatsapp/webhook/test-endpoint", $request->all());

            return $this->handleApiResponse($response);
        } catch (\Exception $e) {
            Log::error('WhatsApp webhook test error', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to test webhook'
            ], 500);
        }
    }


    public function createKeyword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'keyword_name' => 'required|string|uppercase',
            'description' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $response = Http::withHeaders(array_merge($this->headers, [
                'Content-Type' => 'application/json',
            ]))->post("{$this->baseUrl}/whatsapp/incoming/new-keyword", $request->all());

            return $this->handleApiResponse($response);
        } catch (\Exception $e) {
            Log::error('WhatsApp keyword creation error', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create keyword'
            ], 500);
        }
    }


    public function handleIncomingWebhook(Request $request)
    {
        // Verify the webhook secret
        $webhookSecret = $request->query('webhook_secret');
        $webhookType = $request->query('webhook_type');

        if (!$this->verifyWebhook($webhookSecret, $webhookType)) {
            Log::warning('Invalid webhook request', $request->all());
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $data = $request->all();
        Log::info('Incoming WhatsApp message', $data);

        return response()->json(['status' => 'success']);
    }


    protected function verifyWebhook($secret, $type)
    {
        $validSecret = config('services.kprimesms.webhook_secret');
        $validTypes = ['webhook_register', 'wa_phone_register'];

        return $secret === $validSecret && in_array($type, $validTypes);
    }

    protected function handleApiResponse($response)
    {
        if ($response->successful()) {
            return $response->json(['status' => 'success'], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'API request failed',
            'api_response' => $response->body()
        ], $response->status());
    }
}
