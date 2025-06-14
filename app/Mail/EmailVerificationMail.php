<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailVerificationMail extends Mailable
{
  use Queueable, SerializesModels;

  public $user;
  public $token;
  public $redirectTo;

  public function __construct(User $user, $token, $redirectTo = null)
  {
    $this->user = $user;
    $this->token = $token;
    $this->redirectTo = $redirectTo;
  }

  public function build()
  {
    return $this->subject('Vérification de votre adresse email')
      ->markdown('emails.verify-email')
      ->with([
        'user' => $this->user,
        'verificationUrl' => route('verification.verify', [
          'id' => $this->user->id,
          'token' => $this->token,
          'redirect_to' => $this->redirectTo
        ])
      ]);
  }
}
