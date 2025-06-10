<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Mini E-commerce</title>
    <link rel="stylesheet" href="style.css">

    <link href="{{ asset('assets/css/login.css') }}" rel="stylesheet">
</head>

<body>
    <div class="login-container">
        <h1>Connexion</h1>

        <form id="loginForm">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
                <div id="emailError" class="error-message">Veuillez entrer une adresse email valide</div>
            </div>

            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" required>
                <div id="passwordError" class="error-message">Mot de passe incorrect</div>
            </div>

            <button type="submit">Se connecter</button>
        </form>

        <div class="links">
            <a href="forgot-password.html">Mot de passe oublié ?</a> |
            <a href="register.html">Créer un compte</a>
        </div>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();

            // Réinitialiser les messages d'erreur
            document.getElementById('emailError').style.display = 'none';
            document.getElementById('passwordError').style.display = 'none';

            // Récupérer les valeurs
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            // Validation basique côté client
            let isValid = true;

            // Vérifier l'email
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                document.getElementById('emailError').style.display = 'block';
                isValid = false;
            }

            // Vérifier le mot de passe (au moins 6 caractères)
            if (password.length < 6) {
                document.getElementById('passwordError').style.display = 'block';
                isValid = false;
            }

            // Si le formulaire est valide, envoyer les données au serveur
            if (isValid) {
                // Ici, vous feriez une requête AJAX vers votre backend
                // pour vérifier les identifiants
                console.log('Envoi des données au serveur:', {
                    email,
                    password
                });

                // Exemple de réponse du serveur (à remplacer par une vraie requête)
                // simulateServerResponse(email, password);
            }
        });

        // Fonction de simulation de réponse du serveur
        function simulateServerResponse(email, password) {
            // Ceci est une simulation - en réalité, vous feriez une requête AJAX
            setTimeout(() => {
                // Simuler une réponse d'erreur pour les identifiants incorrects
                if (password !== "correctpassword") {
                    document.getElementById('passwordError').style.display = 'block';
                    document.getElementById('passwordError').textContent = 'Email ou mot de passe incorrect';
                } else {
                    // Redirection si la connexion réussit
                    window.location.href = 'products.html';
                }
            }, 1000);
        }
    </script>
</body>

</html>