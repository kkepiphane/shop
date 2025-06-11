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
            return redirect()->intended('/home');
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
            'password' => 'required|string|min:8',
            'country' => 'required|string|size:2',
            'phone_prefix' => 'required|string',
            'phone_number' => [
                'required',
                'string',
                'unique:users',
                new PhoneNumber('country')
            ]
        ]);
        $fullPhoneNumber = $validated['phone_prefix'] . $validated['phone_number'];

        $user = User::create([
            'full_name' => $validated['full_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'country' => $validated['country'],
            'phone_number' => $fullPhoneNumber
        ]);

        $this->sendVerificationEmail($user);

        return response()->json([
            'success' => true,
            'message' => 'Un email de confirmation a été envoyé à votre adresse. Veuillez vérifier votre boîte mail.'
        ]);
    }

    public function verifyEmail($id, $token)
    {
        $user = User::findOrFail($id);

        $verificationToken = EmailVerificationToken::where('user_id', $user->id)
            ->where('token', $token)
            ->where('expires_at', '>', now())
            ->first();

        if (!$verificationToken) {
            return redirect()->route('login')->with('error', 'Lien de vérification invalide ou expiré.');
        }

        $user->email_verified_at = now();
        $user->save();

        $verificationToken->delete();

        return redirect()->route('login')->with('success', 'Votre email a été vérifié avec succès. Vous pouvez maintenant vous connecter.');
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


    public function logout()
    {
        auth()->logout();
        return redirect('/');
    }
}
