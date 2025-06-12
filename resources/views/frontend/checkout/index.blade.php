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
                                    <span id="selectedCountryFlag"></span>
                                </div>
                                <select id="country" class="form-control" name="country" required>
                                    <option value="">Sélectionnez votre pays</option>
                                    <!-- Les options seront chargées dynamiquement -->
                                </select>
                            </div>
                            <div id="countryError" class="error-message">Veuillez sélectionner votre pays</div>
                        </div>
                        <div class="form-group row">
                            <div class="form-group">
                                <label for="fullname">Nom complet</label>
                                <input type="text" id="fullname" name="fullname" class="form-control" value="{{ $userData['fullname'] ?? '' }}" required>
                                <div id="fullnameError" class="error-message">Veuillez entrer votre nom complet</div>
                            </div>
                        </div>

                    <div class="form-group row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="c_address" class="text-black">Adresse <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="c_address" name="c_address" placeholder="Street address">
                                <div id="addressError" class="error-message"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="c_address" class="text-black">Adresse de livraison <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" placeholder="Apartment, suite, unit etc. (optional)">

                        </div>
                    </div>

                    <div class="form-group row mb-5">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="email">Email</label><span class="text-danger">*</span>
                                <input type="email" id="email" name="email" value="{{ $userData['email'] ?? '' }}" class="form-control" required>
                                <div id="emailError" class="error-message"></div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="phone">Numéro de téléphone</label><span class="text-danger">*</span>
                                <div class="phone-group">
                                    <div class="phone-prefix-container">
                                        <span id="phonePrefixFlag" class="flag-icon flag-c"></span>
                                        <input type="text" id="phonePrefix" class="phone-prefix" value="{{ $userData['phone'] ?? '' }}" readonly>
                                    </div>
                                    <input type="tel" id="phone" name="phone" class="phone-number" required>
                                </div>
                                <div id="phoneError" class="error-message"></div>
                                <small id="phoneFormatHint" class="format-hint"></small>
                            </div>
                        </div>
                    </div>

                    @if(auth()->check())
                    <div class="alert alert-info">
                        Vous êtes connecté en tant que {{ auth()->user()->full_name }}.
                    </div>
                    @else
                    <div class="form-group">
                        <label for="c_create_account" class="text-black" data-bs-toggle="collapse" href="#create_an_account" role="button" aria-expanded="false" aria-controls="create_an_account">
                            <input type="checkbox" value="1" id="c_create_account"> Créer un compte ?
                        </label>
                        <div class="collapse" id="create_an_account">
                            <div class="py-2 mb-4">
                                <p class="mb-3">Créez un compte en saisissant les informations ci-dessous.</p>
                                <div class="form-group">
                                    <label for="password" class="text-black">Mot de passe</label>
                                    <input type="password" class="form-control" id="password" name="password" placeholder="">
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
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
                // document.getElementById('registerForm').addEventListener('submit', function(e) {
                //     e.preventDefault();
                //     if (validateForm()) {
                //         // Si la validation réussit, vous pouvez envoyer le formulaire
                //         submitForm();
                //     }
                // });

                // Validation du numéro de téléphone en temps réel
                document.getElementById('phone').addEventListener('input', function() {
                    validatePhoneNumber();
                });

                // Lorsque le pays change, valider à nouveau le numéro
                document.getElementById('country').addEventListener('change', function() {
                    validatePhoneNumber();
                    updatePhoneFormatHint();
                });
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

    const phoneFormats = {
        'TG': {
            pattern: '[279]\\d{7}',
            format: '(\\d{2})(\\d{2})(\\d{2})(\\d{2})',
            example: '90 12 34 56',
            length: 8
        },
        'BJ': {
            pattern: '(?:[25689]\\d|40)\\d{6}',
            format: '(\\d{2})(\\d{2})(\\d{2})(\\d{2})',
            example: '96 12 34 56',
            length: 8
        },
        'CM': {
            pattern: '[26]\\d{8}|88\\d{6,7}',
            formats: [{
                    pattern: '88\\d{6}',
                    format: '(\\d{2})(\\d{2})(\\d{2})(\\d{2})',
                    example: '88 12 34 56',
                    length: 8
                },
                {
                    pattern: '[26]\\d{8}',
                    format: '(\\d)(\\d{2})(\\d{2})(\\d{2})(\\d{2})',
                    example: '6 12 34 56 78',
                    length: 9
                }
            ]
        },
        'CN': {
            pattern: '1[127]\\d{8,9}|2\\d{9}(?:\\d{2})?|[12]\\d{6,7}|86\\d{6}|(?:1[03-689]\\d|6)\\d{7,9}|(?:[3-579]\\d|8[0-57-9])\\d{6,9}',
            format: '(\\d{3})(\\d{4})(\\d{4})',
            example: '131 2345 6789',
            length: 11
        }
        // Vous pouvez ajouter tous les autres pays ici en suivant le même format
    };

    function updatePhoneFormatHint() {
        const countryCode = document.getElementById('country').value;
        const hintElement = document.getElementById('phoneFormatHint');

        if (!countryCode) {
            hintElement.textContent = '';
            return;
        }

        // Exemples de formats nationaux (sans l'indicatif)
        const examples = {
            'FR': 'ex: 6 12 34 56 78',
            'US': 'ex: (201) 555-0123',
            'GB': 'ex: 7400 123456',
            'DE': 'ex: 171 1234567',
            'TG': 'ex: 90 12 34 56',
            'BE': 'ex: 470 12 34 56',
            // Ajoutez d'autres pays selon vos besoins
        };

        // const format = phoneFormats[countryCode];
        // hintElement.textContent = `le numéro de téléphone comporte: ${format.example} (${format.length} chiffres)`;
    }

    // function submitForm() {
    //     if (!validateForm()) return;

    //     const formData = {
    //         full_name: document.getElementById('fullname').value,
    //         email: document.getElementById('email').value,
    //         country: document.getElementById('country').value,
    //         phone_number: document.getElementById('phone').value,
    //         phone_prefix: document.getElementById('phonePrefix').value,
    //         password: document.getElementById('password').value,

    //     };

    //     country = document.getElementById('country').value,
    //         console.log(country)

    //     fetch('/register', {
    //             method: 'POST',
    //             headers: {
    //                 'Content-Type': 'application/json',
    //                 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
    //                 'Accept': 'application/json'
    //             },
    //             body: JSON.stringify(formData)
    //         })
    //         .then(response => response.json())
    //         .then(data => {
    //             if (data.success) {
    //                 document.getElementById('registerForm').style.display = 'none';
    //                 document.getElementById('successMessage').style.display = 'block';
    //             } else {
    //                 console.error(data.errors);
    //                 let errorMessage = "";
    //                 for (let key in data.errors) {
    //                     if (data.errors.hasOwnProperty(key)) {
    //                         errorMessage += data.errors[key][0] + "\n";
    //                     }
    //                 }
    //                 Swal.fire({
    //                     title: "Erreur !",
    //                     text: errorMessage,
    //                     icon: "error",
    //                     confirmButtonText: "OK"
    //                 });


    //             }
    //         })
    //         .catch(error => {
    //             console.error('Error:', error);
    //         });
    // }
</script>
@endpush