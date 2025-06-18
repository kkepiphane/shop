<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\EmailVerificationToken;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailVerificationMail;
use Illuminate\Support\Facades\Auth;

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

        return response()->json([
          'errors' => [
            'email' => ["Votre compte n’est pas encore activé. Veuillez cliquer sur le lien d’activation que vous avez reçu par email avant de vous connecter."]
          ]
        ], 422);
      }

      $redirectTo = $request->session()->pull('redirect_to', $request->input('redirect_to', url('/')));

      return response()->json([
        'redirect' => $redirectTo
      ]);
    }

    return response()->json([
      'errors' => [
        'email' => ['Identifiants incorrects']
      ]
    ], 422);
  }

  public function showRegistrationForm(Request $request)
  {
    return view('frontend.auth.register', [
      'redirect_to' => $request->input('redirect_to', url()->previous())
    ]);
  }


  public function register(Request $request)
  {
    $validated = $request->validate([
      'full_name' => 'required|string|max:255',
      'email' => 'required|string|email|max:255|unique:users',
      'password' => 'required|string|min:8',
      'country' => 'required|string|size:2',
      'phone_number' => 'required|string|unique:users',
      'redirect_to' => 'nullable|url'
    ], [
      'email.unique' => 'L\'adresse email est déjà utilisée.',
      'phone_number.unique' => 'Le numéro de téléphone est déjà utilisé.'
    ]);
    

    $user = User::create([
      'full_name' => $validated['full_name'],
      'email' => $validated['email'],
      'password' => Hash::make($validated['password']),
      'country' => $validated['country'],
      'phone_number' => $validated['phone_number']
    ]);

    $redirectTo = $validated['redirect_to'] ?? $request->session()->get('redirect_to', url()->previous());
    $this->sendVerificationEmail($user, $redirectTo);

    return response()->json([
      'success' => true,
      'message' => 'Nous vous avons envoyé un email de confirmation. Merci de vérifier votre messagerie afin de valider votre adresse.'
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


    $redirectTo = $verificationToken->redirect_to ?? route('home');
    $verificationToken->delete();

    Auth::login($user);

    return redirect($redirectTo)->with('success', 'Votre compte a été activé avec succès !');
  }


  private function sendVerificationEmail(User $user, $redirectTo = null)
  {
    $token = \Str::random(60);

    EmailVerificationToken::create([
      'user_id' => $user->id,
      'token' => $token,
      'expires_at' => now()->addHours(24),
      'redirect_to' => $redirectTo
    ]);

    Mail::to($user->email)->send(new EmailVerificationMail($user, $token));
  }


  public function logout(Request $request)
  {
    auth()->logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/')->with('status', 'Vous avez été déconnecté avec succès.');
  }
}
