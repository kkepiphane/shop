@extends('app')

@section('content')

<link href="{{ asset('assets/css/checkout.css') }}" rel="stylesheet">
<!-- Start Hero Section -->
<div class="hero">
  <div class="container">
    <div class="row justify-content-between">
      <div class="col-lg-5">
        <div class="intro-excerpt">
          <h1>Checkout</h1>
        </div>
      </div>
      <div class="col-lg-7">

      </div>
    </div>
  </div>
</div>
<!-- End Hero Section -->

<div class="untree_co-section">
  <div class="container">
    <form method="POST" action="{{ route('checkout.process') }}" id="registerForm">
      <div class="row">
        <div class="col-md-6 mb-5 mb-md-0">
          <h2 class="h3 mb-3 text-black">Détails de la livraison</h2>
          <div class="p-3 p-lg-5 border bg-white">
            @csrf
            <div class="form-group">
              <label for="country">Pays</label>
              <div class="country-select-wrapper">
                <div class="selected-flag">
                  <span id="selectedCountryFlag">
                    @if($userData['country'])
                    <img src="https://flagcdn.com/{{ strtolower($userData['country']) }}.svg" alt="Flag" class="flag-icon">
                    @endif
                  </span>
                </div>
                <select id="country" class="form-control" name="country" required>
                  <option value="">Sélectionnez votre pays</option>
                  <!-- Les options seront chargées dynamiquement par JavaScript -->
                </select>
              </div>
            </div>

            <div class="form-group">
              <label for="phone">Numéro de téléphone</label>
              <div class="phone-group">
                <div class="phone-prefix-container">
                  <span id="phonePrefixFlag" class="flag-icon">
                    @if($userData['country'])
                    <img src="https://flagcdn.com/{{ strtolower($userData['country']) }}.svg" alt="Flag" class="flag-icon">
                    @endif
                  </span>
                  <input type="text" id="phonePrefix" class="phone-prefix" value="" readonly>
                </div>
                <input type="tel" id="phone" name="phone" class="form-control phone-number"
                  value="{{ $userData['phone'] ?? '' }}" required>
              </div>
              <div id="phoneError" class="error-message"></div>
            </div>

            <div class="form-group">
              <label for="c_address">Adresse de livraison (complément)</label>
              <input type="text" class="form-control" id="c_address" name="c_address" value="{{ $userData['c_address'] ?? '' }}" required>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="row mb-5">
            <div class="col-md-12">
              <h2 class="h3 mb-3 text-black">Votre commande</h2>
              <div class="p-3 p-lg-5 border bg-white">
                <table class="table site-block-order-table mb-5">
                  <thead>
                    <th>Product</th>
                    <th>Total</th>
                  </thead>
                  <tbody>

                    @foreach($cartItems as $item)
                    <tr>
                      <td class="product-name">
                        {{ $item->name }} <strong class="mx-2">x</strong> {{ $item->quantity }}
                      </td>
                      <td>{{ $item->price * $item->quantity }} FCFA</td>
                    </tr>
                    @endforeach
                    <tr>
                      <td class="text-black font-weight-bold"><strong>Cart Subtotal</strong></td>
                      <td class="text-black">{{ \Cart::getSubTotal() }} FCFA</td>
                    </tr>
                    <tr>
                      <td class="text-black font-weight-bold"><strong>Commande Total</strong></td>
                      <td class="text-black font-weight-bold"><strong>{{ \Cart::getTotal() }} FCFA</strong></td>
                    </tr>
                  </tbody>
                </table>

                <div class="form-group">
                  <button type="submit" class="btn btn-black btn-lg py-3 btn-block">Payer</button>
                </div>

              </div>
            </div>
          </div>

        </div>
      </div>
    </form>
    <!-- </form> -->
  </div>
</div>

@endsection
@push('scripts')
@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/libphonenumber-js/1.10.6/libphonenumber-js.min.js"></script>
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
          option.textContent = country.name.common;
          option.dataset.phoneCode = country.idd.root + (country.idd.suffixes?.length === 1 ? country.idd.suffixes[0] : '');
          option.dataset.flag = country.flags.svg || country.flags.png;

          // Sélectionner le pays de l'utilisateur s'il existe
          if (country.cca2 === "{{ $userData['country'] ?? '' }}") {
            option.selected = true;
            phonePrefix.value = option.dataset.phoneCode;
            phonePrefixFlag.innerHTML = `<img src="${option.dataset.flag}" alt="Flag" class="flag-icon">`;
            selectedFlag.innerHTML = `<img src="${option.dataset.flag}" alt="Flag" class="flag-icon">`;
          }

          countrySelect.appendChild(option);
        });

        // Gérer le changement de sélection
        countrySelect.addEventListener('change', function() {
          const selectedOption = this.options[this.selectedIndex];
          const flagUrl = selectedOption.dataset.flag;
          const phoneCode = selectedOption.dataset.phoneCode;

          selectedFlag.innerHTML = flagUrl ? `<img src="${flagUrl}" alt="Flag" class="flag-icon">` : '';
          phonePrefixFlag.innerHTML = flagUrl ? `<img src="${flagUrl}" alt="Flag" class="flag-icon">` : '';
          phonePrefix.value = phoneCode || '';
        });

        // Validation du numéro de téléphone
        document.getElementById('phone').addEventListener('input', validatePhoneNumber);
        document.getElementById('country').addEventListener('change', validatePhoneNumber);
      })
      .catch(error => {
        console.error('Erreur:', error);
        loadBackupCountries();
      });

    document.getElementById('registerForm').addEventListener('submit', function(e) {
      e.preventDefault();

      if (validateForm()) {
        // Afficher un loader
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Traitement...';

        // Soumettre le formulaire
        this.submit();
      }
    });

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

      // Valider l'adresse
      const address = document.getElementById('c_address').value.trim();
      if (!address) {
        document.getElementById('addressError').style.display = 'block';
        isValid = false;
      } else {
        document.getElementById('addressError').style.display = 'none';
      }

      // Valider le numéro de téléphone
      if (!validatePhoneNumber()) {
        isValid = false;
      }

      return isValid;
    }

    function validatePhoneNumber() {
      const phoneInput = document.getElementById('phone');
      const countryCode = document.getElementById('country').value;
      const phonePrefix = document.getElementById('phonePrefix').value;
      const phoneError = document.getElementById('phoneError');

      if (!phoneInput.value) {
        phoneError.textContent = 'Veuillez entrer un numéro de téléphone';
        phoneError.style.display = 'block';
        return false;
      }

      try {
        const phoneNumber = libphonenumber.parsePhoneNumber(phonePrefix + phoneInput.value, countryCode);
        if (phoneNumber && phoneNumber.isValid()) {
          phoneError.style.display = 'none';
          return true;
        } else {
          phoneError.textContent = 'Numéro invalide pour ce pays';
          phoneError.style.display = 'block';
          return false;
        }
      } catch (error) {
        phoneError.textContent = 'Format de numéro invalide';
        phoneError.style.display = 'block';
        return false;
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
            suffixes: ['']
          },
          flags: {
            svg: 'https://flagcdn.com/tg.svg'
          }
        },
        {
          cca2: 'FR',
          name: {
            common: 'France'
          },
          idd: {
            root: '+33',
            suffixes: ['']
          },
          flags: {
            svg: 'https://flagcdn.com/fr.svg'
          }
        }
      ];

      const countrySelect = document.getElementById('country');
      backupCountries.forEach(country => {
        const option = document.createElement('option');
        option.value = country.cca2;
        option.textContent = country.name.common;
        option.dataset.phoneCode = country.idd.root + (country.idd.suffixes?.length === 1 ? country.idd.suffixes[0] : '');
        option.dataset.flag = country.flags.svg;
        countrySelect.appendChild(option);
      });
    }
  });
</script>
@endpush
@endpush