<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Connexion - Mini E-commerce</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link href="{{ asset('assets/css/login.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/css/bootstrap.min.css')}}" rel="stylesheet">
  <style>
    .alert-top-left {
      position: fixed;
      top: 10px;
      right: 10px;
      width: auto;
      max-width: 600px;
    }
  </style>
</head>

<body>
  <div class="login-container">
    <h1>Connexion</h1>

    <form id="loginForm">
      @if(session('redirect_to'))
      <input type="hidden" name="redirect_to" value="{{ session('redirect_to') }}">
      @else
      <input type="hidden" name="redirect_to" value="{{ url()->previous() }}">
      @endif

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
      <p>Pas encore de compte ?
        <a href="{{ route('register') }}?redirect_to={{ request('redirect_to', session('redirect_to', url()->previous())) }}">
          Créer un compte
        </a>
      </p>
    </div>
  </div>

  <script src="{{ asset('assets/js/bootstrap.bundle.min.js')}}"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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

                const alertHTML = `
                  <div class="alert alert-danger alert-dismissible fade show alert-top-left" role="alert">
                    <strong>Erreur!</strong> ${error.errors.email[0]}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>
                `;

                $('body').append(alertHTML);
                setTimeout(function() {
                  $('.alert-top-left').alert('close');
                }, 5000);
              }
              if (error.errors.password) {
                const alertHTML = `
                  <div class="alert alert-danger alert-dismissible fade show alert-top-left" role="alert">
                    <strong>Erreur!</strong> ${error.errors.password[0]}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>
                `;

                $('body').append(alertHTML);
                setTimeout(function() {
                  $('.alert-top-left').alert('close');
                }, 5000);
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