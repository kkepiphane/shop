<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Création de compte - Mini E-commerce</title>
  <link href="{{ asset('assets/css/bootstrap.min.css')}}" rel="stylesheet">
  <link href="{{ asset('assets/css/register.css') }}" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <style>
    .alert-top-left {
      position: fixed;
      top: 10px;
      right: 10px;
      width: auto;
      max-width: 500px;
    }
  </style>
</head>

<body>
  <div class="register-container">
    <h1>Création de compte</h1>

    <div id="successMessage" class="success-message" style="display:none;">
      Un email de confirmation a été envoyé à votre adresse. Veuillez vérifier votre boîte mail.
    </div>

    <form id="registerForm">
      <input type="hidden" name="redirect_to" value="{{ $redirect_to }}">
      <div class="form-group">
        <label for="fullname">Nom complet</label>
        <input type="text" id="fullname" name="fullname" required>
        <div id="fullnameError" class="error-message">Veuillez entrer votre nom complet</div>
      </div>

      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required>
        <div id="emailError" class="error-message"></div>
      </div>

      <div class="form-group">
        <label for="country">Pays</label>
        <div class="country-select-wrapper">
          <div class="selected-flag">
            <span id="selectedCountryFlag"></span>
          </div>
          <select id="country" name="country" required>
            <option value="">Sélectionnez votre pays</option>
            <!-- Les options seront chargées dynamiquement -->
          </select>
        </div>
        <div id="countryError" class="error-message">Veuillez sélectionner votre pays</div>
      </div>

      <div class="form-group">
        <label for="phone">Numéro de téléphone</label>
        <div class="phone-group">
          <div class="phone-prefix-container">
            <span id="phonePrefixFlag" class="flag-icon"></span>
            <input type="text" id="phonePrefix" class="phone-prefix" value="" readonly>
          </div>
          <input type="tel" id="phone" name="phone" class="phone-number" required>
        </div>
        <div id="phoneError" class="error-message"></div>
        <small id="phoneFormatHint" class="format-hint"></small>
      </div>

      <div class="form-group">
        <label for="password">Mot de passe</label>
        <input type="password" id="password" name="password" required>
        <div id="passwordError" class="error-message">Le mot de passe doit contenir au moins 8 caractères</div>
      </div>

      <div class="form-group">
        <label for="confirmPassword">Confirmer le mot de passe</label>
        <input type="password" id="confirmPassword" name="confirmPassword" required>
        <div id="confirmPasswordError" class="error-message">Les mots de passe ne correspondent pas</div>
      </div>

      <button type="submit">Créer mon compte</button>
    </form>

    <div class="links">
      <a href="{{ route('login') }}">Déjà un compte ? Se connecter</a>
    </div>
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/libphonenumber-js/1.10.6/libphonenumber-js.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


  <script src="{{ asset('assets/js/bootstrap.bundle.min.js')}}"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      fetch('https://restcountries.com/v3.1/all?fields=cca2,name,idd,flags')
        .then(response => response.json())
        .then(data => {
          const countrySelect = document.getElementById('country');
          const selectedFlag = document.getElementById('selectedCountryFlag');
          const phonePrefixFlag = document.getElementById('phonePrefixFlag');
          const phonePrefix = document.getElementById('phonePrefix');

          // Trier les pays par nom commun
          data.sort((a, b) => a.name.common.localeCompare(b.name.common));

          data.forEach(country => {
            const option = document.createElement('option');
            option.value = country.cca2;

            // Utiliser le SVG si disponible, sinon le PNG
            const flagUrl = country.flags.svg || country.flags.png;
            option.innerHTML = `
                    <img src="${flagUrl}" alt="${country.name.common}" class="flag-icon">
                    ${country.name.common}
                `;

            option.dataset.phoneCode = country.idd.root + (country.idd.suffixes?.length === 1 ? country.idd.suffixes[0] : '');
            option.dataset.flag = flagUrl;
            countrySelect.appendChild(option);
          });

          // Gérer le changement de sélection
          countrySelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const flagUrl = selectedOption.dataset.flag;
            const phoneCode = selectedOption.dataset.phoneCode;

            selectedFlag.innerHTML = `<img src="${flagUrl}" alt="Flag" class="flag-icon">`;

            phonePrefixFlag.innerHTML = `<img src="${flagUrl}" alt="Flag" class="flag-icon">`;
            phonePrefix.value = phoneCode;
          });

          // Détection automatique du pays
          detectUserCountry();

          // Gestion de la soumission du formulaire
          document.getElementById('registerForm').addEventListener('submit', function(e) {
            e.preventDefault();
            if (validateForm()) {

              const submitBtn = this.querySelector('button[type="submit"]');
              submitBtn.disabled = true;
              submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Traitement...';


              submitForm();
            }
          });

          // Validation du numéro de téléphone en temps réel
          document.getElementById('phone').addEventListener('input', function() {
            validatePhoneNumber();
          });

          // Lorsque le pays change, valider à nouveau le numéro
          document.getElementById('country').addEventListener('change', function() {
            validatePhoneNumber();
          });
        })
        .catch(error => {
          console.error('Erreur:', error);
          loadBackupCountries();
        });
    });


    function detectUserCountry() {
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
          position => {
            fetch(`https://api.bigdatacloud.net/data/reverse-geocode-client?latitude=${position.coords.latitude}&longitude=${position.coords.longitude}&localityLanguage=fr`)
              .then(response => response.json())
              .then(data => {
                const countryCode = data.countryCode;
                if (countryCode) {
                  const countrySelect = document.getElementById('country');
                  countrySelect.value = countryCode;
                  countrySelect.dispatchEvent(new Event('change'));
                }
              });
          },
          error => {
            console.log('Geolocation error:', error);
            detectCountryByIP();
          }
        );
      } else {
        detectCountryByIP();
      }
    }

    function loadBackupCountries() {
      const backupCountries = [{
          cca2: 'TG',
          name: {
            common: 'Togo'
          },
          idd: {
            root: '+228',
            suffixes: ['228']
          },
          flags: {
            svg: 'https://flagcdn.com/tg.svg'
          }
        },

      ];

      const countrySelect = document.getElementById('country');
      backupCountries.forEach(country => {
        const option = document.createElement('option');
        option.value = country.cca2;
        option.innerHTML = `<img src="${country.flags.svg}" alt="${country.name.common}" class="flag-icon"> ${country.name.common}`;
        option.dataset.phoneCode = country.idd.root + country.idd.suffixes[0];
        option.dataset.flag = country.flags.svg;
        countrySelect.appendChild(option);
      });
    }

    function validateForm() {
      let isValid = true;

      // Valider le nom complet
      const fullname = document.getElementById('fullname').value.trim();
      if (!fullname) {
        document.getElementById('fullnameError').style.display = 'block';
        isValid = false;
      } else {
        document.getElementById('fullnameError').style.display = 'none';
      }

      // Valider l'email
      const email = document.getElementById('email').value.trim();
      if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        document.getElementById('emailError').textContent = 'Veuillez entrer une adresse email valide';
        document.getElementById('emailError').style.display = 'block';
        isValid = false;
      } else {
        document.getElementById('emailError').style.display = 'none';
      }

      // Valider le pays
      const country = document.getElementById('country').value;
      if (!country) {
        document.getElementById('countryError').style.display = 'block';
        isValid = false;
      } else {
        document.getElementById('countryError').style.display = 'none';
      }

      // Valider le numéro de téléphone
      if (!validatePhoneNumber()) {
        isValid = false;
      }

      // Valider le mot de passe
      const password = document.getElementById('password').value;
      if (password.length < 8) {
        document.getElementById('passwordError').style.display = 'block';
        isValid = false;
      } else {
        document.getElementById('passwordError').style.display = 'none';
      }

      // Valider la confirmation du mot de passe
      const confirmPassword = document.getElementById('confirmPassword').value;
      if (password !== confirmPassword) {
        document.getElementById('confirmPasswordError').style.display = 'block';
        isValid = false;
      } else {
        document.getElementById('confirmPasswordError').style.display = 'none';
      }

      return isValid;
    }

    function validatePhoneNumber() {
      const phoneNumberInput = document.getElementById('phone').value.trim();
      const countryCode = document.getElementById('country').value;
      const phonePrefix = document.getElementById('phonePrefix').value.trim();
      const phoneError = document.getElementById('phoneError');

      if (!phoneNumberInput) {
        phoneError.textContent = 'Veuillez entrer un numéro de téléphone';
        phoneError.style.display = 'block';
        return false;
      }

      if (!countryCode) {
        phoneError.textContent = 'Veuillez d\'abord sélectionner un pays';
        phoneError.style.display = 'block';
        return false;
      }

      // Combiner le préfixe et le numéro saisi
      const fullPhoneNumber = phonePrefix + phoneNumberInput;

      try {
        const phoneNumber = libphonenumber.parsePhoneNumber(fullPhoneNumber, countryCode);

        if (phoneNumber && phoneNumber.isValid()) {
          // Afficher le numéro formaté (sans répéter le préfixe)
          document.getElementById('phone').value = phoneNumber.nationalNumber;
          phoneError.style.display = 'none';
          return true;
        } else {
          phoneError.textContent = 'Numéro de téléphone invalide pour ce pays';
          phoneError.style.display = 'block';
          return false;
        }
      } catch (error) {
        phoneError.textContent = 'Format de numéro invalide';
        phoneError.style.display = 'block';
        return false;
      }
    }



    function submitForm() {
      if (!validateForm()) return;

      const formData = {
        full_name: document.getElementById('fullname').value,
        email: document.getElementById('email').value,
        country: document.getElementById('country').value,
        phone_number: document.getElementById('phone').value,
        phone_prefix: document.getElementById('phonePrefix').value,
        password: document.getElementById('password').value,
        redirect_to: document.querySelector('input[name="redirect_to"]').value

      };

      country = document.getElementById('country').value,
        console.log(country)

      fetch('/register', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
          },
          body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            document.getElementById('registerForm').style.display = 'none';
            document.getElementById('successMessage').style.display = 'block';
          } else {
            console.error(data.errors);
            let errorMessage = "";
            for (let key in data.errors) {
              if (data.errors.hasOwnProperty(key)) {
                errorMessage += data.errors[key][0] + "\n";
              }
            }

            const alertHTML = `
              <div class="alert alert-danger alert-dismissible fade show alert-top-left" role="alert">
                <strong>Erreur!</strong> ${errorMessage}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
            `;

            $('body').append(alertHTML);
            setTimeout(function() {
              $('.alert-top-left').alert('close');
            }, 5000);
            const submitBtn = document.querySelector('#registerForm button[type="submit"]');
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Créer mon compte';

          }
        })
        .catch(error => {
          console.error('Error:', error);
        });
    }
  </script>
</body>

</html>