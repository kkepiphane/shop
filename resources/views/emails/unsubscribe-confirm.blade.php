<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Désabonnement Newsletter | KPRIMESOFT</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 30px auto;
            padding: 20px;
        }

        .header {
            background-color: #182e5b;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }

        .content {
            padding: 30px;
            border: 1px solid #e5e7eb;
            border-top: none;
        }

        .success {
            color: #28a745;
        }

        .error {
            color: #dc3545;
        }

        .footer {
            margin-top: 20px;
            font-size: 12px;
            color: #6b7280;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Désabonnement Newsletter</h1>
    </div>

    <div class="content">
        @if($success === true)
        <div class="success">
            <p>L'email <strong>{{ $email }}</strong> a bien été désabonné de notre newsletter.</p>
            <p>Vous ne recevrez plus nos communications.</p>
        </div>
        @elseif($success === false)
        <div class="error">
            <p>Nous n'avons pas trouvé l'email <strong>{{ $email }}</strong> dans notre base d'abonnés.</p>
            <p>Il est possible que vous soyez déjà désabonné.</p>
        </div>
        @else
        <form action="{{ route('newsletter.process-unsubscribe') }}" method="POST">
            @csrf
            <input type="hidden" name="email" value="{{ $email }}">
            <p>Confirmez-vous le désabonnement de l'email <strong>{{ $email }}</strong> ?</p>
            <button type="submit" style="background-color: #dc3545; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">
                Confirmer le désabonnement
            </button>
        </form>
        @endif

        <div class="footer">
            <p>© {{ date('Y') }} KPRIMESOFT. Tous droits réservés.</p>
        </div>
    </div>
</body>

</html>