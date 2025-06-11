<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Mini E-commerce</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
            <a href="{{ route('register') }}">Créer un compte</a>
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

            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                document.getElementById('emailError').style.display = 'block';
                isValid = false;
            }

            if (password.length < 6) {
                document.getElementById('passwordError').style.display = 'block';
                isValid = false;
            }

            if (isValid) {
                fetch('/login', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            email,
                            password
                        })
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => {
                                throw err;
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        }
                    })
                    .catch(error => {
                        if (error.errors) {
                            if (error.errors.email) {
                                document.getElementById('emailError').textContent = error.errors.email[0];
                                document.getElementById('emailError').style.display = 'block';
                            }
                            if (error.errors.password) {
                                document.getElementById('passwordError').textContent = error.errors.password[0];
                                document.getElementById('passwordError').style.display = 'block';
                            }
                        } else {
                            document.getElementById('passwordError').textContent = 'Une erreur est survenue';
                            document.getElementById('passwordError').style.display = 'block';
                        }
                    });
            }
        });
    </script>
</body>

</html>