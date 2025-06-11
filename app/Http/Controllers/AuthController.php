<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\EmailVerificationToken;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailVerificationMail;
use App\Rules\PhoneNumber;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('frontend.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (auth()->attempt($credentials)) {
            if (!auth()->user()->email_verified_at) {
                auth()->logout();
                return back()->with('error', 'Veuillez vérifier votre email avant de vous connecter.');
            }
            return redirect()->intended('/products');
        }

        return back()->withErrors(['email' => 'Identifiants incorrects']);
    }

    public function showRegistrationForm()
    {
        return view('frontend.auth.register');
    }

    public function checkPhone(Request $request)
    {
        $request->validate(['phone' => 'required|string']);

        $exists = User::where('phone_number', $request->phone)->exists();

        return response()->json(['available' => !$exists]);
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'country' => 'required|string|size:2',
            'phone_number' => [
                'required',
                'string',
                'unique:users',
                new PhoneNumber('country')
            ]
        ]);

        $user = User::create([
            'full_name' => $validated['full_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'country' => $validated['country'],
            'phone_number' => $this->formatPhoneNumber($validated['phone_number'], $validated['country'])
        ]);

        $this->sendVerificationEmail($user);

        return redirect()->route('login')->with('success', 'Compte créé. Veuillez vérifier votre email.');
    }

    private function validatePhoneForCountry($phone, $countryCode)
    {
        $phoneUtil = \libphonenumber\PhoneNumberUtil::getInstance();

        try {
            // Parse avec le pays comme référence
            $numberProto = $phoneUtil->parse($phone, $countryCode);

            // Valider le numéro
            $isValid = $phoneUtil->isValidNumber($numberProto);

            // Vérifier que le pays correspond
            $phoneRegion = $phoneUtil->getRegionCodeForNumber($numberProto);
            $regionMatches = strtoupper($phoneRegion) === strtoupper($countryCode);

            return [
                'valid' => $isValid && $regionMatches,
                'message' => !$isValid ? 'Numéro de téléphone invalide'
                    : (!$regionMatches ? 'Le numéro ne correspond pas au pays sélectionné' : '')
            ];
        } catch (\Exception $e) {
            return [
                'valid' => false,
                'message' => 'Format de numéro invalide: ' . $e->getMessage()
            ];
        }
    }

    private function formatPhoneNumber($phone, $countryCode)
    {
        $phoneUtil = \libphonenumber\PhoneNumberUtil::getInstance();

        try {
            $numberProto = $phoneUtil->parse($phone, $countryCode);
            return $phoneUtil->format($numberProto, \libphonenumber\PhoneNumberFormat::E164);
        } catch (\Exception $e) {
            return $phone;
        }
    }

    private function sendVerificationEmail(User $user)
    {
        $token = \Str::random(60);

        EmailVerificationToken::create([
            'user_id' => $user->id,
            'token' => $token,
            'expires_at' => now()->addHours(24)
        ]);

        Mail::to($user->email)->send(new EmailVerificationMail($user, $token));
    }

    public function verifyEmail($token)
    {
        $verification = EmailVerificationToken::where('token', $token)
            ->where('expires_at', '>', now())
            ->firstOrFail();

        $user = $verification->user;
        $user->email_verified_at = now();
        $user->save();

        $verification->delete();

        return redirect()->route('login')->with('success', 'Email vérifié. Vous pouvez maintenant vous connecter.');
    }

    public function logout()
    {
        auth()->logout();
        return redirect('/');
    }
}
