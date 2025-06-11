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

    public function __construct(User $user, $token)
    {
        $this->user = $user;
        $this->token = $token;
    }

    public function build()
    {
        return $this->subject('Vérification de votre adresse email')
            ->view('emails.verify-email')
            ->with([
                'user' => $this->user,
                'verificationUrl' => route('verification.verify', [
                    'id' => $this->user->id,
                    'token' => $this->token
                ])
            ]);
    }
}
