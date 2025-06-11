<?php

namespace App\Rules;

use libphonenumber\PhoneNumberUtil;
use libphonenumber\NumberParseException;
use Illuminate\Contracts\Validation\Rule;

class PhoneNumber implements Rule
{
    protected $countryField;
    protected $message;

    public function __construct($countryField = 'country')
    {
        $this->countryField = $countryField;
    }

    public function passes($attribute, $value)
    {
        $countryCode = request($this->countryField);
        $phonePrefix = request('phone_prefix'); 

        if (empty($countryCode)) {
            $this->message = 'Le pays est requis pour valider le numéro';
            return false;
        }

        $phoneUtil = PhoneNumberUtil::getInstance();

        try {
            // Combine le préfixe et le numéro
            $fullNumber = $phonePrefix . $value;
            $numberProto = $phoneUtil->parse($fullNumber, $countryCode);

            $isValid = $phoneUtil->isValidNumber($numberProto);
            $regionMatches = $phoneUtil->getRegionCodeForNumber($numberProto) === $countryCode;

            if (!$isValid) {
                $this->message = 'Numéro de téléphone invalide';
            } elseif (!$regionMatches) {
                $this->message = 'Le numéro ne correspond pas au pays sélectionné';
            }

            return $isValid && $regionMatches;
        } catch (NumberParseException $e) {
            $this->message = 'Format de numéro invalide: ' . $e->getMessage();
            return false;
        }
    }

    public function message()
    {
        return $this->message;
    }
}
