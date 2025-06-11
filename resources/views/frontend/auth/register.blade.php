<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Création de compte - Mini E-commerce</title>
    <link href="{{ asset('assets/css/register.css') }}" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">

</head>

<body>
    <div class="register-container">
        <h1>Création de compte</h1>

        <div id="successMessage" class="success-message" style="display:none;">
            Un email de confirmation a été envoyé à votre adresse. Veuillez vérifier votre boîte mail.
        </div>

        <form id="registerForm">
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
            <a href="login.html">Déjà un compte ? Se connecter</a>
        </div>
    </div>
    <!-- Ajoutez dans votre head -->
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

                        // Mettre à jour le drapeau dans le select
                        selectedFlag.innerHTML = `<img src="${flagUrl}" alt="Flag" class="flag-icon">`;

                        // Mettre à jour le drapeau et le préfixe téléphonique
                        phonePrefixFlag.innerHTML = `<img src="${flagUrl}" alt="Flag" class="flag-icon">`;
                        phonePrefix.value = phoneCode;

                        // Mettre à jour l'exemple de format
                        updatePhoneFormatHint(selectedOption.value);
                    });

                    // Détection automatique du pays
                    detectUserCountry();
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    loadBackupCountries();
                });
        });

        // Fonction pour mettre à jour l'exemple de format
        function updatePhoneFormatHint(countryCode) {
            const phoneFormatHint = document.getElementById('phoneFormatHint');
            const examples = {
                'FR': 'Ex: 612345678 (9 chiffres)',
                'BE': 'Ex: 471234567 (9 chiffres)',
                'US': 'Ex: 2015550123 (10 chiffres)',
                // Ajouter d'autres pays au besoin
            };
            phoneFormatHint.textContent = examples[countryCode] || 'Veuillez entrer un numéro valide pour ce pays';
        }

        // Modifier la fonction detectUserCountry pour mettre à jour les drapeaux
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

        // Modifier loadBackupCountries pour inclure les drapeaux
        function loadBackupCountries() {
            const backupCountries = [{
                    cca2: 'FR',
                    name: {
                        common: 'France'
                    },
                    idd: {
                        root: '+3',
                        suffixes: ['3']
                    },
                    flags: {
                        svg: 'https://flagcdn.com/fr.svg'
                    }
                },
                // Ajouter d'autres pays de la même manière
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

        // Dans votre script existant
        function validatePhoneNumber(phone, countryCode) {
            const cleanedPhone = phone.replace(/[-\s]/g, '');
            const rules = {
                'FR': {
                    pattern: /^(0[1-9])(\d{2}){4}$/,
                    min: 10, // 0 + 9 chiffres
                    max: 10
                },
                'US': {
                    pattern: /^[2-9]\d{9}$/,
                    min: 10,
                    max: 10
                },
                'BE': {
                    pattern: /^(0[1-9])(\d{3}){2}$/,
                    min: 10,
                    max: 10
                }
                // Ajouter d'autres pays
            };

            const rule = rules[countryCode] || {
                pattern: /^\d+$/,
                min: 6,
                max: 15
            };

            return {
                isValid: rule.pattern.test(cleanedPhone),
                isTooShort: cleanedPhone.length < rule.min,
                isTooLong: cleanedPhone.length > rule.max,
                expectedLength: rule.min
            };
        }

        // Écouteur d'événement pour la validation en temps réel
        document.getElementById('phone').addEventListener('input', function() {
            const countryCode = document.getElementById('country').value;
            const phone = this.value;
            const phoneError = document.getElementById('phoneError');

            if (!countryCode) {
                phoneError.style.display = 'block';
                phoneError.textContent = 'Veuillez d\'abord sélectionner un pays';
                return;
            }

            const validation = validatePhoneNumber(phone, countryCode);

            if (validation.isTooShort) {
                phoneError.style.display = 'block';
                phoneError.textContent = `Trop court. Le numéro doit avoir ${validation.expectedLength} chiffres.`;
            } else if (validation.isTooLong) {
                phoneError.style.display = 'block';
                phoneError.textContent = 'Trop long. Vérifiez le format.';
            } else if (!validation.isValid) {
                phoneError.style.display = 'block';
                phoneError.textContent = 'Format invalide pour ce pays.';
            } else {
                phoneError.style.display = 'none';
                // Vérifier l'unicité via API
                checkPhoneUniqueness(phonePrefix.value + phone);
            }
        });

        // Fonction améliorée pour vérifier l'unicité
        function checkPhoneUniqueness(fullPhoneNumber) {
            if (!fullPhoneNumber || fullPhoneNumber.length < 5) return;

            fetch('/check-phone', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        phone: fullPhoneNumber
                    })
                })
                .then(response => response.json())
                .then(data => {
                    const phoneError = document.getElementById('phoneError');
                    if (!data.available) {
                        phoneError.style.display = 'block';
                        phoneError.textContent = 'Ce numéro est déjà utilisé';
                    }
                });
        }

        function getPhoneNumberRules(countryCode) {
            try {
                const phoneNumber = libphonenumber.parsePhoneNumber('+' + Math.random().toString().slice(2, 15), countryCode);
                if (phoneNumber) {
                    return {
                        min: phoneNumber.countryCallingCode.length + 1,
                        max: phoneNumber.countryCallingCode.length + 15
                    };
                }
                return {
                    min: 6,
                    max: 15
                };
            } catch {
                return {
                    min: 6,
                    max: 15
                };
            }
        }

        // Validation simplifiée
        function validatePhoneLength() {
            const countryCode = document.getElementById('country').value;
            const phoneInput = document.getElementById('phone');
            const rules = getPhoneNumberRules(countryCode);

            // Nettoyer le numéro
            const cleanedValue = phoneInput.value.replace(/\D/g, '');

            // Bloquer la saisie si dépasse le max
            if (cleanedValue.length > rules.max) {
                phoneInput.value = cleanedValue.slice(0, rules.max);
                return;
            }

            // Validation visuelle
            const phoneError = document.getElementById('phoneError');
            if (cleanedValue.length < rules.min) {
                phoneError.style.display = 'block';
                phoneError.textContent = `Le numéro doit contenir au moins ${rules.min} chiffres`;
            } else {
                phoneError.style.display = 'none';
            }
        }

        // Modifier validatePhoneLength pour être asynchrone
        async function validatePhoneLength() {
            const countryCode = document.getElementById('country').value;
            const phoneInput = document.getElementById('phone');
            const rules = await getPhoneNumberRules(countryCode);

            // Bloquer la saisie au max
            const cleanedValue = phoneInput.value.replace(/\D/g, '');
            if (cleanedValue.length > rules.max) {
                phoneInput.value = cleanedValue.slice(0, rules.max);
            }

            // Mettre à jour l'affichage
            document.getElementById('maxLength').textContent = rules.max;
            document.getElementById('currentLength').textContent =
                Math.min(cleanedValue.length, rules.max);

            // Style dynamique
            const lengthHint = document.querySelector('.phone-length-hint');
            lengthHint.className = 'phone-length-hint' +
                (cleanedValue.length < rules.min ? ' warning' : '') +
                (cleanedValue.length > rules.max ? ' error' : '');
        }

        // Initialiser libphonenumber
        document.addEventListener('DOMContentLoaded', function() {
            // Écouteurs d'événements
            document.getElementById('country').addEventListener('change', validatePhoneLength);
            document.getElementById('phone').addEventListener('input', validatePhoneLength);
        });
    </script>
</body>

</html>