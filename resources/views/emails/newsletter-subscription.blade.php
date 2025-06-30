<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Confirmation Newsletter') }} | KPRIMESOFT</title>
    <style>
        /* Styles de base */
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333333;
            background-color: #f5f7fa;
            margin: 0;
            padding: 0;
        }

        /* Conteneur principal */
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 0;
            background-color: #ffffff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        /* En-tête avec logo */
        .header {
            background-color: #182e5b;
            padding: 25px 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }

        .logo {
            max-height: 60px;
            width: auto;
        }

        /* Contenu */
        .content {
            padding: 30px;
            border: 1px solid #e5e7eb;
            border-top: none;
            border-radius: 0 0 8px 8px;
        }

        /* Styles de texte */
        h1 {
            color: #ffffff;
            margin: 15px 0 0 0;
            font-size: 24px;
        }

        h2 {
            color: #182e5b;
            margin-top: 0;
            font-size: 20px;
        }

        p {
            margin: 0 0 15px 0;
        }

        /* Bouton */
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #182e5b;
            color: #f8f9fa !important;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            margin: 20px 0;
        }

        .unsubscribe-link {
            color: #6b7280;
            font-size: 12px;
        }

        /* Pied de page */
        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #6b7280;
            text-align: center;
            padding: 15px;
            border-top: 1px solid #e5e7eb;
        }

        /* Responsive */
        @media only screen and (max-width: 600px) {
            .content {
                padding: 20px;
            }
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="header">
            <img src="{{ config('app.url') }}/theme/assets/imgs/kprimesoft-logo.png" alt="KPRIMESOFT Logo" class="logo">
            <h1>{{ __('Confirmation Newsletter') }}</h1>
        </div>

        <div class="content">
            <p>{{ __('Bonjour') }},</p>

            <p>{{ __('Merci de vous être abonné à notre newsletter. Nous sommes ravis de vous compter parmi nos abonnés !') }}</p>

            <h2>{{ __('Votre inscription est confirmée') }}</h2>

            <p>{{ __('Email enregistré') }} : <strong>{{ $subscriber->email }}</strong></p>

            <p>{{ __('Vous recevrez désormais nos dernières actualités, offres spéciales et informations exclusives.') }}</p>

            <a href="{{ config('app.url') }}" class="button">{{ __('Visiter notre site') }}</a>

            <p class="unsubscribe-link">
                {{ __('Si vous ne souhaitez plus recevoir nos emails, vous pouvez vous') }}
                <a href="{{ route('newsletter.unsubscribe', ['email' => $subscriber->email]) }}" style="color: #182e5b;">
                    {{ __('désabonner ici') }}
                </a>.
            </p>

            <p>{{ __('Cordialement') }},<br>{{ __('L\'équipe KPRIMESOFT') }}</p>
        </div>

        <div class="footer">
            <p>
                <a href="{{ config('app.url') }}/privacy" style="color: #6b7280; text-decoration: none; margin: 0 10px;">
                    {{ __('Confidentialité') }}
                </a>
                <a href="{{ config('app.url') }}/contact" style="color: #6b7280; text-decoration: none; margin: 0 10px;">
                    {{ __('Contact') }}
                </a>
            </p>
            <p>© {{ date('Y') }} KPRIMESOFT. {{ __('Tous droits réservés') }}.</p>
        </div>
    </div>
</body>

</html>