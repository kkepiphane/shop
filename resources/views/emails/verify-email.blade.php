<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Vérification de votre email - Mini E-commerce</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      line-height: 1.6;
      color: #333;
      max-width: 600px;
      margin: 0 auto;
      padding: 20px;
    }

    .button {
      display: inline-block;
      padding: 12px 24px;
      background-color: #4CAF50;
      color: white;
      text-decoration: none;
      border-radius: 5px;
      font-weight: bold;
      margin: 15px 0;
    }

    .footer {
      margin-top: 30px;
      font-size: 0.9em;
      color: #777;
    }
  </style>
</head>

<body>
  <h1>Bonjour {{ $user->full_name }},</h1>

  <p>Merci de vous être inscrit sur Mini E-commerce. Pour finaliser votre inscription, veuillez vérifier votre adresse email en cliquant sur le bouton ci-dessous :</p>

  <div style="text-align: center; margin: 25px 0;">
    <a href="{{ $verificationUrl }}" class="button">
      Vérifier mon email
    </a>
  </div>

  <p>Ce lien expirera dans 24 heures. Si vous ne parvenez pas à cliquer sur le bouton, copiez et collez le lien suivant dans votre navigateur :</p>

  <div style="word-break: break-all; background-color: #f5f5f5; padding: 10px; border-radius: 5px; margin: 15px 0;">
    {{ $verificationUrl }}
  </div>

  <p>Si vous n'avez pas créé de compte sur notre plateforme, vous pouvez ignorer cet email en toute sécurité.</p>

  <div class="footer">
    <p>Cordialement,<br>L'équipe <strong>Mini E-commerce</strong></p>
    <p>
      <small>
        © {{ date('Y') }} Mini E-commerce. Tous droits réservés.<br>
      </small>
    </p>
  </div>
</body>

</html>