<?php

namespace App\Http\Controllers;

use App\Mail\NewsletterSubscription;
use Illuminate\Http\Request;
use App\Models\Subscriber;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:subscribers,email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => __('You are already subscribed or the email is invalid')
            ], 422);
        }

        try {
            $subscriber = Subscriber::create([
                'email' => $request->email,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            // Optionnel: Envoyer un email de confirmation
            Mail::to($request->email)->send(new NewsletterSubscription($subscriber));

            return response()->json([
                'success' => true,
                'message' => __('Thank you for subscribing to our newsletter!')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Subscription failed. Please try again later.')
            ], 500);
        }
    }

    public function unsubscribe($email)
    {
        // Décoder l'email au cas où il serait encodé dans l'URL
        $email = urldecode($email);

        return view('emails.unsubscribe-confirm', [
            'email' => $email,
            'success' => null
        ]);
    }

    public function processUnsubscribe(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $subscriber = Subscriber::where('email', $request->email)->first();

        if ($subscriber) { 
            $subscriber->update(['is_active' => false]);
            return view('emails.unsubscribe-confirm', [
                'email' => $request->email,
                'success' => true
            ]);
        }

        return view('emails.unsubscribe-confirm', [
            'email' => $request->email,
            'success' => false
        ]);
    }
}
