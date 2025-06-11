<!DOCTYPE html>
<html>
<head>
    <title>Vérification de votre email</title>
</head>
<body>
    <h1>Bonjour {{ $user->full_name }},</h1>
    <p>Merci de vous être inscrit sur notre plateforme. Veuillez cliquer sur le lien ci-dessous pour vérifier votre adresse email :</p>
    
    <a href="{{ $verificationUrl }}" style="display: inline-block; padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px;">
        Vérifier mon email
    </a>

    <p>Si vous n'avez pas créé de compte, vous pouvez ignorer cet email.</p>
    
    <p>Cordialement,<br>L'équipe Mini E-commerce</p>
</body>
</html>